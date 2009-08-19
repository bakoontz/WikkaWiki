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


// ===== initialization =====
WikkaEdit.prototype.init = function() {
	// load CSS
	document.write("<link rel=\"stylesheet\" type=\"text/css\" href=\"3rdparty/plugins/wikkaedit/wikkaedit.css\" />");

	// init data
	this.we_actionsMenuEnabled = (typeof(this.contextualHelp) != "undefined");
	this.we_searchReplaceEnabled = (typeof(this.showSearchWindow) != "undefined");
	this.initButtons();
	if (this.we_actionsMenuEnabled) {
		this.initCategs();
		this.initActions();
	}

	//var we_ta_container = this.we_ta;		// without textarea_container (old method)
	var we_ta_container = document.getElementById("textarea_container");	// with textarea_container

	// add a toolbar before textarea
	this.we_toolbar = document.createElement("div");
	this.we_toolbar.id = "wikkatoolbar";
	this.we_toolbar.innerHTML = this.genToolbar();
	we_ta_container.parentNode.insertBefore(this.we_toolbar, we_ta_container);

	// submenu
	this.we_submenu = document.createElement("div");
	this.we_submenu.id = "wikkasubmenu";
	this.we_submenu.style.visibility = "hidden";
	we_ta_container.parentNode.insertBefore(this.we_submenu, we_ta_container); //this.we_help.nextSibling);

	// search & replace
	if (this.we_searchReplaceEnabled) {
		// search & replace window
		this.we_search = document.createElement("div");
		this.we_search.id = "wikkasearch";
		this.we_search.style.visibility = "hidden"
		this.we_search.style.width = "500px"; // width & height can't be moved to the CSS, else JS can't read these values later
		this.we_search.style.height = "180px";
		we_ta_container.parentNode.insertBefore(this.we_search, we_ta_container.nextSibling);

		// dummy textarea, used to scroll the textarea to the right position
		// it is hidden, but it needs to be inserted to the page to work
		this.we_searchTa = document.createElement("textarea");
		this.we_searchTa.style.position = "absolute";
		this.we_searchTa.style.left = 0;
		this.we_searchTa.style.top = 0;
		this.we_searchTa.style.visibility = "hidden";
		this.we_searchTa.style.scroll = "auto";
		we_ta_container.parentNode.insertBefore(this.we_searchTa, we_ta_container.nextSibling);
	}

	// actions
	if (this.we_actionsMenuEnabled) {
		// help
		this.we_helpPreviousContent = "&nbsp;";
		this.we_help = document.createElement("div");
		this.we_help.id = "wikkahelp";
		this.we_help.innerHTML = this.we_helpPreviousContent;	// contains at least a character to keep constant height
		this.we_help.style.display = (this.we_actionsMenuEnabled ? "" : "none");
		we_ta_container.parentNode.insertBefore(this.we_help, we_ta_container.nextSibling.nextSibling);	// next*2 to skip following <br/>
		// tooltip
		this.we_tooltip = document.createElement("div");
		this.we_tooltip.id = "wikkatooltip";
		this.we_tooltip.style.visibility = "hidden"
		we_ta_container.parentNode.insertBefore(this.we_tooltip, this.we_help.nextSibling);
	}

	// log (debug)
	this.we_log = document.createElement("div");
	this.we_log.id = "wikkalog";
	this.we_log.innerHTML = "log<br/>";
	this.we_log.style.display = "none";
	we_ta_container.parentNode.insertBefore(this.we_log, we_ta_container.nextSibling);

	// make sure nothing is selectionned in the textarea
	this.setSelectionRange(0, 0);
	// intercept special keys (tab...)
	this.we_ta.onkeydown = this.keyDown;
	this.we_ta.onmousedown = this.mouseDown;
	// focus on the textarea
	this.we_ta.focus();

	// editor height follow browser window height
	setInterval("varWikkaEdit.moveElementsAfterWindowResize();", this.EDITOR_HEIGHT_POLL_INTERV);
	// help for the command under the carret
	if (this.we_actionsMenuEnabled)
		setInterval("varWikkaEdit.contextualHelp();", this.CONTEXTUAL_HELP_POLL_INTERV);
}

