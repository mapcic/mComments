<?php 
	$db = JFactory::getDbo();
	$query = "CREATE TABLE IF NOT EXISTS `#__mcomments_0` ( `id` int(11) NOT NULL AUTO_INCREMENT, `email` varchar(255) NOT NULL, `message` mediumtext NOT NULL, `parent` int(11) DEFAULT 0, `utime` int(11) DEFAULT 0, PRIMARY KEY (`id`) );";
	$db->setQuery($query);
	$db->query();
?>