{source}
<style type="text/css">
	.ShliambOff {
		display: none;
	}
</style>

<script type="text/javascript">
function mComments() {
	jQuery('.mcButton').on('click', addCommet_mc);
	jQuery('.mcAnswer').on('click', addCommetFloatForm_mc);
	jQuery('.mcMore').each(function(ind, val){
		var $this = jQuery(this),
			info = jQuery(this).parents('.mComments').find('.mcTable'),
			table = info.attr('table'),
			num = info.attr('num'),
			len = info.attr('len');

		$this.attr('num', num).attr('len', len).attr('table', table);

		if ( +num < +len) {
			$this.removeClass('ShliambOff').on('click', moreComments_mc);
		}
	});
}

function addCommet_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		table = mc.find('.mcTable').attr('table'),
		comment = $this.parents('.mcForm'),
		parent = comment.attr('mcid'),
		level = comment.attr('level'),
		email = comment.find('.mcEmail'),
		msg = comment.find('.mcTextarea');

	if ( !isMsg(msg.val()) || !isEmail(email.val()) ) {
		console.log('empty')
		return 0;
	}

	jQuery.ajax({
		type : 'POST', url : '/templates/protostar/php/mCommentsAdd.php', dataType: 'json', 
		data: { email : email.val(), msg : msg.val(), parent : parent, table: table, level: level },
		success: function( data ) {
			console.log(data);
			var div = '<div class="mcComment mcLevel'+data.level+'" mcid="'+data.id+'" level="'+data.level+'">' +
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
				comment.addClass('ShliambOff');
			}
		}
	});
}

function addCommetFloatForm_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		comment = $this.parents('.mcComment'),
		parent = comment.attr('mcid'),
		level = comment.attr('level'),
		floatForm = mc.find('.mcFormFloat');

	comment.after(floatForm);
	floatForm.attr('mcid', parent).attr('level', +level+1).removeClass('ShliambOff');
}

function moreComments_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		table = $this.attr('table')
		len = $this.attr('len'),
		num = $this.attr('num');

	jQuery.ajax({
		type : 'POST', url : '/templates/protostar/php/mCommentsMore.php', dataType: 'json', 
		data: { len: len, num: num, table: table },
		success: function( data ) {
			$this.attr('len', data.len).attr('num', data.num);
			if (data.num >= data.len) {
				$this.addClass('ShliambOff').off('click', moreComments_mc);
			}

			jQuery.each(data.items, function(ind, val) {
				var div = '<div class="mcComment mcLevel'+val.level+'" mcid="'+val.id+'" level="'+val.level+'">' +
							'<div class="mcEmail">'+val.email+'</div>' +
							'<div class="mcTime">'+val.utime+'</div>' +
							'<div class="mcMessаge">'+val.message+'</div>' +
							'<div class="mcAnswer">Ответить</div>' +
						'</div>',
					comments = $this.parents('.mComments').find('.mcComments');
				jQuery(div).appendTo(comments).find('.mcAnswer').on('click', addCommetFloatForm_mc);
			});
		}
	});
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
	$num = 2;

	$path = urldecode((JFactory::getURI())->getPath()); 

	$query = $db->getQuery(true)
		->select($db->qn('table_name'))
		->from($db->qn('#__mcomments_ids'))
		->where($db->qn('path').' = '.$db->q($path));
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
		$out = '<div class="mcComment mcLevel0" mcid="'.$val->id.'" level="0">
					<div class="mcEmail">'.$val->email.'</div>
					<div class="mcMessаge">'.$val->message.'</div>
					<div class="mcAnswer">Ответить</div>
				</div>';
		$out = $out.printChild($commentsByLevel, 1, $val->id);
		echo $out;
	}

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
	<div class="mcMore ShliambOff" len="" num="">Еще</div>
</div>
{/source}