// ===== return true if the browser is supported =====
WikkaEdit.prototype.browserSupported = function() {
	var ok = false;
	try {
		var w3 = (this.we_ta.selectionStart != null);
		var ie6 = (document.selection && document.selection.createRange);
		if (w3 || ie6)
			ok = true;
	} catch(e) {
	}
	return ok;
}

// ===== move & resize some elements when the browser window is resized =====
WikkaEdit.prototype.moveElementsAfterWindowResize = function() {
	// when unloading page, this function may throw errors so the try/catch is necessarry
	try {
		// make editor height follow browser window height
		if (this.we_ta.style.height == "") this.we_ta.style.height = "500px"; // this.we_ta.style.height is "" the 1st time
		var windowHeight = parseInt(window.innerHeight ? window.innerHeight : document.documentElement.clientHeight, 10);
		var change = windowHeight - parseInt(document.body.offsetHeight, 10) - 20;	// TODO remove this hardcoded value (needed for vertical scrollbar to disappear)
		if (change != 0) {
			var newheight = Math.max(this.EDITOR_MIN_HEIGHT, parseInt(this.we_ta.style.height, 10) + change);
			this.we_ta.style.height = newheight + "px";
		}

		// keep search & replace window on the bottom of the window
		if (this.we_searchReplaceEnabled) {
			var windowWidth = parseInt(window.innerWidth ? window.innerWidth : document.documentElement.clientWidth, 10);
			this.we_search.style.left = (windowWidth / 2 - parseInt(this.we_search.style.width,10) / 2) + "px";
			this.we_search.style.top = (windowHeight - parseInt(this.we_search.style.height,10) - 1) + "px";
		}
	} catch(e) {
	}
}

// ===== generate HTML code for toolbar =====
WikkaEdit.prototype.genToolbar = function() {
	// ie6 dosn't understand CSS hover on images, so a JS hack is used
	var ie6_hover = (navigator.userAgent.indexOf("MSIE 6.0") == -1 ? "" : " onmouseover=\"this.className='toolbutton_hover';\" onmouseout=\"this.className='toolbutton_std';\"");

	// ========== main buttons ==========
	var html = "";
	for(var i in this.we_buttons) {
		switch (this.we_buttons[i].type) {
			case "button" :		// button
				html += "<img class=\"toolbutton\" src=\"3rdparty/plugins/wikkaedit/images/" + this.refToName(i) + ".gif\" alt=\"" + this.refToName(i) + "\" title=\"" + this.we_buttons[i].title + "\" onclick=\"varWikkaEdit.toolbarButtonClick(this, '" + this.refToName(i) + "');\"" + ie6_hover + " />";
				break;
			case "submenu" :	// submenu
				html += "<img class=\"toolbutton\" src=\"3rdparty/plugins/wikkaedit/images/submenu.gif\" alt=\"submenu\" title=\"" + this.we_buttons[i].title + "\" onclick=\"varWikkaEdit.toolbarButtonClick(this, '" + this.refToName(i) + "');\"" + ie6_hover + " />";
				break;
			case "separator" :	// separator
				html += "<img class=\"toolseparator\" src=\"3rdparty/plugins/wikkaedit/images/separator.gif\" alt=\"|\" />";
				break;
			default :
				alert("genToolbar() : unknown type (" + this.we_buttons[i].type + ")");
		}
	}

	// ========== actions ==========
	if (this.we_actionsMenuEnabled) {
		html += "<div class='toolhorizsep'></div>";
		html += "<span class='actiontitle'>Actions</span>";
		for(i in this.we_categs) {
			if (this.we_categs[i].title != null)	// hidden actions don't have a title
				html += "<span class=\"toolbutton\" style='margin-left:20px; padding:2px 5px 3px 6px' onclick=\"varWikkaEdit.toolbarCategClick('" + i + "', this);\">" + this.we_categs[i].title + "<img src='3rdparty/plugins/wikkaedit/images/submenu.gif' style='vertical-align:middle'/></span>";
		}
	}

	return html;
}

