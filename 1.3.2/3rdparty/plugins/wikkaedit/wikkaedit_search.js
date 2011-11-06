/*
////////////////////////////////////////////////////////////////////////
// WikkaEdit                                                          //
//                                                                    //
// supported browsers : MZ1.4+, MSIE5+, Opera 8+, khtml/webkit        //
//                                                                    //
// (C) 2007-2008 Olivier Borowski (olivier.borowski@wikkawiki.org)    //
// Homepage : http://wikkawiki.org/WikkaEdit                          //
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


// ===== UI events =====

// radio to switch between search and replace
WikkaEdit.prototype.srRadioSearchReplaceClick = function(obj, we_replace) {
	this.we_replace = we_replace;
	// update search window content
	this.showSearchWindow();
}

// text fields for search and replace
WikkaEdit.prototype.srFieldSearchReplaceChange = function(obj) {
	switch (obj.id) {
		case "sr_field_searchFor" : this.we_searchFor = obj.value; break;
		case "sr_field_replaceWith" : this.we_replaceWith = obj.value; break;
		default : alert("srFieldSearchReplaceChange() : id=" + obj.id);
	}
}

WikkaEdit.prototype.srFieldSearchReplaceKeyPress = function(e) {
	if (e == null) e = window.event;
	var key = (e.keyCode != null ? e.keyCode : e.which);
	if (key == 13) { // enter
		varWikkaEdit.we_ta.focus(); // blur() the textfield to call its onchange()
		varWikkaEdit.srBtnFindNextClick();
		return varWikkaEdit.cancelKey();
	}
	return true;
}

// options checkboxes
WikkaEdit.prototype.srCheckboxClick = function(obj) {
	switch (obj.id) {
		case "sr_check_regexp" : this.we_regexp = obj.checked; break;
		case "sr_check_case" : this.we_case = obj.checked; break;
		case "sr_check_whole" : this.we_whole = obj.checked; break;
		case "sr_check_cursor" : this.we_cursor = obj.checked; break;
		case "sr_check_reverse" : this.we_reverse = obj.checked; break;
		case "sr_check_prompt" : this.we_prompt = obj.checked; break;
		default : alert("srCheckboxClick() : id=" + obj.id);
	}
	// update search window content
	this.showSearchWindow();
}

// "find next" or "replace" button
WikkaEdit.prototype.srBtnFindNextClick = function() {
	this.findNext();
}

// "cancel" button
WikkaEdit.prototype.srBtnCancelClick = function() {
	this.hideSearchWindow();
}

// ===== does the current selection match the criteria? =====
WikkaEdit.prototype.checkIfMatch = function(searched, text, start, end) {
	// check the word itseft
	if (text.substring(start, end) != searched)
		return false;
	// if "whole word" option is not selected, no need to do additional tests
	if (!this.we_whole)
		return true;
	// check for leading and trailing chars
	// note : charBefore and charAfter can be ""
	var charBefore = (start < 1 ? "" : text.substr(start - 1, 1));
	var charAfter = text.substr(end, 1);
	var errBefore = ((charBefore >= "0") && (charBefore <= "9")) || ((charBefore >= "a") && (charBefore <= "z")) || ((charBefore >= "A") && (charBefore <= "Z"));
	var errAfter = ((charAfter >= "0") && (charAfter <= "9")) || ((charAfter >= "a") && (charAfter <= "z")) || ((charAfter >= "A") && (charAfter <= "Z"));
	return !(errBefore || errAfter);
}

// ===== find the next occurence or replace selection =====
WikkaEdit.prototype.findNext = function() {
	var searchfor = this.we_searchFor;

	// empty search field => removes selection and does nothing
	if (searchfor == "") {
		this.we_ta.focus();
		this.setSelectionRange(0);
		return;
	}

	// position of the selection
	var sr = this.getSelectionRange();
	// textarea content
	var text = this.getTextAreaContent();

	// duplicate these variables to simplify search in case sensitive or not
	var textC = text, searchforC = searchfor;
	if (!this.we_case) {
		textC = textC.toLowerCase();
		searchforC = searchforC.toLowerCase();
	}

	var currentSelectionMatch = this.checkIfMatch(searchforC, textC, sr.start, sr.end);

	if ((this.we_replace) && (currentSelectionMatch)) {
		// selection content
		var selCont = this.getSelectionContent(sr);
		// update textarea content & move selection
		var newSr = new SelRange(sr.start, sr.start + this.we_replaceWith.length);
		this.setTextAreaContent(selCont.before + this.we_replaceWith + selCont.after, newSr);
		// as the textarea content has change, textC, sr... are not up to date so
		// we restart at the beginning of the function
		this.findNext();
		return;
	}

	// depending on the "from cursor" option, the initial position can be reset
	var newSrStart = (this.we_cursor ? sr.start : 0);

	// search for the next occurence
	var finished = false;
	while (!finished) {
		if (!this.we_reverse)
			newSrStart = textC.indexOf(searchforC, newSrStart + (currentSelectionMatch ? 1 : 0));
		else
			newSrStart = textC.lastIndexOf(searchforC, newSrStart - (currentSelectionMatch ? 1 : 0));
		if (newSrStart == -1) {					// not found
			finished = true;
			this.we_lastMsg = "no other occurence";
		} else if (!this.we_whole) {			// found => ok
			finished = true;
		} else {								// found but is this a whole word?
			finished = this.checkIfMatch(searchforC, textC, newSrStart, newSrStart + searchfor.length);
			// not a whole word => need to move to the next character to avoid an infinite loop
			if (!finished)
				newSrStart++;
		}
	}

	// update search window
	this.showSearchWindow();

	this.we_ta.focus();

	if (newSrStart == -1) {
		// no other occurence
		// - the latest match is still highlited? => ok
		// - the current selection ha nothing to do with the searched string => unselect
		if (!currentSelectionMatch)
			this.setSelectionRange(sr.start, sr.start);
	} else {
		// new occurence found
		// => update selection
		this.setSelectionRange(newSrStart, newSrStart + searchfor.length);

		// scroll the textarea to the selection
		// - on ie6+ and konqueror, this is done automatically
		// - on opera, it can't be done :(
		// - on gecko and webkit, the following hack is needed
		if (this.we_gecko || this.we_webkit) {
			var windowWidth = parseInt(window.innerWidth ? window.innerWidth : document.documentElement.clientWidth, 10);
			var TEXTAREA_MARGINS_LR = 26;	// TODO : hardcoded value : 26 is approximatly the left and right margins of textarea
			var taWidth = windowWidth - TEXTAREA_MARGINS_LR;
			var taHeight = parseInt(this.we_ta.style.height, 10);

			// duplicate real textarea to we_searchTa but limit it's value from the start to the selection
			this.we_searchTa.style.width = taWidth + "px";
			this.we_searchTa.style.height = taHeight + "px";
			this.we_searchTa.value = text.substr(0, newSrStart);

			// the textarea has to scroll down?
			if (this.we_ta.scrollTop < this.we_searchTa.scrollHeight - taHeight) {
				this.addLog("scroll down");
				this.we_ta.scrollTop = this.we_searchTa.scrollHeight - taHeight;
			}

			// the textarea has to scroll up?
			var LINE_HEIGHT = 20; // TODO : hardcoded value : line height (approximatly 20px)
			this.we_searchTa.style.height = LINE_HEIGHT + "px";
			if (this.we_ta.scrollTop > this.we_searchTa.scrollHeight - LINE_HEIGHT) {
				this.addLog("scroll up");
				this.we_ta.scrollTop = this.we_searchTa.scrollHeight - LINE_HEIGHT;
			}
		}

		// "replace all"? => loop again
		if ((this.we_replace) && (!this.we_prompt))
			this.findNext();
	}
}

var buttonsDisabledArray = null;
// ===== display or update search & replace window =====
WikkaEdit.prototype.showSearchWindow = function() {
	var html = "", y=24;

	//html += "<form action='javascript:alert(1);' onsubmit='javascript:alert(2);'>";

	// window title (and close button)
	html += "<div id='sr_window_title'>Search / Replace</div>";
	html += "<div style='position:absolute;left:479px;top:1px' onclick='varWikkaEdit.hideSearchWindow();'><img src='3rdparty/plugins/wikkaedit/images/close_window.png'></div>";

	html += "<div style='position:absolute;left:10px;top:"+(y+10)+"px;width:480px;height:"+(this.we_replace?61:40)+"px;border:1px solid gray;-moz-border-radius:7px'></div>";
	html += "<div style='position:absolute;left:20px;top:"+y+"px;background-color:#f0f0ee;padding:0 3px 0 3px'><table border='0' cellspacing='0' cellpadding='0'><tr><td><input type='radio' id='sr_radio_search' name='sr_radio_sr' onclick='varWikkaEdit.srRadioSearchReplaceClick(this,false);'"+(this.we_replace?"":" checked")+"></td><td><label for='sr_radio_search'>Search</label></td><td width='10'></td><td><input type='radio' id='sr_radio_replace' name='sr_radio_sr' onclick='varWikkaEdit.srRadioSearchReplaceClick(this,true);'"+(this.we_replace?" checked":"")+"></td><td><label for='sr_radio_replace'>Replace</label></td></tr></table></div>";
	html += "<div style='position:absolute;left:20px;top:"+(y+22)+"px'>";
	html += "<table border='0' cellspacing='0' cellpadding='0'>";
	html += "<tr><td style='width:100px;white-space:nowrap;padding-right:5px'>Search for :</td><td><input type='text' id='sr_field_searchFor' style='width:250px' onchange='varWikkaEdit.srFieldSearchReplaceChange(this);'></td><td style='padding-left:8px'><input type='checkbox' id='sr_check_regexp' onclick='varWikkaEdit.srCheckboxClick(this);'" + (this.we_regexp ? " checked" : "") + " disabled></td><td><label for='sr_check_regexp' title='regular expression' style='color:grey'>regexp</label></td></tr>";
	if (this.we_replace) {
		html += "<tr><td style='white-space:nowrap;padding-right:5px'>Replace with :</td><td><input type='text' id='sr_field_replaceWith' style='width:250px' onchange='varWikkaEdit.srFieldSearchReplaceChange(this);'></td><td></td><td></td></tr>";
	}
	y += 24;
	html += "</table>";
	html += "</div>";

	html += "<div style='position:absolute;left:10px;top:"+(y+59)+"px;width:480px;height:42px;border:1px solid gray;-moz-border-radius:7px'></div>";
	html += "<div style='position:absolute;left:20px;top:"+(y+49)+"px;background-color:#f0f0ee;padding:0 3px 0 3px'>Options</div>";

	html += "<div style='position:absolute;left:20px;top:"+(y+65)+"px'><table border='0' cellspacing='0' cellpadding='0'><tr><td><input type='checkbox' id='sr_check_case' onclick='varWikkaEdit.srCheckboxClick(this);'" + (this.we_case ? " checked" : "") + "></td><td><label for='sr_check_case'>case sensitive</label></td></tr></table></div>";

	html += "<div style='position:absolute;left:20px;top:"+(y+80)+"px'><table border='0' cellspacing='0' cellpadding='0'><tr><td><input type='checkbox' id='sr_check_whole' onclick='varWikkaEdit.srCheckboxClick(this);'" + (this.we_whole ? " checked" : "") + "></td><td><label for='sr_check_whole'>whole word</label></td></tr></table></div>";

	html += "<div style='position:absolute;left:190px;top:"+(y+65)+"px'><table border='0' cellspacing='0' cellpadding='0'><tr><td><input type='checkbox' id='sr_check_cursor' onclick='varWikkaEdit.srCheckboxClick(this);'" + (this.we_cursor ? " checked" : "") + "></td><td><label for='sr_check_cursor'>from cursor</label></td></tr></table></div>";

	html += "<div style='position:absolute;left:190px;top:"+(y+80)+"px'><table border='0' cellspacing='0' cellpadding='0'><tr><td><input type='checkbox' id='sr_check_reverse' onclick='varWikkaEdit.srCheckboxClick(this);'" + (this.we_reverse ? " checked" : "") + "></td><td><label for='sr_check_reverse'>reverse search</label></td></tr></table></div>";

	if (this.we_replace)
		html += "<div style='position:absolute;left:360px;top:"+(y+65)+"px'><table border='0' cellspacing='0' cellpadding='0'><tr><td><input type='checkbox' id='sr_check_prompt' onclick='varWikkaEdit.srCheckboxClick(this);'" + (this.we_prompt ? " checked" : "") + "></td><td><label for='sr_check_prompt'>prompt</label></td></tr></table></div>";

	var titleBtnOk = (!this.we_replace ? "Find next" : (this.we_prompt ? "Replace" : "Replace all"));
	html += "<div style='position:absolute;left:308px;top:"+(y+107)+"px'><input type='button' value='" + titleBtnOk + "' onclick='varWikkaEdit.srBtnFindNextClick();' style='width:90px'></div>";
	html += "<div style='position:absolute;left:402px;top:"+(y+107)+"px'><input type='button' value='Cancel' onclick='varWikkaEdit.srBtnCancelClick();' style='width:90px'></div>";

	html += "<div style='position:absolute;left:20px;top:"+(y+107)+"px;color:red'>" + this.we_lastMsg + "</div>";

	html += "</div>";

	//html += "</form>";

	this.we_lastMsg = "";

	this.we_search.innerHTML = html;
	document.getElementById("sr_field_searchFor").value = this.we_searchFor;
	document.getElementById("sr_field_searchFor").onkeypress = varWikkaEdit.srFieldSearchReplaceKeyPress;
	if (this.we_replace) {
		document.getElementById("sr_field_replaceWith").value = this.we_replaceWith;
		document.getElementById("sr_field_replaceWith").onkeypress = varWikkaEdit.srFieldSearchReplaceKeyPress;
	}
	this.we_search.style.visibility = "";
	document.getElementById("sr_field_searchFor").select();

	// disable form submit when "enter" key is pressed
	//document.getElementById("").onKeyDown = this.disableEnterKey;
	//document.getElementById().onKeyDown = this.disableEnterKey;
	//document.getElementById().onKeyDown = this.disableEnterKey;
	//document.onkeydown = this.disableEnterKey;
	//var elts = document.getElementsByTagName("form");
	//for(var i in elts)
	//	this.addLog(i);
	//this.addLog(document.forms[0].action);
	if (buttonsDisabledArray == null) {
		var idForm = null;
		for(var i=0; i<document.forms.length; i++) {
			if (document.forms[i].action.indexOf("/edit") != -1) {
				if (idForm == null)
					idForm = i;
				else
					this.addLog("idForm already set !");
			}
		}
		if (idForm == null) {
			this.addLog("idForm = null !");
		} else {
			buttonsDisabledArray = new Array();
			var elt;
			for(i=0; i<document.forms[idForm].elements.length; i++) {
				elt = document.forms[idForm].elements[i];
				if ((elt.type != null) && (elt.type == "submit")) {
					buttonsDisabledArray.push(elt);
					elt.disabled = true;
					elt.style.color = "gray";
				}
			//document.getElementById("edit_store_button").disabled = true;
			//document.getElementById("edit_preview_button").disabled = true;
			}
		}
	}
}

// ===== hide search & replace window =====
WikkaEdit.prototype.hideSearchWindow = function() {
	this.we_search.style.visibility = "hidden";

	// re-enable the "enter" key
	if (buttonsDisabledArray != null) {
		for(var i=0; i<buttonsDisabledArray.length; i++) {
			buttonsDisabledArray[i].disabled = false;
			buttonsDisabledArray[i].style.color = "";
		}
	}
	buttonsDisabledArray = null;
	//document.onkeydown = "";

	this.we_ta.focus();
}
