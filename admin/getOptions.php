<?php 
function getOptions() {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true)
		->select($db->qn(array('table_name', 'path')))
		->from($db->qn("#__mcomments_ids"));
	$options = $db->setQuery($query)->loadObjectList();

	foreach ($options as $val) {
		$optionsHTML = $optionsHTML.'<option value="'.$val->table_name.'">'.$val->path.'</option>';
	}

	return $optionsHTML;
}
?>