// ===== a main button (or submenu) is pressed? =====
WikkaEdit.prototype.toolbarButtonClick = function(obj, buttonName, submenuName) {
	// buttonName is the button pressed
	// obj is a pointer to the button image object (used to detect X & Y coordinates)
	// obj can be null too (when toolbarButtonClick() is called by a keyboard shortcut for example)

	// hide a possible opened menu
	if (((submenuName != null) || (this.we_buttons[this.nameToRef(buttonName)].type != "submenu")) && (buttonName != "shortcuts"))
		this.hideSubmenu();

	switch (buttonName) {
		case "h1" :			this.addToLine("======", "======"); break;
		case "h2" :			this.addToLine("=====", "====="); break;
		case "h3" :			this.addToLine("====", "===="); break;
		case "h4" :			this.addToLine("===", "==="); break;
		case "h5" :			this.addToLine("==", "=="); break;

		case "bold" :		this.addToSelection("**", "**"); break;
		case "italic" :		this.addToSelection("//", "//"); break;
		case "underline" :	this.addToSelection("__", "__"); break;
		case "strike" :		this.addToSelection("++", "++"); break;
		case "style" :		this.toggleSubmenu(obj, "style"); break;

		case "justifycenter" :	this.addToLine("@@", "@@"); break;
		case "bullist" :	this.addToLine("\t- "); break;
		case "numlist" :	this.addToLine("\t1) "); break;
		case "comments" :	this.addToLine("\t& "); break;

		case "indent" :		this.indent(1); break;
		case "outdent" :	this.indent(-1); break;

		case "hr" :			this.addToSelection("----"); break;

		case "link" :		this.addToSelection("[[http://www.example.com Page Title]]"); break;
		case "image" :		this.toolbarActionClick("we_image"); break;
		case "table" :		this.addToSelection("|=|header1|=|header2||\n||cell1||cell2||");										// 1.2
							break;
		case "rawhtml" :	this.addToSelection("\"\"insert-raw-html-here\"\""); break;
		case "sourcecode" :	this.addToSelection("%%(language-ref)\ninsert-source-code-here\n%%"); break;

		case "find" :		this.showSearchWindow(); break;
		case "shortcuts" :	this.toggleSubmenu(obj, "shortcuts"); break;
		case "formatting_rules" :	this.showFormattingRules(); break;

		// submenu : style
		case "forecolor" :	this.toolbarActionClick("we_color"); break;
		case "monospace" :	this.addToSelection("##", "##"); break;
		case "highlight" :	this.addToSelection("''", "''"); break;
		case "key" :		this.addToSelection("#%", "#%"); break;
		case "leftfloat" :	this.addToLine("<<", "<<"); break;
		case "rightfloat" :	this.addToLine(">>", ">>"); break;

		default : alert("toolbarButtonClick() : unknown buttonName (" + buttonName + ")");
	}
}

// ===== show formatting rules (open a new window) =====
WikkaEdit.prototype.showFormattingRules = function() {
	var newUrl = window.location.href;
	newUrl = newUrl.replace(/[a-zA-Z0-9]+\/edit.*$/, "FormattingRules");
	window.open(newUrl, "wikka_formatting_rules");
}

// ===== an action category is pressed? =====
WikkaEdit.prototype.toolbarCategClick = function(ref, obj) {
	this.toggleSubmenu(obj, ref, true);
}

// ===== an action button is pressed? =====
// even when actions are not enabled, some basic actions (image...) are still available
WikkaEdit.prototype.toolbarActionClick = function(actionRef) {
	// hide a possible opened menu
	this.hideSubmenu();

	var actionAndParams = "";
	if (this.we_actionsMenuEnabled) {
		// === use actions data to insert the action ===
		// add the action and default parameters to the textarea
		var actionName = this.we_actions[actionRef].we_name;
		var OP;
		actionAndParams += "{{" + actionName;
		for(var i in this.we_actions[actionRef].we_params) {
			OP = this.we_actions[actionRef].we_params[i];
			if ((OP.nodefault == null) || (OP.nodefault == false))	// null or hide => add param
				actionAndParams += " " + OP.name + "=\"" + OP.value + "\"";
		}
		actionAndParams += "}}";
	} else {
		// === use static text to insert the action ===
		switch (actionRef) {
			case "we_image" :
				actionAndParams = "{{image url=\"url\" title=\"text\" alt=\"text\"}}";
				break;
			case "we_color" :
				actionAndParams = "{{color text=\"text\" c=\"color\"}}";
				break;
			default :
				alert("toolbarActionClick() : actionRef=" + actionRef);
		}
	}
	this.addToSelection(actionAndParams);
}

