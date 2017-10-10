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
		branchId = comment.attr('branchId'),
		level = comment.attr('level'),
		email = comment.find('.mcEmail'),
		msg = comment.find('.mcTextarea');

	if ( !isMsg(msg.val()) || !isEmail(email.val()) ) {
		return 0;
	}

	jQuery.ajax({
		type : 'POST', url : '/path/to/mCommentsAdd.php', dataType: 'json', 
		data: { email : email.val(), msg : msg.val(), branchId: branchId, parent : parent, table: table, level: level },
		success: function( data ) {
			var div = '<div class="mcComment mcLevel'+data.level+'" mcid="'+data.id+'" level="'+data.level+'" branchId="'+ data.branchId +'">'  +
						'<div class="mcEmail">'+data.email+'</div>' +
						'<div class="mcTime">'+getDate(data.utime)+'</div>' +
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

			mc.find('.mcAnswer')
				.off('click', addCommetFloatForm_mc)
				.on('click', addCommetFloatForm_mc);
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

function moreComments_mc(event) {
	event.preventDefault();

	var $this = jQuery(this),
		table = $this.attr('table'),
		len = $this.attr('len'),
		num = $this.attr('num'),
		offset = +$this.parents('.mComments').find('.mcLevel0').length;
		console.log(offset);

	jQuery.ajax({
		type : 'POST', url : '/path/to/mCommentsMore.php', dataType: 'json', 
		data: { len: len, num: offset, table: table },
		success: function( data ) {
			$this.attr('len', data.len).attr('num', data.num);
			if (data.num >= data.len) {
				$this.addClass('ShliambOff').off('click', moreComments_mc);
			}

			jQuery.each(data.items, function(ind, val) {
				var div = '<div class="mcComment mcLevel'+val.level+'" mcid="'+val.id+'" level="'+val.level+'" branchId="'+val.branchId+'">' +
							'<div class="mcEmail">'+val.email+'</div>' +
							'<div class="mcTime">'+getDate(val.utime)+'</div>' +
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

function getDate(date) {
	var date = new Date(date),
		options = {
			'year' : date.getFullYear()+'',
			'month' : date.getMonth()+'',
			'day' : date.getDay()+'',
			'hours' : date.getHours()+'',
			'minutes' : date.getMinutes()+'',
			'seconds' : date.getSeconds()+''
		}
	for (key in options) {
		options[key] = options[key].length == 1? 0 + options[key] : options[key]; 
	}

	return options.hours+':'+options.minutes+':'+options.seconds+' '+options.day+'.'+options.month+'.'+options.year;
}