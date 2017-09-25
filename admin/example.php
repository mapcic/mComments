{source}
<style type="text/css">
	.ShliambOff {
		display: none;
	}
	.mcLevel0 {
		background-color: darkgrey;
		margin-top: 10px;
	}
	.mcLevel1 {
		background-color: darkgrey;
		margin-top: 2px;
		margin-left: 40px;
	}	
	.mcLevel2 {
		background-color: darkgrey;
		margin-top: 2px;
		margin-left: 60px;
	}
	.mcLevel3 {
		background-color: darkgrey;
		margin-top: 2px;
		margin-left: 120px;
	}
	.mcLevel4 {
		background-color: darkgrey;
		margin-top: 2px;
		margin-left: 160px;
	}
	.lastComment {
		background: cadetblue !important;
	}
</style>

<script type="text/javascript" onload="mComments();" src="/templates/protostar/js/mCommentsAdmin.js" defer></script>

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
{/source}