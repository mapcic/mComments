{source}
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
		$out = $out.'<div class="mcComment mcLevel'.$val->level.'" mcid="'.$val->id.'" level="'.$val->level.'" branchid="'.$val->branchId.'">
					<div class="mcEmail">'.$val->email.'</div>
					<div class="mcTime">'.date('G:i:s d.m.Y',strtotime($val->utime)).'</div>
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
	$num = 2;

	$path = urldecode((JFactory::getURI())->getPath()); 
	$path = ($path != '/')? $path : '/home';

	$altPath = (substr($path, -1) == '/')? substr($path, 0, -1) : $path.'/';


	$query = $db->getQuery(true)
		->select($db->qn('table_name'))
		->from($db->qn('#__mcomments_ids'))
		->where(
			$db->qn('path').' = '.$db->q($path)
			.'OR'.
			$db->qn('path').' = '.$db->q($altPath));
	$from = $db->setQuery($query)
		->loadResult();

	$query = $db->getQuery(true)
		->select('*')
		->from($db->qn($from))
		->where($db->qn('state').' = 1 AND '.$db->qn('level').' = 0')
		->order($db->qn('utime').' DESC')
		->setLimit($num);
	$comments0 = $db->setQuery($query)
		->loadObjectList();

	$query = $db->getQuery(true)
        ->select('COUNT('.$db->qn('id').')')
        ->from($db->qn($from))
		->where($db->qn('state').' = 1 AND '.$db->qn('level').' = 0');
    $db->setQuery($query);   
	$len = $db->loadResult();

	echo '<div class="ShliambOff mcTable" table="'.$from.'" num="'.count($comments0).'" len="'.$len.'"></div>';

	$query = $db->getQuery(true)
		->select('*')
		->from($from)
		->where($db->qn('state').' = 1 AND '.$db->qn('level').' <> 0')
		->order($db->qn('utime').' DESC');
	$comments = $db->setQuery($query)
		->loadObjectList();

	if (empty($comments0)) {
		return 1;
	}

	$commentsByLevel = array();
	foreach ($comments as $key => $val) {
		$commentsByLevel[$val->level][] = $val;	
	}

	foreach ($comments0 as $key => $val) {
		$out = '<div class="mcComment mcLevel0" mcid="'.$val->id.'" level="0" branchid="'.$val->branchId.'">
					<div class="mcEmail">'.$val->email.'</div>
					<div class="mcTime">'.date('G:i:s d.m.Y', strtotime($val->utime)).'</div>
					<div class="mcMessаge">'.$val->message.'</div>
					<div class="mcAnswer">Ответить</div>
				</div>';
		$out = $out.printChild($commentsByLevel, 1, $val->id);
		echo $out;
	}

	return 1;
}
?>

<script onload="jQuery(document).ready(function(){mComments();});" type="text/javascript" src="path/to/mComments.js"></script>

<div class="mComments">
	<div class="mcForm" mcid="0" level="0" branchId="0">
		<div class="mcFormText">SomeText</div>
		<input type="text" name="email" class="mcEmail">
		<textarea class="mcTextarea"></textarea>
		<div class="mcButton">Отправить</div>
	</div>
	<div class="mcForm mcFormFloat ShliambOff" mcid="" level="" branchId="">
		<div class="mcFormText">SomeText</div>
		<input type="text" name="email" class="mcEmail">
		<textarea class="mcTextarea"></textarea>
		<div class="mcButton">Отправить</div>
	</div>
	<div class="mcComments"><?php
		getComments();	
	?></div>
	<div class="mcMore ShliambOff" len="" num="">Еще</div>
</div>
{/source}