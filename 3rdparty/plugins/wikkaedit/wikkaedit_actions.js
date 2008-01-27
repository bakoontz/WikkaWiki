/*
////////////////////////////////////////////////////////////////////////
// WikkaEdit                                                          //
// v. 1.00                                                            //
// supported browsers : MZ1.4+, MSIE5+, Opera 8+, khtml/webkit        //
//                                                                    //
// (C) 2007 Olivier Borowski (olivier.borowski@wikkawiki.org)         //
// Homepage : http://docs.wikkawiki.org/WikkaEdit                     //
//                                                                    //
// This program is free software; you can redistribute it and/or      //
// modify it under the terms of the GNU General Public License        //
// as published by the Free Software Foundation; either version 2     //
// of the License, or (at your option) any later version.             //
//                                                                    //
// This program is distributed in the hope that it will be useful,    //
// but WITHOUT ANY WARRANTY; without even the implied warranty of     //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      //
// GNU General Public License for more details.                       //
//                                                                    //
// You should have received a copy of the GNU General Public License  //
// along with this program; if not, write to the Free Software        //
// Foundation, Inc., 59 Temple Place - Suite 330, Boston,             //
// MA  02111-1307, USA.                                               //
//                                                                    //
////////////////////////////////////////////////////////////////////////
*/


// ===== line content =====
// get line content and current selection
// - text : line content
// - sr : current selection (relative to the current line)
// - lineStart : number of characters before the current line starts
WikkaEdit.prototype.getLineContentAndSr = function() {
	// textarea content
	var text = this.getTextAreaContent();
	// position of the selection
	var sr = this.getSelectionRange();
	// find line content and relative selection range
	var lineStart = text.lastIndexOf("\n", sr.start-1) + 1;
	if (lineStart == -1) lineStart = 0;
	var lineEnd1 = text.indexOf("\n", sr.start);
	if (lineEnd1 == -1) lineEnd1 = text.length;
	var lineEnd2 = text.indexOf("\n", sr.end);
	if (lineEnd2 == -1) lineEnd2 = text.length;
	var lineEnd = Math.min(lineEnd1, lineEnd2);
	// returned values
	var line = text.substring(lineStart, lineEnd);
	var newSr = {start:sr.start - lineStart, end:sr.end - lineStart};
	//this.addLog("lineAndSr : sr = {" + newSr.start + ", " + newSr.end + "}, text = " + line);
	return {text:line, sr:newSr, lineStart:lineStart}; //absoluteSr:sr};
}

// ===== action content =====
// get action content and current selection
// - text : action content
// - sr : current selection (relative to the current action)
// - tagStart : number of characters before the current action starts
WikkaEdit.prototype.getActionContentAndSr = function() {
	var lineAndSr = this.getLineContentAndSr();
	var tagStart = lineAndSr.text.lastIndexOf("{{", lineAndSr.sr.start);
	var tagEnd = lineAndSr.text.indexOf("}}", lineAndSr.sr.start - 2);
	var actionAndParams = "", newSr =  {start:null, end:null};
	//this.addLog("tagStart = " + tagStart + " - tagEnd = " + tagEnd);
	if ((tagStart != -1) && (tagEnd != -1)) {
		// returned values
		actionAndParams = lineAndSr.text.substring(tagStart + 2, tagEnd);	// +2 to remove "{{"
		newSr = {start:lineAndSr.sr.start - tagStart - 2, end:lineAndSr.sr.end - tagStart - 2};
	}
	//this.addLog("actionAndSr : sr = {" + newSr.start + ", " + newSr.end + "}, text = " + actionAndParams);
	return {text:actionAndParams, sr:newSr, tagStart:lineAndSr.lineStart + tagStart}; //, absoluteSr:lineAndSr.absoluteSr};
}

WikkaEdit.prototype.getActionNameAndParams = function() {
	var actionAndSr = this.getActionContentAndSr();
	var actionNameEnd = actionAndSr.text.indexOf(" ");					// {{actionName param=""}}
	if (actionNameEnd == -1) actionNameEnd = actionAndSr.text.length;	// {{actionName}}
	var actionName = actionAndSr.text.substr(0, actionNameEnd);
	var actionParams = actionAndSr.text.substr(actionName.length);
	var erase = false;
	var actionParamsHid = "";
	for(var i=0; i<actionParams.length; i++) {
		var car = actionParams.substr(i, 1);
		if (car == "\"") {
			erase = !erase;
			actionParamsHid += "\"";
		} else
			actionParamsHid += (erase ? "_" : car);
	}
	//this.addLog("action : name = " + actionName + "<br/>params=" + actionParams);
	return {name:actionName, params:actionParams, paramsHid:actionParamsHid};
}

