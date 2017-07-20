{source}
<script type="text/javascript">
function mComments() {
	jQuery('.mcButton').on('click', addCommet_mc);
	// jQuery('.mcMore').on('click', addCommets_mc);
	jQuery('.mcAnswer').on('click', addCommetForm_mc);
}

function addCommet_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		table = mc.find('.mcTable').attr('table'),
		comment = $this.parents('.mcForm'),
		parent = comment.attr('mcid'),
		email = comment.find('.mcEmail'),
		level = comment.attr('level'),
		msg = comment.find('.mcTextarea');

	console.log(msg.val());
	console.log(email.val());
	console.log(table);
	console.log(parent);

	
	if ( !isMsg(msg.val()) || !isEmail(email.val()) ) {
		console.log('empty')
		return 0;
	}

	jQuery.ajax({
		type : 'POST', url : '/templates/protostar/php/mCommentsAdd.php', dataType: 'json', 
		data: { email : email.val(), msg : msg.val(), parent : parent, table: table, level: level },
		success: function( data ) {
	console.log('goood')
	console.log(data);
	console.log(data.message);
	console.log(data.email);
	console.log(data.id);
			var div = '<div class="mcComment mcLevel'+data.level+'" mcid="'+data.id+'">' +
						'<div class="mcEmail">'+data.email+'</div>' +
						'<div class="mcTime">'+data.utime+'</div>' +
						'<div class="mcMessаge">'+data.message+'</div>' +
						'<div class="mcAnswer">Ответить</div>' +
					'</div>';
			
			jQuery(div).insertAfter(comment);
			
			email.val('');
			msg.val('');
			comment.attr('mcid', 0);

			if (comment.hasClass('mcFormFloat')) {
				comment.addClass('ShliamOff');
			}
		}
	});
}

// late
// function addCommets_mc(event) {
// 	event.preventDefault();
// }

function addCommetForm_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		comment = $this.parent(),
		parent = comment.attr('mcid'),
		floatForm = mc.find('.mcFormFloat');

	comment.insertAfter(floatForm);
	floatForm.after(comment).attr('mcid', parent).removeClass('ShliambOff');
}

function clearString( str ) {
    return str.replace(/^\s+|\s+$/g,''); 
}

function isEmail( email ) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function isMsg( msg ) {
	if(clearString(msg) == '') {
		return false;
	}
	return true;
}

jQuery(document).ready(function(){
	mComments();
});
</script>



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

		$query = 'CREATE TABLE IF NOT EXISTS `'.$page->table_name.'` ( `id` int(11) NOT NULL AUTO_INCREMENT, `email` varchar(255) NOT NULL, `message` mediumtext NOT NULL, `parent` int(11) DEFAULT 0, `utime` int(11) DEFAULT 0, `level` int(11) DEFAULT 0, `state` int(11) DEFAULT 1, PRIMARY KEY (`id`) );';
		$db->setQuery($query)
			->query();
	}
}

mCommetntsInit();
?>

<style>
	.ShliambOff {
		display : none;
	}
</style>

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
		$out = $out.'<div class="mcComment mcLevel'.$val->level.'" mcid="'.$val->id.'" level="'.$val->level.'">
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
		$out = '<div class="mcComment mcLevel0" mcid="'.$val->id.'" level="0">
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

<div class="mComments">
	<div class="mcForm" mcid="0" level="0">
		<div class="mcFormText">SomeText</div>
		<input type="text" name="email" class="mcEmail">
		<textarea class="mcTextarea"></textarea>
		<div class="mcButton">Отправить</div>
	</div>
	<div class="mcForm mcFormFloat ShliambOff" mcid="" level="">
		<div class="mcFormText">SomeText</div>
		<input type="text" name="email" class="mcEmail">
		<textarea class="mcTextarea"></textarea>
		<div class="mcButton">Отправить</div>
	</div>
	<div class="mcComments"><?php
		getComments();	
	?></div>
	<!-- <div class="mcMore"></div> -->
</div>
{/source}