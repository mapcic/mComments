<?php 
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

function printChild(&$arr, $num, $id) {
	$out = '';
	$child = getChild($arr, $num, $id);

	if (empty($child)) {
		return $out;	
	}
	
	foreach ($child as $key => $val) {
		$out = $out.'<div class="mcLevel'.$val->level.'" mcid="'.$val->id.'">
					<div class="mcEmail">'.$val->email.'</div>
					<div class="mcMessаge">'.$val->message.'</div>
					<div class="mcAnswer">Ответить</div>
				</div>';
		$out = $out.printChild($arr, $num+1, $val->id);
	}
	return $out;
}

function getComments( $path = null ){
	$db = JFactory::getDbo();
	$out = array();

	$path = urldecode((JFactory::getURI())->getPath()); 

	$query = $db->getQuery(true)
		->select($db->qn('table_name'))
		->from($db->qn('#__mcomments_ids'))
		->where($db->qn('path').' = '.$db->q($path));
	$from = $db->setQuery($query)
		->loadResult();

	$query = $db->getQuery(true)
		->select('*')
		->from($from)
		->where($db->qn('state').' = 1 AND '.$db->qn('level').' = 0')
		->order($db->qn('utime').' DESC')
		->setLimit(20);
	$comments0 = $db->setQuery($query)
		->loadObjectList();

	$ids = array();
	foreach ($comments0 as $key => $val) {
		$ids[] = $val->id;	
	}

	$query = $db->getQuery(true)
		->select('*')
		->from($from)
		->where($db->qn('state').' = 1 AND '.$db->qn('level').' <> 0')
		->order($db->qn('utime').' DESC');
	$comments = $db->setQuery($query)
		->loadObjectList();

	if (empty($comments)) {
		return 1;
	}

	$commentsByLevel = array();
	foreach ($comments as $key => $val) {
		$commentsByLevel[$val->level][] = $val;	
	}

	foreach ($comments0 as $key => $val) {
		$out = '<div class="mcLevel0" mcid="'.$val->id.'">
					<div class="mcEmail">'.$val->email.'</div>
					<div class="mcMessаge">'.$val->message.'</div>
					<div class="mcAnswer">Ответить</div>
				</div>';
		$out = $out.printChild($commentsByLevel, 1, $val->id);
		echo $out;
	}

	echo '<div class="ShliambOff mcTable" table="'.$from.'"></div>';
	return 1;
}
?>