// ===== hide a submenu (when necessary) =====
WikkaEdit.prototype.hideSubmenu = function() {
	this.we_submenu.style.visibility = "hidden";
}

// ===== display / hide a submenu =====
// 3 kind of submenus :
// - text style
// - shortcuts summary
// - actions list
WikkaEdit.prototype.toggleSubmenu = function(obj, newSubmenu, isAction) {
	isAction = (isAction == true);
	if ((newSubmenu == this.we_currentSubmenu) && (this.we_submenu.style.visibility != "hidden")) {
		this.hideSubmenu();
		this.we_ta.focus();
		return;
	}

	// the submenu position is based on the pressed button position
	var coords = this.getObjectCoords(obj);
	coords.y += 25;		// TODO remove this hardcoded value (toolbar height)
	this.we_submenu.style.left = coords.x + "px";
	this.we_submenu.style.top = coords.y + "px";

	var html = "";

	if (newSubmenu == "shortcuts") {
		// ===== shortcuts summary submenu =====

		var SHORTCUTS_SUMMARY_WIDTH = 350;
		html += "<div style='width:" + SHORTCUTS_SUMMARY_WIDTH + "px' onclick='varWikkaEdit.hideSubmenu();'>";
		html += "<div id='sr_window_title'>Editor shortcuts</div>";
		html += "<div style='padding:5px 5px 10px 10px; line-height:200%'>";
		html += "<span class='we_key'>Ctrl</span> + <span class='we_key'>B</span> : bold<br/>";
		html += "<span class='we_key'>Ctrl</span> + <span class='we_key'>I</span> : italic<br/>";
		html += "<span class='we_key'>Ctrl</span> + <span class='we_key'>U</span> : underline<br/>";
		html += "<span class='we_key'>Ctrl</span> + <span class='we_key'>Shift</span> + <span class='we_key'>S</span> : strike<br/>";
		html += "<br/>";
		html += "<span class='we_key'>Tab</span> : tab character or indent<br/>";
		html += "<span class='we_key'>Shift</span> + <span class='we_key'>Tab</span> : outdent<br/>";
		html += "<br/>";
		// Ctrl + F is not intercepted by Safari
		if (!this.we_webkit) {
			html += "<span class='we_key'>Ctrl</span> + <span class='we_key'>F</span> : search &amp; replace<br/>";
			html += "<br/>";
		}
		html += "<span class='we_key'>Esc</span> : release focus<br/>";
		html += "</div>";
		html += "</div>";

		// move the submenu to the left of the button
		this.we_submenu.style.left = (coords.x - SHORTCUTS_SUMMARY_WIDTH + 18) + "px"; // TODO remove this hardcoded value (button width)

	} else {
		// ===== submenu containing buttons =====

		// ie6 dosn't understand CSS hover on images, so a JS hack is used
		var ie6_hover = (navigator.userAgent.indexOf("MSIE 6.0") == -1 ? "" : " onmouseover=\"this.className='toolbutton_hover';\" onmouseout=\"this.className='toolbutton_std';\"");
		var i;
		if (!isAction) {
			for(i in this.we_buttons[this.nameToRef(newSubmenu)].we_buttons) {
				html += "<div class=\"smbutton\" onclick=\"varWikkaEdit.toolbarButtonClick(this, '" + this.refToName(i) + "', '" + newSubmenu + "');\">";
				html += "<img class=\"smimage\" src=\"3rdparty/plugins/wikkaedit/images/" + this.refToName(i) + ".gif\" alt=\"" + this.refToName(i) + "\"" + ie6_hover + " />";
				html += this.we_buttons[this.nameToRef(newSubmenu)].we_buttons[i].title;
				html += "</div>";
			}
		} else {
			for(i in this.we_actions) {
				if (this.we_actions[i].categ == newSubmenu) {
					html += "<div class=\"smbutton\" onclick=\"varWikkaEdit.toolbarActionClick('" + i + "');\"" + (this.we_actions[i].summary == null ? "" : " title=\"" + this.we_actions[i].summary + "\"") + ">";
					html += "<img class=\"smimage\" src=\"3rdparty/plugins/wikkaedit/images/" + this.we_actions[i].name + ".gif\" alt=\"" + this.we_actions[i].name + "\"" + ie6_hover + " />";
					html += this.we_actions[i].we_title;
					html += "</div>";
				}
			}
		}

	}

	this.we_submenu.innerHTML = html;
	this.we_submenu.style.visibility = "";
	this.we_currentSubmenu = newSubmenu;
}

