{source}


<?php
function mCommetntsInit(){
	$db = JFactory::getDbo();

	$query = $db->getQuery(true)
		->select(array('id', 'path', 'home'))
		->from($db->qn('#__menu'))
		->where($db->qn('published').' = 1 AND '.$db->qn('link').' LIKE "%option=com_content%"');
	$pages = $db->setQuery($query)
		->loadObjectList();

	$query = 'CREATE TABLE IF NOT EXISTS `#__mcomments_ids` ( `id` int(11) NOT NULL AUTO_INCREMENT, `home` int(1) DEFAULT 0, `path` varchar(255) NOT NULL, `table_name` varchar(255) NOT NULL, PRIMARY KEY (`id`) );';
	$db->setQuery($query)
		->query();

	$query = 'CREATE TABLE IF NOT EXISTS `#__mcomments_last` ( `id` int(11) NOT NULL AUTO_INCREMENT, `mcid` int(11) NOT NULL, `table_name` varchar(255) NOT NULL, PRIMARY KEY (`id`) );';
	$db->setQuery($query)
		->query();
		
	foreach ($pages as $key => $val) {
		$page = (object) array(
			'table_name' => $db->getPrefix().'mcomments_'.$val->id,
			'path' => '/'.$val->path,
			'home' => $val->home
		);

		$query = $db->getQuery('true')
			->select($db->qn('id'))
			->from($db->qn('#__mcomments_ids'))
			->where($db->qn('path').' = "'.$page->path.'"');
		$resp = $db->setQuery($query)
			->loadResult();

		if (!empty($resp)) {
			continue;
		}

		$db->insertObject('#__mcomments_ids', $page);

		$query = 'CREATE TABLE IF NOT EXISTS `'.$page->table_name.'` ( `id` int(11) NOT NULL AUTO_INCREMENT, `email` varchar(255) NOT NULL, `message` mediumtext NOT NULL, `parent` int(11) DEFAULT 0, `branchId` int(11) DEFAULT 0, `utime` int(11) DEFAULT 0, `level` int(11) DEFAULT 0, `state` int(11) DEFAULT 1, PRIMARY KEY (`id`) );';
		$db->setQuery($query)
			->query();
	}
}

function mCommetntsDest() {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true)
		->select($db->qn('table_name'))
		->from($db->qn('#__mcomments_ids'));
	$tables = $db->setQuery($query)->loadObjectList();

	foreach ($tables as $key => $val) {
		$query = 'DROP TABLE IF EXISTS '.$db->qn($val->table_name);
		$db->setQuery($db->replacePrefix($query))->query();
	}

	$query = 'DROP TABLE IF EXISTS '.$db->replacePrefix($db->qn('#__mcomments_ids'));
	$db->setQuery($query)->query();

	$query = 'DROP TABLE IF EXISTS '.$db->replacePrefix($db->qn('#__mcomments_last'));
	$db->setQuery($query)->query();
}

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

mCommetntsInit();
// mCommetntsDest();
?>

<div id="mCommentsAdmin">

	<div id="mcLast" class="mComments">
		<div class="mcInfo ShliambOff" offset="0" num="3" table="#__mcomments_last"></div>

		<div class="mcHead"></div>

		<div class="mcForm mcFormFloat ShliambOff" mcid="" level="" branchId="" tid="">
			<div class="mcFormText">SomeText</div>
			<input type="text" name="email" class="mcEmail">
			<textarea class="mcTextarea"></textarea>
			<div class="mcButton">Отправить</div>
		</div>

		<div class="mcComments"></div>

		<div class="mcMore ShliambOff">Еще</div>
	</div>


	<div id="mcPage" class="mComments">
		<div class="mcInfo ShliambOff" offset="0" num="5" table=""></div>

		<div class="mcHead"></div>

		<div class="mcTables"><select class="mcTables"><?php
			echo getOptions();
		?></select></div>

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

		<div class="mcComments"></div>

		<div class="mcMore ShliambOff">Еще</div>
	</div>	
</div>

<script type="text/javascript" onload="mComments();" src="/templates/protostar/js/mCommentsAdmin.js" defer></script>
{/source}