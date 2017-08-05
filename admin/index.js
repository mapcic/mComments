function mc_init() {

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

function add_mc() {
		
}