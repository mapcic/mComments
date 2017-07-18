<?php
function mCommetntsInit(){
	$db = JFactory::getDbo();

	$query = $db->getQuery(true)
		->select(array('id', 'path', 'home'))
		->from($db->qn('#__menu'))
		->where($db->qn('published').' = 1 AND '.$db->qn('link').' LIKE "%option=com_content%"');
	$pages = $db->setQuery($query)
		->loadObjectList();

	foreach ($pages as $key => $val) {
		$page = (object) array(
			'table_name' => '#__mcomments_'.$val->id,
			'path' => $val->path,
			'home' => $val->home
		);

		$query = $db->getQuery('true')
			->select($db->qn('id'))
			->from($db->qn('#__mcomments_ids'))
			->where($db->qn('path').' = '.$page->path);
		$resp = $db->setQuery($query)
			->loadResult();

		if (empty($resp)) {
			return 1;
		}

		if (!$db->insertObject('#__mcomments_ids', $page)) {
			return -1;
		}

		$query = 'CREATE TABLE IF NOT EXISTS `'.$page->table_name.'` ( `id` int(11) NOT NULL AUTO_INCREMENT, `email` varchar(255) NOT NULL, `message` mediumtext NOT NULL, `parent` int(11) DEFAULT 0, `utime` int(11) DEFAULT 0, PRIMARY KEY (`id`) );';
		$db->setQuery($query)
			->query();
	}
}
?>