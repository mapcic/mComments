<?php
function getChild($comments, $level, $parentId){
	$out = [];
	foreach ($comments[$level] as $key => $val) {
		if ( $val->parent != $parentId ) {
			continue;
		}

		$out[] = $val;
		$child = getChild($comments, $level+1, $val->id);
		$out = array_merge($out, $child);
	}
	return $out;
}

function sortComment( &$comments ) {
	$commentsByLevel = [];
	foreach ($comments as $key => $val) {
		$commentsByLevel[$val->level][] = $val;	
	}
	
	$levels = array_keys($commentsByLevel);
	$startLevel = min($levels);
	$parent = $commentsByLevel[$startLevel][0]->parent;

	$out = getChild($commentsByLevel, $startLevel, $parent);

	return $out;
}

function loadLast($table, $offset, $num) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn($table))
		->setLimit($num, $offset);
	$last = $db->setQuery($query)->loadObjectList();

	$out = [];
	foreach ($last as $key => $val) {
		$subQuery = $db->getQuery(true)
			->select($db->qn('branchId'))
			->from($db->qn($val->table_name))
			->where($db->qn('id').' = '.$val->mcid);
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn($val->table_name))
			->where($db->qn('branchId').' = ('.$subQuery.')');
		$comments = $db->setQuery($query)->loadObjectList();
		$comments = sortComment($comments);

		$out[] = array(
			'items' => $comments,
			'branchId' => $comments[0]->branchId,
			'table' => $val->table_name,
			'mark' => $val->mcid
		);
	}

	return $out;
}

function loadPage($table, $offset, $num) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn($table))
		->where($db->qn('level').' = 0')
		->order($db->qn('utime').' DESC, '.$db->qn('id').' DESC')
		->setLimit($num, $offset);
	$level0 = $db->setQuery($query)->loadObjectList();

	$branchs = [];
	foreach ($level0 as $key => $val) {
		$query = $db->getQuery(true)
			->select('*')
			->from($table)
			->where($db->qn('branchId').' = '.$val->id)
			->order($db->qn('utime').' ASC, '.$db->qn('id').' ASC');
		$comments = $db->setQuery($query)->loadObjectList();
		$comments = sortComment($comments);

		$branchs = array_merge($branchs, $comments);
	}

	return $branchs;
}

function load() {
	$table = $_POST['table'];
	$num = $_POST['num'];
	$offset = $_POST['offset'];
	$comments = '';

	if ($table == '#__mcomments_last') {
		$comments = loadLast($table, $offset, $num);
	} else {
		$comments = loadPage($table, $offset, $num);
	}

	$out = array(
		'items' => $comments
	);

	echo(json_encode($out));
}

function remove() {
	$table = $_POST['table'];
	$id = $_POST['id'];
	$branchId = $_POST['branchId'];

	$db = JFactory::getDbo();
	if ($table == '#__mcomments_last') {
		$query = $db->getQuery(true)
			->select($db->qn('table_name'))
			->from($db->qn($table));
		$tablePage = $db->setQuery($query)->loadResult();
	} else {
		$tablePage = $table;
	}

	$subQuery = $db->getQuery(true)
			->select($db->qn('level'))
			->from($db->qn($tablePage))
			->where($db->qn('id').' = '.$id);
	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn($tablePage))
		->where($db->qn('branchId').' = '.$branchId
			.' AND '.
			$db->qn('level').' > ('.$subQuery.')');
	$branch = $db->setQuery($query)->loadObjectList();

	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn($tablePage))
		->where($db->qn('id').' = '.$id);
	$root = $db->setQuery($query)->loadObjectList();

	$num = ($root->level == 0)? 1 : 0;
	
	$branch = array_merge($root, $branch);
	$branch = sortComment($branch);

	$ids = [];
	foreach ($branch as $key => $val) {
		$ids[] = $val->id;
	}

	$query = $db->getQuery(true)
		->delete($db->qn($tablePage))
		->where(array(
			$db->qn('id').' IN ('.implode(',', $ids).')'
		));
	$db->setQuery($query)->execute();

	$query = $db->getQuery(true)
		->delete($db->qn('#__mcomments_last'))
		->where(array(
			$db->qn('mcid').' IN ('.implode(',', $ids).')',
			$db->qn('table_name').' = '.$db->q($tablePage)
		));
	$db->setQuery($query)->execute();

	$out = array(
		'ids' => $ids,
		'num' => $num
	);

	echo(json_encode($out));
}

function info() {
	$table = $_POST['table'];

	$db = JFactory::getDbo();
	$query = $db->getQuery(true)
		->select('COUNT('.$db->quoteName('id').')')
		->from($db->qn($table));
		
	if ($table != '#__mcomments_last') {
		$query->where($db->qn('level').' = 0');
	}

	$len = $db->setQuery($query)->loadResult();

	$out = array(
		'len' => $len
	);

	echo(json_encode($out)); 
}

function add() {
	$email = $_POST['email'];
	$level = $_POST['level'];
	$msg = $_POST['msg'];
	$parent = $_POST['parentId'];
	$branchId = $_POST['branchId'];
	$table = $_POST['table'];

	$db = JFactory::getDbo();

	if ( !preg_match('/'.$db->getPrefix().'mcomments_.+/', $table) ) {
		return 0;
	}

	$comment = (object) array(
		'email' => $email,
		'message' => $msg,
		'parent' => $parent,
		'branchId' => $branchId,
		'level' => $level,
		'utime' => date('U')
	);

	$id = 0;
	$db->insertObject($table, $comment);

	$comment->id = (int)$db->insertid();
	if ($comment->level == 0) {
		$comment->branchId = $comment->id;
		$db->updateObject($table, $comment, 'id');
	}

	$lastComment = (object) array(
		'mcid' => $comment->id,
		'table_name' => $table
	);
	$db->insertObject('#__mcomments_last', $lastComment);

	echo json_encode($comment);
}

function initJoomlaApi() {
	define('_JEXEC', 1); define('DS', DIRECTORY_SEPARATOR);
	define('JPATH_BASE', preg_replace('/(?:\/[\w\-]+){3}$/', '', dirname(__FILE__)));

	require_once (JPATH_BASE .DS.'includes'.DS.'defines.php');
	require_once (JPATH_BASE .DS.'includes'.DS.'framework.php');

	return 1;
}

$nameFunc = $_POST['method' ];
if ( in_array($nameFunc, array('load', 'info', 'remove', 'add')) ) {
	initJoomlaApi();
	$nameFunc();
}
?>