WikkaEdit.prototype.getParamUnderCarret = function() {
	var actionAndSr = this.getActionContentAndSr();
	var objAction = this.getActionNameAndParams();
	var i;
	var paramUnderCarret = null;
	var paramCarret = actionAndSr.sr.start - objAction.name.length - 1; //sr.start - lineStart - tagStart - actionName.length - 3;
	if ((paramCarret < 0) || (paramCarret >= objAction.params.length))
		paramCarret = null;
	if (paramCarret != null) {
		var lastSpace = objAction.paramsHid.lastIndexOf(" ", paramCarret);
		// -1 is ok
		paramUnderCarret = objAction.paramsHid.substr(lastSpace + 1);
		paramUnderCarret = paramUnderCarret.substr(0, paramUnderCarret.indexOf("="));
	}
	//this.addLog("paramUnderCarret = " + paramUnderCarret);
	return paramUnderCarret;
}

WikkaEdit.prototype.getParams = function() {
	var objAction = this.getActionNameAndParams();
	var tabParamsD2 = objAction.params.split("\"");
	var tabParams = new Array();
	for(var i=0; i<tabParamsD2.length-1; i+=2) {
		var paramName = this.trim(tabParamsD2[i].substr(1, tabParamsD2[i].length - 2));
		var paramValue = tabParamsD2[i+1];
		tabParams.push({name: paramName, value: paramValue});
		//this.addLog("paramName=<" + paramName + ">");
	}
	return tabParams;
}

// ===== sort parameters =====
// display first already existing params
// then, params missing in the current line
WikkaEdit.prototype.sortParams = function(objAction) {
	var tabUsedParams = this.getParams();
	var tabAvailParams = new Array();
	var actionRef = this.nameToRef(objAction.name);
	var i, j;
	for(i in this.we_actions[actionRef].we_params)
		tabAvailParams.push({name:this.we_actions[actionRef].we_params[i].name, priority:-1});
	for(i=0; i<tabUsedParams.length; i++) {
		for(j=0; j<tabAvailParams.length; j++) {
			if (tabAvailParams[j].name == tabUsedParams[i].name)
				tabAvailParams[j].priority = 100 - i;
		}
	}
	tabAvailParams.sort(this.sortParams2);
	return tabAvailParams;
}

WikkaEdit.prototype.sortParams2 = function(a, b) {
	if (a.priority < b.priority)
		return 1;
	if (a.priority > b.priority)
		return -1;
	return (a.name > b.name ? 1 : -1);
}

// ===== contextual help =====
WikkaEdit.prototype.contextualHelp = function() {
	//this.clearLog();
	var actionAndSr = this.getActionContentAndSr();
	var help = "&nbsp;";

	// we are in a {{ }} delimiter
	if (actionAndSr.text != "") {
		var objAction = this.getActionNameAndParams();
		var actionRef = this.nameToRef(objAction.name);
		// ensure this action exists
		if (this.we_actions[actionRef] != null) {
			var paramUnderCarret = this.getParamUnderCarret();
			var tabAvailParams = this.sortParams(objAction);

			// build line
			var params="", AIP, paramRef, clickaction;
			for(var i=0; i<tabAvailParams.length; i++) {
				paramRef = this.nameToRef(tabAvailParams[i].name);
				AIP = this.we_actions[actionRef].we_params[paramRef];
				params += " ";
				if (tabAvailParams[i].priority != -1)	// parameter present
					clickaction = " onclick=\"varWikkaEdit.paramClick('" + paramRef + "');\"";
				else									// parameter not used yet
					clickaction = " onclick=\"varWikkaEdit.we_ta.focus();\" ondblclick=\"varWikkaEdit.paramClick('" + paramRef + "');\"";
				params += "<span class=\"wikkaparamlink\"" + clickaction + " onmouseover=\"varWikkaEdit.paramOver(this, '" + actionRef + "', '" + paramRef + "', " + (tabAvailParams[i].priority != -1) + ");\" onmouseout=\"varWikkaEdit.paramOut('" + paramRef + "');\">";
				if (AIP.name == paramUnderCarret) params += "<b>";
				if (tabAvailParams[i].priority == -1) params += "<i>";
				params += AIP.name + "=\"" + AIP.value + "\"";
				if (tabAvailParams[i].priority == -1) params += "</i>";
				if (AIP.name == paramUnderCarret) params += "</b>";
				params += "</span>";
				// TODO : add warning for required parameters
			}
			help = "{{<span class=\"wikkaparamlink\" onclick=\"varWikkaEdit.paramClick();\" onmouseover=\"varWikkaEdit.paramOver(this, '" + actionRef + "');\" onmouseout=\"varWikkaEdit.paramOut();\">" + objAction.name + "</span>" + params + "}}";
		}
	}

	if (help != this.we_helpPreviousContent)
		this.we_help.innerHTML = help;
	this.we_helpPreviousContent = help;
	this.we_help.style.visibility = (help == "&nbsp;" ? "hidden" : "");
}


