// comment page
function initP() {
	jQuery('#mcPage select.mcTables').on('change', changePage);
	jQuery('#mcPage .mcMore').on('click', loadP);
	jQuery('#mcPage .mcButton').on('click', commentP);
}

function loadP() {
	var $this = jQuery(this),
		node = $this.parents('.mComments'),
		info = node.find('.mcInfo');

	jQuery.ajax({
		type : 'POST', dataType: 'json',
		url : '/templates/protostar/php/mCommentsAdmin.php',
		data: { offset: node.find('.mcComment[level=0]').length,
				num: info.attr('num'),
				table: info.attr('table'), 
				method: 'load'},
		success: function(data) {
			var comments = node.find('.mcComments');

			jQuery.each(data.items, function(ind, val) {
				jQuery(commentHTML(val)).appendTo(comments);
			});

			node.find('.mcAnswer').off('click', showFormP).on('click', showFormP);
			node.find('.mcRemove').off('click', rmP).on('click', rmP);

			if ( info.attr('len') == node.find('.mcComment[level=0]').length ) {
				$this.addClass('ShliambOff');
			}
		}
	});
}

function changePage(event) {
	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		info = mc.find('.mcInfo'),
		table = $this.val();

	jQuery.ajax({
		type: 'POST', dataType: 'json',
		url : '/templates/protostar/php/mCommentsAdmin.php',
		data: { table: table,
				method: 'info'},
		success: function(data) {
			info.attr('len', data.len).attr('offset', 0).attr('table', table);
			mc.find('.mcComments').empty();
			mc.find('.mcMore').removeClass('ShliambOff').trigger('click');
		}
	});
}

