{source}
<style type="text/css">
	.ShliambOff {
		display: none;
	}
	.mcLevel0 {
		background-color: green;
		margin-top: 5px;
	}
	.mcLevel1 {
		background-color: orange;
		margin-left: 20px;
	}	
	.mcLevel2 {
		background-color: red;
		margin-left: 40px;
	}
	.mcLevel3 {
		background-color: blue;
		margin-left: 60px;
	}
</style>

<script type="text/javascript" src="/templates/protostar/js/mCommnetsAdmin"></script>

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
		<div class="mcInfo ShliambOff" offset="0" num="5" table="#__mcomments_last"></div>

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