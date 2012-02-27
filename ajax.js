Object.prototype.extend = function(dest, src){
	for (var elem in src) {
		dest[elem] = src[elem];
	}
	return dest;
}
function trim(stringToTrim) {
	if(stringToTrim != undefined)
  	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
/**
 * Create a new Ajax Request
 * Copyright Philipp Rehs
 * @param {Object} options
 */
function Ajax(options){
	this.options = {
		method: 'post',
		url: null,
		parameter: null,
		contentType: 'application/x-www-form-urlencoded',
		ResponseHandle: {
			action: 'empty'
		},
		forceMime: null,
	}
	this.options = Object.extend(this.options, options || {});
	this.ajax = this.init();
	this.headers = new Object();
	this.expectedHeaders = new Object;
	this.expectedHeaders['Content-Type'] = 'text/html';
	this.headers_received = false;
	this.state = 0;
	this.status = 0;
}
/**
 * Start the request
 */
Ajax.prototype.go = function() {
	if(this.options['url'] != null){
		if(this.options['method'].toLowerCase() != "get"){
			this.options['method'] = "post";
		}
		this.ajax.open(this.options['method'].toUpperCase(), this.options['url'], true);
		this.ajax.setRequestHeader('Content-Type',this.options['contentType']);
		this.ajax.onreadystatechange = this.onreadychange(this);
		if(this.options['forceMime'] != null){
			this.ajax.overrideMimeType(this.options['forceMime']);
		}
		this.ajax.send(this.options['parameter']);
	}
	else{
		return false;
	}
};
/**
 * Set an expected header
 * @param String name
 * @param String value
 */
Ajax.prototype.setExpectedHeader = function(name, value){
	this.expectedHeaders[name] = value;
}
Ajax.prototype.stop = function(){
	this.ajax.abort();
}
/**
 * Call a request after a interval again
 * @param integer interval Interval in milliseconds
 * @param boolean now Start the first request now
 * @return integer
 */
Ajax.prototype.interval = function (interval, now){
	if(now == true){
		this.go();
	}
	return setInterval(this.getReferenceFunction(this,'go'),interval);
}
/**
 * Create a function for the delayed / interval functions
 * @param {Object} parent
 * @param String type
 * @return function
 */
Ajax.prototype.getReferenceFunction = function(parent, type){
	switch(type){
		case 'go':
			return function(){
				parent.go()
			};
		case 'result':
			return function(){
				parent.ResultHandle.call(parent,parent.ajax);
			};
		default:
			return false;
	}
}
/**
 * Call the request after a delay
 * @param delay interval The Timeout in milliseconds
 * @return integer
 */
Ajax.prototype.delayed = function(delay){
	return setTimeout(this.getReferenceFunction(this,'go'),delay);
}
/**
 * Build the XmlHttpRequest
 * @return object/boolean
 */
Ajax.prototype.init = function(){
	var xmlHttp = false;
	try {
		xmlHttp = new ActiveXObject('MSXML2.XMLHTTP.6.0');
	}
	catch(e){
		try {
			xmlHttp = new ActiveXObject('MSXML3.XMLHTTP');
		}
		catch(e){
			xmlHttp = new XMLHttpRequest();
		}
	}
	return xmlHttp;
};
Ajax.prototype.getHeader = function(name){
	return (this.headers[name.toLowerCase()] || '');
}
/**
 * Create the on ready change function
 * @param {Object} parent
 * @return function
 */
Ajax.prototype.onreadychange = function(parent){
	return function(){
		if (typeof parent.ajax.status != "unknown") {
			parent.state = parent.ajax.readyState
			parent.status = parent.ajax.status;
		}
		if(parent.ajax.readyState == 4){
			return parent.ResultHandle.call(parent,parent.ajax);
		}
  }
}
/**
 * Handle the response
 * All Parameter from the parameter object can be called via this.
 * @param {Object} http
 */
Ajax.prototype.ResultHandle = function(http){
		parameter = this.options['ResponseHandle'];
		switch(parameter.action){
			case 'add':
  			document.getElementById(parameter.id).innerHTML += http.responseText;
  		break;
  		case 'replace':
  			document.getElementById(parameter.id).innerHTML = http.responseText;
  		break;
  		case 'formfield':
  			document.getElementById(parameter.id).value = http.responseText;
  		break;
  		case 'return':
  			return http.responseText;
  		break;
			case 'callback':
				parameter['callback'].call(this, http);
			break;
  		case 'execute':
  			eval(http.responseText);
  		break;
			case 'empty':
			break;
			default:
				alert("Unknown action - "+parameter.action);
			break;
		}
}