function commentP() {
	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		info = mc.find('.mcInfo'),
		form = $this.parents('.mcForm'),
		email = form.find('.mcEmail'),
		msg = form.find('.mcTextarea');

	if ( !isMsg(msg.val()) || !isEmail(email.val()) ) {
		return 0;
	}

	jQuery.ajax({
		type : 'POST', dataType: 'json', 
		url : '/templates/protostar/php/mCommentsAdmin.php',
		data: { email : email.val(),
				msg : msg.val(), 
				branchId: form.attr('branchId'),
				parentId : form.attr('mcid'), 
				table: info.attr('table'), 
				level: form.attr('level'),
				method: 'add'},
		success: function( data ) {
			var div = commentHTML(data);
			
			if (data.level != 0) {
				var flag = true,
					node = mc.find('.mcComments .mcComment[mcid="'+form.attr('mcid')+'"]'),
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
			form.attr('mcid', 0).attr('branchId', 0);

			if (form.hasClass('mcFormFloat')) {
				form.addClass('ShliambOff');
			}

			if (data.level == 0) {
				info.attr('len', +info.attr('len') + 1);
			}

			initL();
		}
	});
}

function rmP() {
	var $this = jQuery(this),
		comment = $this.parents('.mcComment'),
		comments = $this.parents('.mcComments'),
		info = $this.parents('.mComments').find('.mcInfo');

	jQuery.ajax({
		type : 'POST', dataType: 'json', 
		url : '/templates/protostar/php/mCommentsAdmin.php',
		data: { id : comment.attr('mcid'),
				branchId: comment.attr('branchId'),
				table: info.attr('table'),
				method: 'remove'},
		success: function(data) {
			info.attr('len', +info.attr('len') - data.num);
			data.ids = data.ids.map(function(val, ind) {
				return '[mcid="'+ val +'"]';
			}).join();
			comments.find(data.ids).remove();

			initL();
		}
	});
}

function showFormP() {
	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		comment = $this.parents('.mcComment'),
		parentId = comment.attr('mcid'),
		branchId = comment.attr('branchId'),
		floatForm = mc.find('.mcFormFloat');

	if (+branchId == 0) {
		branchId = parentId;
	}

	comment.after(floatForm);
	floatForm.attr('mcid', parentId)
		.attr('branchId', branchId)
		.attr('level', +comment.attr('level')+1)
		.removeClass('ShliambOff');
}


// commnet last
function initL() {
	var mc = jQuery('#mcLast'),
		info = mc.find('.mcInfo'),
		table = info.attr('table');

	jQuery.ajax({
		type: 'POST', dataType: 'json',
		url : '/templates/protostar/php/mCommentsAdmin.php',
		data: { table: table,
				method: 'info'},
		success: function(data) {
			info.attr('len', data.len)
				.attr('table', table);
			mc.find('.mcComments')
				.empty();
			mc.find('.mcMore')
				.removeClass('ShliambOff')
				.off('click', loadL)
				.on('click', loadL)
				.trigger('click');
		}
	});

	mc.find('.mcButton').off('click', commentL)
		.on('click', commentL);
}

function loadL() {
	var $this = jQuery(this),
		node = $this.parents('.mComments'),
		info = node.find('.mcInfo');

	jQuery.ajax({
		type : 'POST', dataType: 'json',
		url : '/templates/protostar/php/mCommentsAdmin.php',
		data: { offset: jQuery('.mcComment[last=1]').length,
				num: info.attr('num'),
				table: info.attr('table'), 
				method: 'load'},
		success: function(data) {
			var comments = node.find('.mcComments');

			comments.find('.mcComment').removeClass('lastComment');

			jQuery.each(data.items, function(ind, val) {
				if (node.find('.mcComment[table='+val.table+'][branchId='+val.branchId+']').length == 0){
					jQuery.each(val.items, function(i, v) {
						jQuery(commentHTML(v)).appendTo(comments);
						comments.children().last()
							.attr('table', val.table)
							.attr('last', 0)
							.prepend('<div class="mcPageName">'+val.page+'</div>');
					});
				}
				markL(val.mark);
			});

			node.find('.mcAnswer').off('click', showFormL).on('click', showFormL);
			node.find('.mcRemove').off('click', rmL).on('click', rmL);

			if ( info.attr('len') == node.find('.mcComment[last=1]').length ) {
				$this.addClass('ShliambOff');
			}
		}
	});
}

function markL(ids) {
	ids = ids instanceof Array? ids : [ids];

	var idsStr = ids.map(function(val, ind) {
		return '[mcid="'+ val +'"]';
	}).join();

	jQuery('#mcLast .mcComment').filter(idsStr).addClass('lastComment').attr('last', 1);
}

function commentL() {
	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		info = mc.find('.mcInfo'),
		form = $this.parents('.mcForm'),
		email = form.find('.mcEmail'),
		msg = form.find('.mcTextarea');

	if ( !isMsg(msg.val()) || !isEmail(email.val()) ) {
		return 0;
	}

	jQuery.ajax({
		type : 'POST', dataType: 'json', 
		url : '/templates/protostar/php/mCommentsAdmin.php',
		data: { email : email.val(),
				msg : msg.val(), 
				branchId: form.attr('branchId'),
				parentId : form.attr('mcid'), 
				table: form.attr('table'), 
				level: form.attr('level'),
				method: 'add'},
		success: function( data ) {
			var div = commentHTML(data);
			
			if (data.level != 0) {
				var flag = true,
					node = mc.find('.mcComments .mcComment[mcid="'+form.attr('mcid')+'"]'),
					startLevel = node.attr('level');
				while (flag) {
					var next = node.next();
					if( next == undefined || next.attr('level') == undefined || next.attr('level') <= startLevel) {
						node.after(div);
						node.next()
							.addClass('lastComment')
							.attr('table', form.attr('table'))
							.attr('last', 1)
						flag = false;
					}
					node = next;
				}
			} else {
				mc.find('.mcComments').prepend(div);
			}

			email.val('');
			msg.val('');
			form.attr('mcid', 0).attr('branchId', 0);

			if (form.hasClass('mcFormFloat')) {
				form.addClass('ShliambOff');
			}

			info.attr('len', +info.attr('len') + 1);
		}
	});
}

function rmL() {
	var $this = jQuery(this),
		comment = $this.parents('.mcComment'),
		comments = $this.parents('.mcComments'),
		info = $this.parents('.mComments').find('.mcInfo'),
		offset = comments.find('.mcComment[last=1]').length;

	jQuery.ajax({
		type : 'POST', dataType: 'json', 
		url : '/templates/protostar/php/mCommentsAdmin.php',
		data: { id : comment.attr('mcid'),
				branchId: comment.attr('branchId'),
				table: comment.attr('table'),
				method: 'remove'},
		success: function(data) {
			data.ids = data.ids.map(function(val, ind) {
				return '[mcid="'+ val +'"][table="'+comment.attr('table')+'"]';
			}).join();
			comments.find(data.ids).remove();
			info.attr('len', +info.attr('len') - offset + comments.find('.mcComment[last=1]').length);
		}
	});
}

function showFormL() {
	var $this = jQuery(this),
		mc = $this.parents('.mComments'),
		comment = $this.parents('.mcComment'),
		parentId = comment.attr('mcid'),
		branchId = comment.attr('branchId'),
		table = comment.attr('table'),
		floatForm = mc.find('.mcFormFloat');

	if (+branchId == 0) {
		branchId = parentId;
	}

	comment.after(floatForm);
	floatForm.attr('mcid', parentId)
		.attr('branchId', branchId)
		.attr('level', +comment.attr('level')+1)
		.attr('table', table)
		.removeClass('ShliambOff');
}

// common function

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

function commentHTML(data) {
	var params = 'class="mcComment mcLevel'+data.level+'" mcid="'+data.id+'" level="'+data.level+'" branchId="'+data.branchId+'"',
		email = '<div class="mcEmail">'+data.email+'</div>',
		utime = '<div class="mcTime">'+data.utime+'</div>',
		msg = '<div class="mcMessаge">'+data.message+'</div>',
		rm = '<div class="mcRemove">Удалить</div>',
		answ = '<div class="mcAnswer">Ответить</div>';

	return '<div '+params+'>'+email+utime+msg+rm+answ+'</div>';
}

function mComments() {
	initP();
	initL();
}