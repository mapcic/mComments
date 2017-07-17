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
		$out = 	'<div class="mcComments">
				<div class="mcLevel'.$val->$num.'" mcid="'.$val->id.'">
					<div class="mcEmail">'.$val->email.'</div>
					<div class="mcMessаge">'.$val->message.'</div>
					<div class="mcAnswer">Ответить</div>
				</div>
			</div>';
		$out = $out.printChild($commentsByLevel, $num+1, $val->id);
		$out = $out.'<div class="mcLevel'.$num.'" menu="'.$val->id.'"><a class="menuNodeTitle" href="/'.$val->path.'">'.$val->title.'</a></div>'.printChild($arr, $num+1, $val->id);
	}

	return $out;
}

function getComments( $path = null ){
	$db = JFactory::getDbo();
	$out = array();

	$query = $db->getQuery(true)
		->select($db->qn('table_name'))
		->from($db->qn('#__mcommetns_ids'))
		->where($db->qn('path').' = '.$path);
	$from = $db->setQuery($query)
		->loadResult();

	$query = $db->getQuery(true)
		->select('*')
		->from($from)
		->where($db->qn('state').' = 1')
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

	$out = '<div class="ShliambOff" table="'.$from.'"></div>'
	foreach ($commentsByLevel[0] as $key => $val) {
		$out = 	'<div class="mcComments">
				<div class="mcLevel0" mcid="'.$val->id.'">
					<div class="mcEmail">'.$val->email.'</div>
					<div class="mcMessаge">'.$val->message.'</div>
					<div class="mcAnswer">Ответить</div>
				</div>
			</div>';
		$out = $out.printChild($commentsByLevel, 1, $val->id);
		echo $out;
	}
	return 1;
}
?>