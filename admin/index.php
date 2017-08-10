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

function get() {
	$table = $_POST['table'];
	$num = $_POST['num'];
	$offset = $_POST['offset'];

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

	echo $branchs;
}

$nameFunc = json_decode( $_POST[ 'params' ] )->method;
if ($nameFunc in array('get')) {
	initJoomlaApi();
	$nameFunc();
}
?>