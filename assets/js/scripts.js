jQuery(document).ready(function($) {

	var hash = window.location.hash.split('#.rec.').pop();

	if(recom.item.length)
	{
		$.ajax({
			url: recom.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: {
				action: 'recommender_ajax',
				nonce: recom.nonce,
				type: 'view',
				item: recom.item
			}
		});

		if(recom.scroll.length)
		{
			$(window).ready(function(){
				$(this).one('scroll', function(){

					$.ajax({
						url: recom.ajaxurl,
						type: 'post',
						dataType: 'json',
						data: {
							action: 'recommender_ajax',
							nonce: recom.nonce,
							type: 'scroll',
							item: recom.item
						}
					});

				});
			});
		}

		if(recom.read.length)
		{
			seconds = Math.round($('body').readingTime() / 5) * 1000;

			setTimeout(function () {

				$.ajax({
					url: recom.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'recommender_ajax',
						nonce: recom.nonce,
						type: 'read',
						item: recom.item
					}
				});

			}, seconds);
		}

		if(recom.like.length)
		{
			$(document).on('click', recom.like_selector, function(event) {

				$.ajax({
					url: recom.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'recommender_ajax',
						nonce: recom.nonce,
						type: 'like',
						item: recom.item
					}
				});

			});
		}

		if(recom.share.length)
		{
			$(document).on('click', recom.share_selector, function(event) {

				$.ajax({
					url: recom.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'recommender_ajax',
						nonce: recom.nonce,
						type: 'share',
						item: recom.item
					}
				});

			});
		}

		if(recom.copy.length)
		{
			$(window).bind('copy', function() {

				$.ajax({
					url: recom.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'recommender_ajax',
						nonce: recom.nonce,
						type: 'copy',
						item: recom.item
					}
				});

			});
		}

		if(recom.hash.length && hash.length)
		{
			$.ajax({
				url: recom.ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {
					action: 'recommender_ajax',
					nonce: recom.nonce,
					type: 'hash',
					item: recom.item,
					user: hash
				}
			});
		}
	}

});