function mComments() {
	jQuery('#mcPage select.mcTables').on('change', changeTable_mc);
	jQuery('.mcMore').on('click', loadComments_mc);
	jQuery('.mcButton').on('click', addCommet_mc);
}

function changeTable_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		info = mc.find('.mcInfo'),
		talbe = $this.val(),
		num = 5;

	jQuery.ajax({
		type: 'POST', url : '/templates/protostar/php/mCommentsAdmin.php', dataType: 'json', 
		data: { table: talbe,
				method: 'info'},
		success: function(data) {
			info.attr('len', data.len).attr('num', num).attr('offset', 0).attr('table', talbe);
			mc.find('.mcComments').empty();
			mc.find('.mcMore').removeClass('ShliambOff').trigger('click');
		}
	});
}

function loadComments_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		node = $this.parents('.mComments'),
		info = node.find('.mcInfo');

	jQuery.ajax({
		type : 'POST', url : '/templates/protostar/php/mCommentsAdmin.php', dataType: 'json', 
		data: { offset: node.find('.mcLevel0').length,
				num: info.attr('num'),
				table: info.attr('table'), 
				method: 'load' },
		success: function(data) {
			var comments = node.find('.mcComments');

			jQuery.each(data.items, function(ind, val) {
				var params = 'class="mcComment mcLevel'+val.level+'" mcid="'+val.id+'" tid="'+data.tid+'" level="'+val.level+'" branchId="'+val.branchId+'"',
					email = '<div class="mcEmail">'+val.email+'</div>',
					utime = '<div class="mcTime">'+val.utime+'</div>',
					msg = '<div class="mcMessаge">'+val.message+'</div>',
					rm = '<div class="mcRemove">Удалить</div>',
					answ = '<div class="mcAnswer">Ответить</div>';

				jQuery('<div '+params+'>'+email+utime+msg+rm+answ+'</div>')
					.appendTo(comments);
			});

			node.find('.mcAnswer').off('click', addCommetFloatForm_mc).on('click', addCommetFloatForm_mc);
			node.find('.mcRemove').off('click', removeComment_mc).on('click', removeComment_mc);

			if ( info.attr('len') == comments.find('.mcLevel0').length ) {
				$this.addClass('ShliambOff');
			}

		}
	});
}

function removeComment_mc( event ) {
	event.preventDefault();

	var $this = jQuery(this),
		comment = $this.parents('.mcComment'),
		comments = $this.parents('.mcComments'),
		info = $this.parents('.mComments').find('.mcInfo');
	jQuery.ajax({
		type : 'POST', url : '/templates/protostar/php/mCommentsAdmin.php', dataType: 'json', 
		data: { id : comment.attr('mcid'),
				branchId: comment.attr('branchId'),
				table: info.attr('table'),
				tid : comment.attr('tid'),
				method: 'remove'},
		success: function(data) {
			info.attr('len', +info.attr('len') - data.num);
			data.ids = data.ids.map(function(val, ind) {
				return '[mcid="'+ val +'"]';
			}).join();
			comments.find(data.ids).remove();
		}
	});
}

function addCommet_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		info = mc.find('.mcInfo'),
		comment = $this.parents('.mcForm'),
		email = comment.find('.mcEmail'),
		msg = comment.find('.mcTextarea'),
		tid = comment.attr('tid');

	if ( !isMsg(msg.val()) || !isEmail(email.val()) ) {
		return 0;
	}

	jQuery.ajax({
		type : 'POST', url : '/templates/protostar/php/mCommentsAdmin.php', dataType: 'json', 
		data: { email : email.val(),
				msg : msg.val(), 
				branchId: comment.attr('branchId'),
				parentId : comment.attr('mcid'), 
				table: info.attr('table'), 
				level: comment.attr('level'),
				method: 'add'},
		success: function( data ) {
			var div = '<div class="mcComment mcLevel'+data.level+'" mcid="'+data.id+'" tid="'+tid+'" level="'+data.level+'" branchId="'+ data.branchId +'">'  +
						'<div class="mcEmail">'+data.email+'</div>' +
						'<div class="mcTime">'+data.utime+'</div>' +
						'<div class="mcMessаge">'+data.message+'</div>' +
						'<div class="mcRemove">Удалить</div>' +
						'<div class="mcAnswer">Ответить</div>' +
					'</div>';
			
			if (data.level != 0) {
				var flag = true,
					node = mc.find('.mcComments .mcComment[mcid="'+comment.attr('mcid')+'"]'),
					startLevel = node.attr('level');
				while (flag) {
					var next = node.next();
					if( next == undefined || next.attr('level') == undefined || next.attr('level') <= startLevel) {
						node.after(div);

						flag = false;
					}

					node = next;
				}
			} else {
				mc.find('.mcComments').prepend(div);
			}

			email.val('');
			msg.val('');
			comment.attr('mcid', 0).attr('branchId', 0);

			if (comment.hasClass('mcFormFloat')) {
				comment.addClass('ShliambOff');
			}

			if (data.level == 0) {
				info.attr('len', +info.attr('len') + 1);
			}
		}
	});
}

function addCommetFloatForm_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		comment = $this.parents('.mcComment'),
		parentId = comment.attr('mcid'),
		branchId = comment.attr('branchId'),
		floatForm = mc.find('.mcFormFloat'),
		tid = comment.attr('tid');

	if (+branchId == 0) {
		branchId = parentId;
	}

	comment.after(floatForm);
	floatForm.attr('mcid', parentId)
		.attr('branchId', branchId)
		.attr('level', +comment.attr('level')+1)
		.attr('tid', tid)
		.removeClass('ShliambOff');
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