// ===== mouse clicked? =====
WikkaEdit.prototype.mouseDown = function(e) {
	// take care : this != varWikkaEdit
	varWikkaEdit.hideSubmenu();
}

// ===== document - key pressed =====
// when search & replace window is opened, the user may want to user "enter" key but this one send the main edit.php <form>
// => hack : the "enter key" is simply disabled
/*WikkaEdit.prototype.disableEnterKey = function(e) {
	if (e == null) e = window.event;
	var key = (e.keyCode != null ? e.keyCode : e.which);
	if (key == 13) { // enter
		varWikkaEdit.addLog("cancelling 'enter' key event");
		return varWikkaEdit.cancelKey();
	}
}*/

// ===== textarea - key pressed? =====
// used for shortcuts and special keys (enter, tab)
WikkaEdit.prototype.keyDown = function(e) {
	// take care : this != varWikkaEdit
	if (!varWikkaEdit.we_canCancelKeyEvent)
		return true;
	if (e == null) e = window.event;
	var key = (e.keyCode != null ? e.keyCode : e.which);
	//varWikkaEdit.addLog("e.ctrlKey="+e.ctrlKey + ", key=" +key);
	switch (key) {
		case 9 :	// tab
			if (e.shiftKey) {
				varWikkaEdit.indent(-1);		// outdent
			} else {
				var sr = varWikkaEdit.getSelectionRange();
				if (sr.start == sr.end) {
					// insert tab character and move carret after it
					var srAfter = new SelRange(sr.end + 1);
					varWikkaEdit.addToSelection("\t", null, srAfter);
				} else {
					varWikkaEdit.indent(1);		// indent
				}
			}
			return varWikkaEdit.cancelKey(e);
		case 13 :	// enter
			var keepEvent = varWikkaEdit.autoIndent();
			if (keepEvent)
				return true;
			else
				return varWikkaEdit.cancelKey(e);
		case 27 :	// escape
			// TODO select the "note" textfield or the "store" button (when notes are disabled)
			varWikkaEdit.we_ta.blur();
			return varWikkaEdit.cancelKey(e);

	}
	if (e.ctrlKey) {	// to improve speed, we first check that ctrl is pressed
		switch (key) {
			case 66 :	// ctrl + B
				varWikkaEdit.toolbarButtonClick(null, "bold");
				return varWikkaEdit.cancelKey(e);
			case 73 :	// ctrl + I
				varWikkaEdit.toolbarButtonClick(null, "italic");
				return varWikkaEdit.cancelKey(e);
			case 85 :	// ctrl + U
				varWikkaEdit.toolbarButtonClick(null, "underline");
				return varWikkaEdit.cancelKey(e);
			case 83 :	// ctrl (+ shift) + S
				if (e.shiftKey) {
					varWikkaEdit.toolbarButtonClick(null, "strike");
					return varWikkaEdit.cancelKey(e);
				}
				break;
			case 70 :	// ctrl + F
				if (varWikkaEdit.we_searchReplaceEnabled) {
					varWikkaEdit.showSearchWindow();
					return varWikkaEdit.cancelKey(e);
				}
				break;
		}
	}
	return true;
}

// ===== textarea - special key pressed? =====
// keyup event is the only reliable way to detect special key (shift, ctrl, alt)
/*WikkaEdit.prototype.keyUp = function(e) {
	// take care : this != varWikkaEdit
	if (e == null) e = window.event;
	var key = (e.keyCode != null ? e.keyCode : e.which);
	if ((e.ctrlKey) && (e.altKey))
		varWikkaEdit.we_ta.blur();
}*/

// ===== cancel key event =====
WikkaEdit.prototype.cancelKey = function(e) {
	// the following lines were disabled as "return false" seems enough
	//e.returnValue = false;
	//e.cancelBubble = true;
	//e.stopPropagation();
	//e.preventDefault();
	return false;
}

