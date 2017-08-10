{source}
<style>
	.ShliambOff {
		display: none;
	}
</style>
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
		<div class="mcInfo ShliambOff" offset="0" num="20" table="#__mcomments_last"></div>
		<div class="mcHead"></div>
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
		<div class="mcComments">
			<div class="mcComment mcLevel0 mcCommentLast" mcid="" branchId="">
				<div class="mcEmail"></div>
				<div class="mcTime"></div>
				<div class="mcMessаge"></div>
				<div class="mcAnswer">Ответить</div>
			</div>
		</div>
	</div>
	<div id="mcPage" class="mComments">
		<div class="mcInfo ShliambOff" offset="0" num="20" table=""></div>
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
	</div>	
</div>
{/source}