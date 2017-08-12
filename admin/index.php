<?php
function initJoomlaApi() {
	define('_JEXEC', 1); define('DS', DIRECTORY_SEPARATOR);
	define('JPATH_BASE', preg_replace('/(?:\/[\w\-]+){3}$/', '', dirname(__FILE__)));

	require_once (JPATH_BASE .DS.'includes'.DS.'defines.php');
	require_once (JPATH_BASE .DS.'includes'.DS.'framework.php');

	return 1;
}

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

	$out = getChild($commentsByLevel, 0, 0);

	return $out;
}

function loadLast($table, $offset, $num) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn($table))
		->setLimit($num, $offset);
	$last = $db->setQuery($query)->loadObjectList();

	$branchIds = [];
	$branchs = [];
	foreach ($last as $key => $val) {
		if (in_array($val->branchId, $branchIds)) {
			continue;
		}
		$branchIds[] = $val->branchId;

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

		$branchs[] = $comments;
	}

	return $branchs;
}

function loadPage($table, $offset, $num) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn($table))
		->where($db->qn('level').' = 0')
		->setLimit($num, $offset);
	$level0 = $db->setQuery($query)->loadObjectList();

	$branchs = [];
	foreach ($level0 as $key => $val) {
		$query = $db->getQuery(true)
			->select('*')
			->from($table)
			->where($db->qn('branchId').' = '.$val->id);
		$comments = $db->setQuery($query)->loadObjectList();
		$comments = sortComment($comments);

		$branchs[] = $comments;
	}

	return $branchs;
}

function load() {
	$table = $_POST['table'];
	$num = $_POST['num'];
	$offset = $_POST['offset'];

	if ($table == '#__mcomments_last') {
		$comments = loadLast($table, $offset, $num);
	} else {
		$comments = loadPage($table, $offset, $num);
	}

	$out = array(
		'comments' => $comments
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
		->from($db->qn($table))
		->where($db->qn('branchId').' = '.$branchId
			.' AND '.
			$db->qn('level').' > ('.$subQuery.')');
	$branch = $db->setQuery($query)->loadObjectList();

	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn($tablePage))
		->where($db->qn('id').' = '.$id);
	$root = $db->setQuery($query)->loadObjectList();

	$num = 0;
	if ($root->level == 0) {
		$num = 1;
	}

	$branch = array_merge($root, $branch);
	$branch = sortComment($branch);

	ids = [];
	foreach ($branch as $key => $val) {
		$ids[] = $val->id;
	}

	$query = $db->getQuery(true)
		->delete($db->qn($tablePage))
		->where($db->qn('id').' IN ('.implode(',', ids));
	$db->setQuery($query)->execute();

	$query = $db->getQuery(true)
		->delete($db->qn('#__mcomments_last'))
		->where($db->qn('id').' IN ('.implode(',', ids));
	$db->setQuery($query)->execute();

	$out = array(
		'ids' => $ids,
		'num' => $num
	);

	echo(json_encode($out));
}

$nameFunc = json_decode( $_POST[ 'params' ] )->method;
if ($nameFunc in array('load', 'info', 'remove')) {
	initJoomlaApi();
	$nameFunc();
}
?>