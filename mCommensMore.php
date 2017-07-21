<?php
define('_JEXEC', 1); define('DS', DIRECTORY_SEPARATOR);    
define('JPATH_BASE', preg_replace('/(?:\/[\w\-]+){3}$/', '', dirname(__FILE__)));

require_once (JPATH_BASE .DS.'includes'.DS.'defines.php');
require_once (JPATH_BASE .DS.'includes'.DS.'framework.php');

function getChild(&$arr, $num, $id) {
	$out = array();
	$key = array();

	if ($num > count($arr)){
		return $out;
	}

	foreach ($arr[$num] as $key => $val) {
		if ( $val->parent == $id ) {
			$out[] = $val;
			$del[] = $key;
		}
	}

	foreach ($del as $key => $val) {
		unset($arr[$num][$val]);
	}

	return $out;
}

function sortComments(&$arr, $level=0) {
	$out = array();
	foreach ($arr as $key => $val) {
		$child = getChild($commentsByLevel, $val->level + 1, $val->id);
		if (empty($child)) {
			$out[] = $val;
		} else {
			$out[] = array_merge($out, sortComments($arr, $val->level + 1));
		}
	}
	return $out;
}

$offset = $_POST['num'];
$len = $_POST['len'];
$from = $_POST['table'];
$num = 20;

$db = JFactory::getDbo();
$query = $db->getQuery(true)
	->select('*')
	->from($db->qn($from))
	->where($db->qn('state').' = 1 AND '.$db->qn('level').' = 0')
	->order($db->qn('utime').' DESC')
	->setLimit($num, $offset);
$comments0 = $db->setQuery($query)
	->loadObjectList();

$query = $db->getQuery(true)
	->select('*')
	->from($db->qn($from))
	->where($db->qn('state').' = 1 AND '.$db->qn('level').' <> 0')
	->order($db->qn('utime').' DESC');
$comments = $db->setQuery($query)
	->loadObjectList();

$commentsByLevel = array();
foreach ($comments as $key => $val) {
	$commentsByLevel[$val->level][] = $val;	
}

$out = (object) array(
	'num' => $offset + count($comments0),
	'len' => $len,
	'items' => sortComments(array_merge($comments0, $commentsByLevel))
);
echo json_encode($out);
?>