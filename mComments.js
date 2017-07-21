function mComments() {
	jQuery('.mcButton').on('click', addCommet_mc);
	jQuery('.mcAnswer').on('click', addCommetFloatForm_mc);
	jQuery('.mcMore').each(function(ind, val){
		var $this = jQuery(this),
			info = jQuery(this).parents('.mComments').find('.mcTable'),
			num = info.attr('num'),
			len = info.attr('len');

		$this.attr('num', num).attr('len', len);

		if ( +num < +len) {
			$this.removeClass('ShliambOff').off('click', moreComments_mc);
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
		len = $this.attr('len'),
		num = $this.attr('num');

	jQuery.ajax({
		type : 'POST', url : '/templates/protostar/php/mCommentsMore.php', dataType: 'json', 
		data: { len: len, num: num },
		success: function( data ) {
			$this.attr('len', data.len).attr('num', data.num);
			if (data.num >= data.len) {
				$this.addClass('ShliambOff').off('click', moreComments_mc);
			}

			jQuery.each(data.items, function(ind, val) {
				var div = '<div class="mcComment mcLevel'+val.level+'" mcid="'+val.id+'">' +
							'<div class="mcEmail">'+val.email+'</div>' +
							'<div class="mcTime">'+val.utime+'</div>' +
							'<div class="mcMessаge">'+val.message+'</div>' +
							'<div class="mcAnswer">Ответить</div>' +
						'</div>',
					comments = $this.parents('.mComments').find('.mcComments');
				div.appendTo(comments).find('.mcAnswer').on('click', addCommetFloatForm_mc);
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