// ===== autoindent feature =====
// when previous line was indented, try to align the new one
// keep marker too eg "1." or "I)"
// - return true if the browser should add the newline itseft
// - return false if javascript has already done the job
WikkaEdit.prototype.autoIndent = function() {
	// position of the selection
	var sr = this.getSelectionRange();
	// selection content
	var selCont = this.getSelectionContent(sr);
	// 3 types of indent characters : tab, 4 spaces or "~" (http://demo.wikkawiki.org/FormattingRules)
	var sp = "(    +|\t+|~+)";
	// indent markers : "- ", "& ", "a)", "a." (+ uppercase + numbers)
	var it = "(\-|\&|([1-9][0-9]*|[a-zA-Z])([.]|[)])|)";
	// does previous line contain indentation?
	var re = new RegExp("^" + sp + it + "(.*)$");
	var m = selCont.previousLine.match(re);
	// yes
	if (m) {
		var indentChars = m[1];
		var marker = m[2];
		var currentLineContent = m[5].replace(/\s+/g, "");
		var newBeforeSel = selCont.before + "\n" + indentChars + marker + (marker == "" ? "" : " ");
		// update textarea content & move selection
		// (note : after emptying a line, cursor goes to the previous line. It's not perfect but we
		// can't improve this as setSelectionRange() is intended to move selection, not caret position)
		var newSr = new SelRange(newBeforeSel.length);
		this.setTextAreaContent(newBeforeSel + selCont.after, newSr);
		return false;
	}
	// no => exit
	return true;
}

// ===== add some text to the selection =====
WikkaEdit.prototype.addToSelection = function(leftTag, rightTag, srAfter, srBefore) {
	if (timeoutSelectionRange != null) {
		this.addLog("addToSelection() was called, but setSelectionRange() setTimeout() is not finished");
		//return;
	}
	if (rightTag == null) rightTag = "";
	// position of the selection
	var sr = (srBefore != null ? srBefore : this.getSelectionRange());
	// selection content
	var selCont = this.getSelectionContent(sr);
	// intelligent carriage return for separator
	if (leftTag == "----") {
		if ((selCont.before != "") && (selCont.before.substr(selCont.before.length-1) != "\n"))
			leftTag = "\n" + leftTag;
		if ((selCont.after != "") && (selCont.after.substr(0, 1) != "\n"))
			leftTag += "\n";
	}
	// update textarea content & move selection
	var newSr;
	if (srAfter != null)
		newSr = srAfter;
	else
		newSr = new SelRange(sr.start + leftTag.length, sr.end + leftTag.length);
	this.setTextAreaContent(selCont.before + leftTag + selCont.sel + rightTag + selCont.after, newSr);
}

// ===== add some text to the current line(s) =====
WikkaEdit.prototype.addToLine = function(leftTag, rightTag) {
	if (timeoutSelectionRange != null) {
		this.addLog("addToLine() was called, but setSelectionRange() setTimeout() is not finished");
		//return;
	}
	if (rightTag == null) rightTag = "";
	// position of the selection (extended to whole lines)
	var sr = this.getSelectionRangeWholeLines();
	// selection content
	var selCont = this.getSelectionContent(sr);
	// real selection
	sr = this.getSelectionRange();
	// add left and right tags for each line
	var arr = selCont.sel.split("\n");
	var newSel = "";
	for(var i=0; i<arr.length; i++) {
		newSel += leftTag + arr[i] + rightTag + (i < arr.length-1 ? "\n" : "");
		if (i == 0)
			sr.start += leftTag.length;
		sr.end += leftTag.length;
	}
	// update textarea content & move selection
	this.setTextAreaContent(selCont.before + newSel + selCont.after, sr);
}

