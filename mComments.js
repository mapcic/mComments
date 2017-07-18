function mComments() {
	jQuery('.mcButton').on('click', addCommets_mc);
	jQuery('.mcAnswer').on('click', addCommetForm_mc);
}

function addCommets_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		comment = $this.parents('.mcForm'),
		parent = comment.attr('mcid'),
		email = comment.find('.mcEmail'),
		msg = comment.find('.mcComment');
	
	if ( !isMsg(msg.val()) || !isEmail(email.val()) ) {
		return 0;
	}

	jQuery.ajax({
		type : 'POST', url : '/path/to/mComments_Add.php', dataType: 'json', 
		data: { email : email, msg : msg, parent : parent },
		success: function( data ) {	
			var div = '<div class="mcLevel'+data.level+'" mcid="'.data.id.'">' +
						'<div class="mcEmail">'+data.email+'</div>' +
						'<div class="mcTime">'+data.utime+'</div>' +
						'<div class="mcMessаge">'+data.message+'</div>' +
						'<div class="mcAnswer">Ответить</div>' +
					'</div>';
			jQuery(div).insertAfter(comment);
			comment.addClass('ShliamOff');
			email.val('');
			msg.val('');
			comment.attr('mcid', 0);
		}
	});
}

function addCommetForm_mc(event) {
	event.preventDefault();
}