// ===== mouse on a parameter zone =====
// => display tooltip
WikkaEdit.prototype.paramOver = function(obj, actionRef, paramRef, used) {
	var coords = this.getObjectCoords(obj);
	coords.y += 25;		// TODO remove this hardcoded value (toolbar height)
	this.we_tooltip.style.left = coords.x + "px";
	this.we_tooltip.style.top = coords.y + "px";
	this.we_tooltip.style.visibility = "";
	var tip;
	if (paramRef == null)
		tip = "click to select the whole action";
	else if (used)
		tip = "click to select the paramater value";
	else
		tip = "doucle-click to add this parameter";
	var html = "";
	html += "<div class='wikkatooltip_header'><u>Tip</u> : " + tip + "</div>";
	html += "<div class='wikkatooltip_body'>";
	if (paramRef == null) {
		html += this.we_actions[actionRef].we_summary + "<br />";
		if (this.we_actions[actionRef].we_usage != null)
			html += "<hr class='wikkatooltip_separator' />" + this.we_actions[actionRef].we_usage;
	} else
		html += this.we_actions[actionRef].we_params[paramRef].description;
	html += "</div>";
	this.we_tooltip.innerHTML = html;
}

// ===== mouse leave the parameter zone =====
// => hide tooltip
WikkaEdit.prototype.paramOut = function(paramRef) {
	this.we_tooltip.style.visibility = "hidden";
}

// ===== click on the action name or a parameter =====
// called by
// - onclick event (for action or existing parameter)
// - ondblclick event (for non existing parameter)
WikkaEdit.prototype.paramClick = function(paramRef) {
	var actionAndSr = this.getActionContentAndSr();
	var posStart, posEnd;

	// click on the action name?
	// => select action
	if (paramRef == null) {
		posStart = actionAndSr.tagStart;
		posEnd = posStart + actionAndSr.text.length + 4; // +4 = {{}}
		this.setSelectionRange(posStart, posEnd);
		this.we_ta.focus();
		return;
	}

	// click on a param?
	var objAction = this.getActionNameAndParams();
	var actionRef = this.nameToRef(objAction.name);
	var paramName = this.we_actions[actionRef].we_params[paramRef].name;
	var pos = objAction.paramsHid.indexOf(" " + paramName); //, actionAndSr.sr.start);
	if (pos == -1) {
		// parameter doesn't exist => add it?
		var actionRef = this.nameToRef(objAction.name);
		var newCarretPos = actionAndSr.tagStart + actionAndSr.text.length + 2;
		var paramValue = this.we_actions[actionRef].we_params[paramRef].value;
		//this.setSelectionRange(newCarretPos, newCarretPos);
		var srBefore = {start:newCarretPos, end:newCarretPos};
		posStart = newCarretPos + paramName.length + 3;
		posEnd = posStart + paramValue.length;
		var srAfter = {start:posStart, end:posEnd};
		this.addToSelection(" " + paramName + "=\"" + paramValue + "\"", null, srAfter, srBefore);
		//this.setSelectionRange(posStart, posEnd);
	} else {
		// parameter already exist => select its value
		posStart = pos + paramName.length + 3;	// +3 = space + equal + quote
		posEnd = objAction.paramsHid.indexOf("\"", posStart);
		posStart += objAction.name.length;
		posEnd += objAction.name.length;
		posStart += actionAndSr.tagStart + 2;	// 2 = {{
		posEnd += actionAndSr.tagStart + 2;	//
		this.setSelectionRange(posStart, posEnd);
	}
	this.we_ta.focus();
}
