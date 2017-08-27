var data = {
		action: 'recommender_ingest',
		nonce: recom.nonce,
		item: recom.item,
		type: []
	}

var recom_obj_to_qs = function(object, prefix) {
	return Object.keys(object).reduce(function(string, key) {
			var value = object[key];
			var key = prefix ? prefix + "[" + key + "]" : key;

			var qs = (typeof value == typeof {})
						? recom_obj_to_qs(value, key)
						: encodeURIComponent(key) + '=' + encodeURIComponent(value);

			string.push(qs);

			return string;
		},[]).join('&');
}

var recom_ajax = function(options, callback) {

	options.method = typeof options.method !== 'undefined' ? options.method : 'GET';
	options.async = typeof options.async !== 'undefined' ? options.async : false;

	var req = window.XMLHttpRequest
				? new XMLHttpRequest()
				: new ActiveXObject("Microsoft.XMLHTTP");

	req.onreadystatechange = function() {
		if (req.readyState == 4 && req.status >= 200 && req.status < 300) {
			if (!!callback) {
				callback(req.responseText);
			} else {
				return {data: req.responseText, status: req.status, error: false}
			}
		} else { 
			return {data: req.responseText || '', status: req.status, error: true}
		}
	}

	if (~['POST', 'PUT'].indexOf(options.method)) {

		req.open(options.method, options.url, options.async);
		req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		if (options.dataType == 'json') {
			options.data = recom_obj_to_qs(options.data);
		}

		req.send(options.data);

	} else {

		if(typeof options.data !== 'undefined' && options.data !== null) {
			options.url = options.url + '?' + options.data;
		}

		req.open(options.method, options.url, options.async);
		req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		req.send(null);

	}

	if (!options.async) {
		if (req.status >= 200 && req.status < 300) {
			if (!!callback) {
				callback(req.responseText)
			} else {
				return {data: req.responseText, status: req.status, error: false}
			}
		} else { 
			return {data: req.responseText || '', status: req.status, error: true}
		}
	}
}

window.onload = function() {

	if(!recom.item.length)
		return

	var hash = window.location.hash.split('#.rec.').pop();
	var is_scrolled = false,
		is_liked = false,
		is_shared = false,
		is_copied = false;

	if(recom.item.length)
		data.type.push('view');

	if(recom.scroll.length)
		window.onscroll = function (e) {
			if(!!is_scrolled) return;

			data.type.push('scroll');
			is_scrolled = true;
		}

	if(recom.read.length) {
		seconds = Math.round(readingTime('body') / 5) * 1000;

		setTimeout(function () {
			data.type.push('read');
		}, seconds);
	}

	if(recom.like.length) {
		like_elems = document.querySelectorAll(recom.like_selector);

		for(i=0; i<like_elems.length; i++)
			like_elems[i].onclick = function() {
				if(!!is_liked) return;
				
				data.type.push('like');
				is_liked = true;
			}
	}

	if(recom.share.length) {
		share_elems = document.querySelectorAll(recom.share_selector);
		
		for(i=0; i<share_elems.length; i++)
			share_elems[i].onclick = function() {
				if(!!is_shared) return;
				
				data.type.push('share');
				is_shared = true;
			}
	}

	if(recom.copy.length)
		document.oncopy = function() {
			if(!!is_copied) return;
			
			data.type.push('copy');
			is_copied = true;
		}

	if(recom.hash.length && hash.length) {
		data.type.push('hash');
		data.user = hash;
	}
}

window.onbeforeunload = function(evt) {
	request = {
		url: recom.ajaxurl,
		method: 'POST',
		async: false,
		data: data,
		dataType: 'json'
	};

	recom_ajax(request);
}