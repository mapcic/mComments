function mComments() {
	// jQuery('.mcButton').on('click', addCommet_mc);
	// jQuery('.mcAnswer').on('click', addCommetFloatForm_mc);
	// jQuery('.mcMore').each(function(ind, val){
	// 	var $this = jQuery(this),
	// 		info = jQuery(this).parents('.mComments').find('.mcInfo'),
	// 		table = info.attr('table'),
	// 		num = info.attr('num'),
	// 		len = info.attr('len');

	// 	$this.attr('num', num).attr('len', len).attr('table', table);

	// 	if ( +num < +len) {
	// 		$this.removeClass('ShliambOff').on('click', moreComments_mc);
	// 	}
	// });
	jQuery('#mcLast .mcMore').on('click', load_mc).trigger('click');
	jQuery('#mcPage select.mcTables').on('change', changeTable_mc);
}

function changeTable_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		selected = $this.val();
	console.log(selected);
	console.log(1);
}

function addCommet_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		info = mc.find('.mcInfo'),
		table = info.attr('table'),
		comment = $this.parents('.mcForm'),
		parent = comment.attr('mcid'),
		branchId = comment.attr('branchId'),
		level = comment.attr('level'),
		email = comment.find('.mcEmail'),
		msg = comment.find('.mcTextarea');

	if ( !isMsg(msg.val()) || !isEmail(email.val()) ) {
		return 0;
	}

	jQuery.ajax({
		type : 'POST', url : '/templates/protostar/php/mCommentsAdd.php', dataType: 'json', 
		data: { email : email.val(), msg : msg.val(), branchId: branchId, parent : parent, table: table, level: level },
		success: function( data ) {
			var div = '<div class="mcComment mcLevel'+data.level+'" mcid="'+data.id+'" level="'+data.level+'" branchId="'+ data.branchId +'">'  +
						'<div class="mcEmail">'+data.email+'</div>' +
						'<div class="mcTime">'+data.utime+'</div>' +
						'<div class="mcMessаge">'+data.message+'</div>' +
						'<div class="mcAnswer">Ответить</div>' +
					'</div>';
			
			jQuery(div).insertAfter(comment);
			
			email.val('');
			msg.val('');
			comment.attr('mcid', 0).attr('branchId', 0);

			if (comment.hasClass('mcFormFloat')) {
				comment.addClass('ShliambOff');
			}

			if (data.level == 0) {
				var more = mc.find('.mcMore');
				more.attr('len', +more.attr('len')+1);
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
		branchId = comment.attr('branchId'),
		level = comment.attr('level'),
		floatForm = mc.find('.mcFormFloat');

	if (+branchId == 0) {
		branchId = parent;
	}

	comment.after(floatForm);
	floatForm.attr('mcid', parent).attr('branchId', branchId).attr('level', +level+1).removeClass('ShliambOff');
}


function load_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		node = $this.parents('.mComments'),
		info = node.find('.mcInfo');

	jQuery.ajax(
		type : 'POST', url : '/templates/protostar/php/mCommentsAdmin.php', dataType: 'json', 
		data: { offset: info.attr('offset'), num: info.attr('num'), table: info.attr('table'), method: 'get' },
		success: function(data) {
			// info.attr('offset', data.offset).attr('len', data.len);
			if (data.offset >= data.len) {
				$this.addClass('ShliambOff').off('click', load_mc);
			}

			jQuery.each(data.items, function(ind, val) {
				var div = '<div class="mcComment mcLevel'+val.level+'" mcid="'+val.id+'" level="'+val.level+'" branchId="'+val.branchId+'">' +
							'<div class="mcEmail">'+val.email+'</div>' +
							'<div class="mcTime">'+val.utime+'</div>' +
							'<div class="mcMessаge">'+val.message+'</div>' +
							'<div class="mcAnswer">Ответить</div>' +
						'</div>',
					comments = node.find('.mcComments');
				jQuery(div).appendTo(comments).find('.mcAnswer').on('click', answer_mc);
			});
		}
	);
}

function remove_mc( event ) {
	event.preventDefault();

	var $this = jQuery(this),
		comment = $this.parent(),
		info = $this.parents('.mComments').find('.mcInfo');
	jQuery.ajax(
		type : 'POST', url : '/templates/protostar/php/mCommentsAdmin.php', dataType: 'json', 
		data: { id : comment.attr('mcid'), table: info.attr('table'), method: 'remove' },
		success: function(data) {
			info.attr('offset', +info.attr('offset')-1).attr('len', +info.attr('len')-1);
			comment.remove();
		}
	);
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