// ===== indent / outdent current line(s) =====
WikkaEdit.prototype.indent = function(sens) {
	if (timeoutSelectionRange != null) {
		this.addLog("addToLine() was called, but setSelectionRange() setTimeout() is not finished");
		//return;
	}
	// position of the selection (extended to whole lines)
	var sr = this.getSelectionRangeWholeLines();
	// selection content
	var selCont = this.getSelectionContent(sr);
	// real selection
	sr = this.getSelectionRange();
	// indent / outdent each line
	var arr = selCont.sel.split("\n");
	var newSel = "";
	for(var i=0; i<arr.length; i++) {
		if (sens == -1) {	// outdent
			if (arr[i].substr(0,1) == "\t") {
				// remove leading tab
				newSel += arr[i].substr(1) + (i < arr.length-1 ? "\n" : "");
				if (i == 0)
					sr.start--;
				sr.end--;
			} else {
				// keep this line
				newSel += arr[i] + (i < arr.length-1 ? "\n" : "");
			}
		} else {			// indent
			newSel += "\t" + arr[i] + (i < arr.length-1 ? "\n" : "");
			if (i == 0)
				sr.start += 1;
			sr.end += 1;
	}
	}
	// update textarea content & move selection
	this.setTextAreaContent(selCont.before + newSel + selCont.after, sr);
}

// ===== set a new selection =====
// setSelectionRange2() is the real function
// setSelectionRange() is a hack for khtml (needs a small delay)
var timeoutSelectionRange = null;
WikkaEdit.prototype.setSelectionRange = function(start, end) {
	if (timeoutSelectionRange != null) {
		this.addLog("setSelectionRange() was called too early => exit");
		return;
	}
	// khtml browser take few milliseconds to update textarea
	// so, apparently, does FF (#704)
	// => need to wait a little for selection to be taken in account
	// TODO : replace "if (false)"
	// if (false) //(navigator.userAgent.indexOf("KHTML") == -1)
	if (navigator.userAgent.indexOf("KHTML") == -1 &&
	    navigator.userAgent.indexOf("Mozilla") == -1)
		this.setSelectionRange2(start, end);
	else
		timeoutSelectionRange = setTimeout("varWikkaEdit.setSelectionRange2(" + start + ", " + end + ");", 1);
}

WikkaEdit.prototype.setSelectionRange2 = function(start, end) {
	if (typeof(this.we_ta.setSelectionRange) != "undefined") {	// W3
		// some browsers use CRLF for newlines in textarea (opera...)
		if (this.we_ta.value.indexOf("\r\n") != -1) {
			var text = this.getTextAreaContent();
			var str;
			str = text.substr(0, start);
			str = str.replace(/\n/g, "  ");
			var nbCrLfStart = str.length - start;
			str = text.substr(0, end);
			str = str.replace(/\n/g, "  ");
			var nbCrLfEnd = str.length - end;
			start += nbCrLfStart;
			end += nbCrLfEnd;
		}
		// HACK : bug in opera
		// when (start == end) and a previous selection exists, setSelectionRange don't work
		if (window.opera == null) {
			this.we_ta.setSelectionRange(start, end);
		} else {
			if (start != end) {
				this.we_ta.setSelectionRange(start, end);
			} else {
				var sr = this.getSelectionRange();
				// check whether a move is necessary
				if ((sr.start != start) || (sr.end != end))
					this.we_ta.setSelectionRange(start, end);
				// check whether the bug has occured => select one character (can't do better...)
				if ((sr.start != start) || (sr.end != end))
					this.we_ta.setSelectionRange(start, start + 1);
			}
		}
	} else {											// ie6
		var range = this.we_ta.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end - start);
		range.select();
	}
	timeoutSelectionRange = null;
}

// ===== get selection content (before, selection, after) =====
WikkaEdit.prototype.getSelectionContent = function(sr) {
	// textarea content
	var text = this.getTextAreaContent();
	// selection, strings before & after selection and previous line
	var selCont = new Object();
	selCont.before = text.substr(0, sr.start);
	selCont.after = text.substr(sr.end);
	selCont.sel = text.substring(sr.start, sr.end);
	selCont.previousLine = selCont.before.substr(selCont.before.lastIndexOf("\n") + 1);
	return selCont;
}

