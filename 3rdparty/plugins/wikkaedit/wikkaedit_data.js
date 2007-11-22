/*
////////////////////////////////////////////////////////////////////////
// WikkaEdit                                                          //
// v. 1.00                                                            //
// supported browsers : MZ1.4+, MSIE5+, Opera 8+, khtml/webkit        //
//                                                                    //
// (C) 2007 Olivier Borowski (olivier.borowski@wikkawiki.org)         //
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

WikkaEdit.prototype.initCategs = function() {
	this.we_categs.hidden = {title:null};
	this.we_categs.objects = {title:"Objects"};
	this.we_categs.page = {title:"Page"};
	this.we_categs.accounts = {title:"User"};
	this.we_categs.global = {title:"Wiki"};
	this.we_categs.misc = {title:"Utilities"};
	this.we_categs.info = {title:"Information"};
}

function WikkaAction(categName, tagName, title, summary, usage) {
	this.we_categ = categName;
	this.we_name = tagName;
	this.we_title = title;
	this.we_summary = summary;
	this.we_usage = usage;
	this.we_params = new Object();
}

WikkaAction.prototype.addParam = function(name, value, nodefault, description) {
	nodefault = (nodefault == true);
	this.we_params[varWikkaEdit.nameToRef(name)] = {name:name, value:value, nodefault:nodefault, description:description};
}

WikkaEdit.prototype.initActions = function() {
	// ===== basic (hidden) =====
	this.we_actions.we_color = new WikkaAction("hidden", "color", "Text color");
	this.we_actions.we_color.addParam("text", "text");
	this.we_actions.we_color.addParam("c", "color");
	this.we_actions.we_image = new WikkaAction("hidden", "image", "Image", "Display an image.");
	this.we_actions.we_image.addParam("url", "url", null, "Image URL. Can be relative (images/img.png) or external (http://example.com/example.jpg)");
	this.we_actions.we_image.addParam("title", "text", null, "Image title");
	this.we_actions.we_image.addParam("alt", "text", null, "Alternate text when image can't be displayed");
	this.we_actions.we_image.addParam("class", "className", true, "Class name (defined in the CSS file)");
	this.we_actions.we_image.addParam("link", "url", true, "Add a link to the image");
	this.we_actions.we_usersettings = new WikkaAction("hidden", "usersettings", "My user settings");
	this.we_actions.we_emailpassword = new WikkaAction("hidden", "emailpassword", "Lost password form", "Display a form to retrieve a password");
	// ===== objects =====
	this.we_actions.we_calendar = new WikkaAction("objects", "calendar", "Calendar", "Display a calendar for a specified or the current month.", "Specifying a month and/or year in the action itself results in a \"static\" calendar without navigation links; conversely, providing no parameters results in a calendar with navigation links to previous, current and next month, with URL parameters determining which month is shown (with the current month as default).");
	this.we_actions.we_calendar.addParam("year", "yyyy", null, "Year in 4 digits format");
	this.we_actions.we_calendar.addParam("month", "mm", null, "Month in 2 digits format");
	this.we_actions.we_flash = new WikkaAction("objects", "flash", "Flash animation");
	this.we_actions.we_flash.addParam("url", "http://example.com/example.swf");
	this.we_actions.we_flash.addParam("width", "w", true);
	this.we_actions.we_flash.addParam("height", "h", true);
	this.we_actions.we_include = new WikkaAction("objects", "include", "Page include", "Include another wiki page in the current page");
	this.we_actions.we_include.addParam("page", "name of the page to be included");
	this.we_actions.we_files = new WikkaAction("objects", "files", "File upload form");
	this.we_actions.we_files.addParam("download", "filename");
	this.we_actions.we_files.addParam("text", "file description");
	this.we_actions.we_mindmap = new WikkaAction("objects", "mindmap", "Mindmap", "Embed a mindmap in the current page.");
	// ===== infos - this page =====
	this.we_actions.we_rss = new WikkaAction("page", "rss", "RSS", "Embed a feed in the current page");
	this.we_actions.we_backlinks = new WikkaAction("page", "backlinks", "Backlinks", "Display a list of pages linking to the current page");
	this.we_actions.we_lastedit = new WikkaAction("page", "lastedit", "Last edit", "Display information on the last revision");
	// === infos - accounts===
	this.we_actions.we_mychanges = new WikkaAction("accounts", "mychanges", "My changes", "Display a list of edits by the current user");
	this.we_actions.we_mypages = new WikkaAction("accounts", "mypages", "My pages", "Display a list of pages owned by the current user");
	// === infos - global===
	this.we_actions.we_category = new WikkaAction("global", "category", "Category", "Display pages belonging to a category");
	this.we_actions.we_wantedpages = new WikkaAction("global", "wantedpages", "Wanted pages", "Display a list of missing pages");
	this.we_actions.we_orphanedpages = new WikkaAction("global", "orphanedpages", "Orphaned pages", "Display a list of pages not linked from any other page");
	this.we_actions.we_pageindex = new WikkaAction("global", "pageindex", "Page index", "Display an alphabetical list of pages in the wiki");
	this.we_actions.we_recentchanges = new WikkaAction("global", "recentchanges", "Recently changed pages");
	this.we_actions.we_recentcomments = new WikkaAction("global", "recentcomments", "Recently commented pages");
	this.we_actions.we_recentlycommented = new WikkaAction("global", "recentlycommented", "Latest comments");
	this.we_actions.we_lastusers = new WikkaAction("global", "lastusers", "Recently registered users", "Display a list of recently registered users");
	// === misc ===
	this.we_actions.we_newpage = new WikkaAction("misc", "newpage", "New page form", "Display a form to create a new page");
	this.we_actions.we_textsearch = new WikkaAction("misc", "textsearch", "Search form");
	this.we_actions.we_textsearchexpanded = new WikkaAction("misc", "textsearchexpanded", "Advanced search form");
	this.we_actions.we_googleform = new WikkaAction("misc", "googleform", "Google search form");
	this.we_actions.we_feedback = new WikkaAction("misc", "feedback", "Feedback form");
	this.we_actions.we_nocomments = new WikkaAction("misc", "nocomments", "Disallow comments");
	this.we_actions.we_interwikilist = new WikkaAction("misc", "interwikilist", "Interwiki list");
	// === infos - rare===
	this.we_actions.we_wikkaname = new WikkaAction("info", "wikkaname", "Wikka name", "Display the name of the current wiki");
	this.we_actions.we_wikkaversion = new WikkaAction("info", "wikkaversion", "Wikka version", "Display the version of the current wiki");
	this.we_actions.we_wikkachanges = new WikkaAction("info", "wikkachanges", "Wikka release notes");
	this.we_actions.we_phpversion = new WikkaAction("info", "phpversion", "PHP version", "Display the PHP version running on this server");
	this.we_actions.we_mysqlversion = new WikkaAction("info", "mysqlversion", "MySQL version", "Display the MySQL version running on this server");
	this.we_actions.we_system = new WikkaAction("info", "system", "System information", "Display a table with information on this server");
	this.we_actions.we_countpages = new WikkaAction("info", "countpages", "Count pages", "Display the total number of pages in this wiki");
	this.we_actions.we_countowned = new WikkaAction("info", "countowned", "Count owned pages", "Display the number of pages owned by the current user");
	this.we_actions.we_countcomments = new WikkaAction("info", "countcomments", "Count comments", "Display the total number of comments in this wiki");
	this.we_actions.we_countusers = new WikkaAction("info", "countusers", "Count users", "Display the total number of registered users in this wiki");
}
