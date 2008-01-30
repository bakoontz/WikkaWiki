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


// ===== constructor =====
function WikkaEdit(textarea) {
	this.EDITOR_MIN_HEIGHT = 120;			// min textarea height (in px)
	this.EDITOR_HEIGHT_POLL_INTERV = 500;	// textarea height change poll interval (in ms)
	this.CONTEXTUAL_HELP_POLL_INTERV = 500;	// refresh time for contextual help (in ms)
	this.we_ta = textarea;					// textarea (= document.getElementById("body"))
	this.we_submenu = null;					// submenu div (= document.getElementById("wikkasubmenu"))
	this.we_toolbar = null;					// toolbar div (= document.getElementById("wikkatoolbar"))
	this.we_help = null;						// help div (= document.getElementById("wikkahelp"))
	this.we_buttons = new Object();			// buttons, submenus
	this.we_categs = new Object();			// action categs
	this.we_actions = new Object();			// actions
	this.we_currentSubmenu = null;			// submenu currently displayed
	// === modules ===
	this.we_actionsMenuEnabled = null;
	this.we_searchReplaceEnabled = null;
	// === browser dependant code ===
	// only used for rare-very-specific browser behaviour
	this.we_webkit = (navigator.userAgent.toLowerCase().indexOf("webkit") != -1);
	this.we_gecko = ((!this.we_webkit) && (navigator.userAgent.toLowerCase().indexOf("gecko") != -1));
	this.we_khtml = ((!this.we_webkit) && (navigator.userAgent.toLowerCase().indexOf("khtml") != -1));
	this.we_opera = (navigator.userAgent.toLowerCase().indexOf("opera") != -1);
	this.we_canCancelKeyEvent = (!this.we_opera);	// opera can't cancel key events, so native browser shortcuts can't be overriden
	// === search & replace ===
	this.we_searchFor = "";
	this.we_replaceWith = "";
	this.we_replace = false;
	this.we_regexp = false;
	this.we_case = false;
	this.we_whole = false;
	this.we_cursor = true;
	this.we_reverse = false;
	this.we_prompt = true;
	this.we_lastMsg = "";
}

// =====  name to ref =====
WikkaEdit.prototype.nameToRef = function(name) {
	return "we_" + name;
}
WikkaEdit.prototype.refToName = function(ref) {
	return ref.substr(3);
}

WikkaEdit.prototype.initButtons = function() {
	this.we_buttons.we_h1 = {type:"button", title:"Heading 1"};
	this.we_buttons.we_h2 = {type:"button", title:"Heading 2"};
	this.we_buttons.we_h3 = {type:"button", title:"Heading 3"};
	this.we_buttons.we_h4 = {type:"button", title:"Heading 4"};
	this.we_buttons.we_h5 = {type:"button", title:"Heading 5"};

	this.we_buttons.we_sep1 = {type:"separator"};

	this.we_buttons.we_bold = {type:"button", title:"Bold"};
	this.we_buttons.we_italic = {type:"button", title:"Italic"};
	this.we_buttons.we_underline = {type:"button", title:"Underline"};
	this.we_buttons.we_strike = {type:"button", title:"Strikethrough"};
	this.we_buttons.we_style = {type:"submenu", title:"Show more", we_buttons:new Object()};
	this.we_buttons.we_style.we_buttons.we_forecolor = {type:"button", title:"Colored text"};
	this.we_buttons.we_style.we_buttons.we_monospace = {type:"button", title:"Monospace"};
	this.we_buttons.we_style.we_buttons.we_highlight = {type:"button", title:"Highlight"};
	this.we_buttons.we_style.we_buttons.we_key = {type:"button", title:"Key"};
	this.we_buttons.we_style.we_buttons.we_leftfloat = {type:"button", title:"Left float"};
	this.we_buttons.we_style.we_buttons.we_rightfloat = {type:"button", title:"Right float"};

	this.we_buttons.we_sep2 = {type:"separator"};

	this.we_buttons.we_justifycenter = {type:"button", title:"Center text"};
	this.we_buttons.we_bullist = {type:"button", title:"List"};
	this.we_buttons.we_numlist = {type:"button", title:"Numbered list"};
	this.we_buttons.we_comments = {type:"button", title:"Inline comments"};

	this.we_buttons.we_sep3 = {type:"separator"};

	this.we_buttons.we_indent = {type:"button", title:"Indent"};
	this.we_buttons.we_outdent = {type:"button", title:"Outdent"};

	this.we_buttons.we_sep4 = {type:"separator"};

	this.we_buttons.we_hr = {type:"button", title:"Line"};
	this.we_buttons.we_link = {type:"button", title:"Hyperlink"};
	this.we_buttons.we_image = {type:"button", title:"Image"};
	this.we_buttons.we_table = {type:"button", title:"Table"};

	this.we_buttons.we_sep5 = {type:"separator"};

	this.we_buttons.we_rawhtml = {type:"button", title:"Raw HTML"};
	this.we_buttons.we_sourcecode = {type:"button", title:"Source code"};

	this.we_buttons.we_sep6 = {type:"separator"};

	if (this.we_searchReplaceEnabled)
		this.we_buttons.we_find = {type:"button", title:"Search &amp; replace"};

	if (varWikkaEdit.we_canCancelKeyEvent)
		this.we_buttons.we_shortcuts = {type:"button", title:"Display shortcuts"};

	this.we_buttons.we_formatting_rules = {type:"button", title:"Formatting rules (new window)"};
}

/* ===== actions are only enabled in WikkaWiki 1.1.7 ===== */
/*WikkaEdit.prototype.initCategs = function() {
	// ...
}

function WikkaAction(categName, tagName, title, summary, usage) {
	// ...
}

WikkaAction.prototype.addParam = function(name, value, nodefault, description) {
	// ...
}

WikkaEdit.prototype.initActions = function() {
	// ...
}
*/