// ===== get selection range =====
// - start
// - end
// ie6 hack from http://the-stickman.com/web-development/javascript/finding-selection-cursor-position-in-a-textarea-in-internet-explorer/
WikkaEdit.prototype.getSelectionRange = function() {
	var start = 0;
	var end = 0;
	if (typeof(this.we_ta.setSelectionRange) != "undefined") {	// W3
		start = this.we_ta.selectionStart;
		end = this.we_ta.selectionEnd;
		// some browsers use CRLF for newlines in textarea (opera...)
		if (this.we_ta.value.indexOf("\r\n") != -1) {
			var text = this.getTextAreaContent();
			var str;
			str = text.substr(0, start);
			str = str.replace(/\n/g, "  ");
			var nbCrLfStart = str.length - start;
			str = text.substr(0, end);
			str = str.replace(/\n/g, "  ");
			var nbCrLfEnd = str.length - end;
			start -= nbCrLfStart;
			end -= nbCrLfEnd;
		}
	} else {											// ie6
		this.we_ta.focus();
		var sel1 = document.selection.createRange();
		var sel2 = sel1.duplicate();
		sel2.moveToElementText(this.we_ta);
		var marker = "<WikkaRangeMark>";
		var selText = sel1.text;
		sel1.text = marker;
		var index = sel2.text.indexOf(marker);
		start = this.js_countTextAreaChars(index == -1 ? sel2.text : sel2.text.substring(0, index));
		end = this.js_countTextAreaChars(selText) + start;
		sel1.moveStart("character", -marker.length);
		sel1.text = selText;
		// setSelectionRange() is only a fix for khtml. The interval it uses cause
		// some problems here, so we don't use it
		var range = this.we_ta.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end - start);
		range.select();
	}
	return new SelRange(start, end);
}

// ===== get selection range =====
// - start
// - end
WikkaEdit.prototype.getSelectionRangeWholeLines = function() {
	var sr = this.getSelectionRange();
	// textarea content
	var text = this.getTextAreaContent();
	// select the whole lines
	var lineStart = text.lastIndexOf("\n", sr.start-1);
	if (lineStart > 0)
		lineStart++;
	else	// 0 or -1
		lineStart = 0;
	var lineEnd = text.indexOf("\n", sr.end);
	if (lineEnd == -1) lineEnd = text.length;
	// change selection
	sr.start = lineStart;
	sr.end = lineEnd;
	return sr;
}

// ===== count the number of chars in the textarea =====
WikkaEdit.prototype.js_countTextAreaChars = function(text) {
	text = text.replace(/\r\n/g, "\n");
	text = text.replace(/\r/g, "\n");
	return text.length;
	//var n = 0;
	//for (var i = 0; i < text.length; i++)
	//	n++;
	//return n;
}

// ===== get textarea content =====
// get textarea content with "\n" as line separator
WikkaEdit.prototype.getTextAreaContent = function() {
	var text = this.we_ta.value;
	// IE use \r\n instead of \n
	text = text.replace(/\r\n/g, "\n");
	text = text.replace(/\r/g, "\n");
	return text;
}

// ===== set textarea content =====
// set textarea content, move selection and give focus to the textarea
WikkaEdit.prototype.setTextAreaContent = function(text, sr) {
	// update textarea content
	var scrollTop = this.we_ta.scrollTop;
	this.we_ta.value = text;
	this.we_ta.scrollTop = scrollTop;
	// focus on textarea
	this.we_ta.focus();
	this.setSelectionRange(sr.start, sr.end);
}

// ===== add a line to the log =====
WikkaEdit.prototype.addLog = function(ch) {
	this.we_log.style.display = "";
	this.we_log.innerHTML = ch + "<br/>" + this.we_log.innerHTML;
}

// ===== clear the log =====
WikkaEdit.prototype.clearLog = function() {
	if (this.we_log.style.display == "")
		this.we_log.innerHTML = "";
}

// ===== remove leading and trailing spaces in a string =====
WikkaEdit.prototype.trim = function(str) {
	while(str.charAt(0) == " ") str = str.substr(1);
	while(str.charAt(str.length - 1) == " ") str = str.substr(0, str.length - 1);
	return str;
}

// ===== return the absolute coordinate of the specified object =====
WikkaEdit.prototype.getObjectCoords = function(obj) {
	// offsetLeft & offsetTop give coordinate of the parent object
	var x=0, y=0;
	do {
		x += obj.offsetLeft;
		y += obj.offsetTop;
	} while ((obj = obj.offsetParent) != null);
	return {x:x, y:y};
}

// ===== selectionRange object =====
function SelRange(start, end) {
	this.start = start;
	this.end = (end == null ? start : end)
}

// ===== run wikkaedit =====
var varWikkaEdit = new WikkaEdit(document.getElementById("body"));
if (varWikkaEdit.browserSupported())
	varWikkaEdit.init();
