function validateSubdomain(domain, responseID){
    new Ajax({
        url: 'ajax.php?modul=validation&typ=subdomain&domain='+domain,
        ResponseHandle: {
            id: responseID,
            action: "callback",
            callback: validate_Callback
        }
    }).go();
}
function getNodeValue(elem){
	if(elem.text) return elem.text;
	else return elem.textContent;
}
function validate_Callback(http){
	xml = http.responseXML.documentElement;
	targetID = this.options.ResponseHandle.id;
	message = getNodeValue(xml.childNodes[1]);
	classes = getNodeValue(xml.childNodes[0]);
	writeElement(targetID, message, classes);
}

function updateSubdomain(hostElem){
	subdomainElem = document.getElementById('subdomain');
	new_value = subdomainElem.value.split(".")[0];
	if(hostElem.selectedIndex != 0){
		new_value += "."+hostElem.options[hostElem.selectedIndex].value;
	}
	subdomainElem.value = new_value
	subdomainElem.onchange();
}

function writeElement(id, content, classes) {
	elem = document.getElementById(id);
	elem.innerHTML = content;
	elem.className = classes;
}

function builtProgressbar(value){
	string = "<div style='width:250px;'>";
	string += "<div style='width: "+value+"; overflow:hidden;'>";
	string += "<img src='images/progress_bar.jpg' width='250px' height='10px;' alt='' />";
	string += "</div>";
	string += "</div>";
	return string;
}

function validatePasswordStrength(pass, responseID){
    new Ajax({
        url: 'ajax.php?modul=validation&typ=password_strength&password='+pass,
        ResponseHandle: {
            id: responseID,
            action: "callback",
            callback: validatePasswordStrength_Callback
        }
    }).go();
}
function validatePasswordStrength_Callback(http){
	xml = http.responseXML.documentElement;
	targetID = this.options.ResponseHandle.id;
	message = getNodeValue(xml.childNodes[2]);
	classes = getNodeValue(xml.childNodes[0]);
	value = getNodeValue(xml.childNodes[1]);
	message += builtProgressbar(value);
	writeElement(targetID, message, classes);
}
function validatePassword(pass1, pass2, responseID){
	 new Ajax({
        url: 'ajax.php?modul=validation&typ=password&pass1='+pass1+'&pass2='+pass2,
        ResponseHandle: {
            id: responseID,
            action: "callback",
            callback: validate_Callback
        }
    }).go();
}
function validateEmail(email, responseID){
	 new Ajax({
        url: 'ajax.php?modul=validation&typ=email&email='+email,
        ResponseHandle: {
            id: responseID,
            action: "callback",
            callback: validate_Callback
        }
    }).go();
}
function loadTemplates(select, responseID){
	new Ajax({
        url: 'ajax.php?modul=communication&typ=loadTemplates&short='+select.options[select.selectedIndex].value,
        ResponseHandle: {
            id: responseID,
            action: "callback",
            callback: loadTemplates_Callback
        }
    }).go();
}
function loadTemplates_Callback(http){
    xml = http.responseXML.documentElement;
    targetID = this.options.ResponseHandle.id;
    targetElem = document.getElementById(targetID);
    for (i = targetElem.length - 1; i > 0; i--) {
        targetElem.remove(i);
    }
    templates = xml.getElementsByTagName('result');
    for (i = 0; i < templates.length; i++) {
        option = document.createElement("option");
        option.text = getNodeValue(templates[i].childNodes[1]);
        option.value = getNodeValue(templates[i].childNodes[0]);
        try {
            targetElem.add(option, null);
        } 
        catch (e) {
            targetElem.add(option);
        }
    }
}

function $(id){
	return document.getElementById(id);
}
function load_template_preview(template){
	$('template_preview').src = 'templates/'+template;
}
