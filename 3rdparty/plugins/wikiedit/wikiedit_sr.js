/*
 * Display a dialog for searching and replacing strings in the current edit screen.
 *  
 * Note: Cross-browser semi-transparence is not W3 compliant CSS.
 *
 * @package		3rdparty
 * @name		Search&Replace
 * @version		0.1
 * @since		Wikka 1.1.6.2
 * @author {http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @link {http://wush.net/trac/wikka/ticket/125 #125}
 * @todo 	- css to be moved to wikiedit.css
 *			- Ctrl+Z doesn't always work
 *  			- Add Documentation
 *  			- When the selection (the text to be replaced that is highlighted) is hidden (out of the 
         	scope of the textarea), no scrolling happens in Mozilla.
 *  			- Create a button in WikiEdit Toolbar
 */

var sr_i18n_err;
var sr_dlg=
{
 obj:null, //the div object
 dir_global_next:false,   
 lX:0,
 lY:0,
 getX:function()
 {
  return parseInt(sr_dlg.obj.style.left);
 },
 getY:function()
 {
  return parseInt(sr_dlg.obj.style.top);
 },
 setXY:function(X,Y)
 {
  sr_dlg.obj.style.left = X + 'px';
  sr_dlg.obj.style.top  = Y + 'px';
 },
 save_eXY:function(e)
 {
  sr_dlg.lX = e.clientX;
  sr_dlg.lY = e.clientY;
 },  
 doMouseDown:function(e)
 {
  e=sr_dlg.compat_e(e);
  if(e.which!=1){return true}
  sr_dlg.save_eXY(e);
  document.onmouseup=sr_dlg.doMouseUp;
  document.onmousemove=sr_dlg.doMouseMove;
  return false
 },
 doMouseMove:function(e)
 {
  var new_X,new_Y;
  e=sr_dlg.compat_e(e);
  if(e.which==0)
  {
   return sr_dlg.doMouseUp()
  }
  new_X=sr_dlg.getX()+e.clientX-sr_dlg.lX;
  new_Y=sr_dlg.getY()+e.clientY-sr_dlg.lY;
  sr_dlg.setXY(new_X, new_Y);
  sr_dlg.save_eXY(e);
  return false
 },
 doMouseUp:function(e)
 {
  document.onmousemove=null;
  document.onmouseup=null;
  sr_dlg.unhide();
  return null;
 },
 compat_e:function(e)
 {
  if(!e) e=window.event;
  if(!e.which) e.which=e.button;
  return e;
 },
 unhide:function()
 {//When moving around the div object, we make sure the menu bar isn't hidden
  var act_X=sr_dlg.getX(),act_Y=sr_dlg.getY();
  if ((act_Y < 10) || (act_X < 10))
  {
   if (act_X < 10) act_X ++;
   if (act_Y < 10) act_Y ++;
   sr_dlg.setXY(act_X, act_Y);
   setTimeout('sr_dlg.unhide()', 30);
  }
 },
 close:function(reset_only)
 {
  document.getElementById("replacement2").style.display = 'none';
  sr_dlg.setAttrReadOnly(document.forms.replaceform.replacement, false);
  sr_dlg.dir_global_next = false;
  sr_dlg.setAttrReadOnly(wE.area, false);
  if (!reset_only)
  {
   sr_dlg.obj.style.display = 'none';
  }
 },
 setAttrReadOnly:function()
 {
  _obj = arguments[0];
  _ro = arguments.length == 1 ? true : arguments[1];
  if (isIE) 
  {
   _obj.readOnly = _ro;
   _obj.className = _obj.className.replace(/ IEreadonly/g, '');
   if (_ro) _obj.className = _obj.className+' IEreadonly';
  }
  else
  {
   if (_ro) _obj.setAttribute('readonly', 'readonly');
   else _obj.removeAttribute('readonly');
  }
 },
 wE_getDefines:function()
 {//In IE, when some texts are selected in the textarea, and if the textarea is blurred then focused again,
  //the current selection is lost. This is an ennoying behavior if the user clicks on the replace window.
  //Strange but when the user clicks on the toolbar button, for example on the Bold button, this doesn't happen.
   var t = wE.area;

   text = t.value.replace(/\r/g, "");
   //I don't know why but in IE, if the user clicks on replacement before clicking to Replace by, an error 
   //related to sel.htc occurs. This solves that strange behavior
   try
   {
    wE.ss = t.selectionStart;
    wE.se = t.selectionEnd;
   } catch (e) {}
 
   wE.sel1 = text.substr(0, wE.ss);
   wE.sel2 = text.substr(wE.se);
   wE.sel = text.substr(wE.ss, wE.se - wE.ss);
   wE.str = wE.sel1+wE.begin+wE.sel+wE.end+wE.sel2;
  if (wE.ss == 0 && wE.se == 0 && (sr_dlg.cursS || sr_dlg.cursE)) //Blur on IE
  {
   wE.sel1 = sr_dlg.cursS;
   wE.sel2 = sr_dlg.cursE;
   wE.sel  = sr_dlg.cursl;
   wE.ss = sr_dlg.cursS.length;
   wE.se = sr_dlg.cursl.length+wE.ss;
  }
 },
 getDefines:function()
 {
  wE.area.focus();
  sr_dlg.replp = document.forms.replaceform.search.value;
  sr_dlg.replr = document.forms.replaceform.replacement.value;
  sr_dlg.is_regexp = document.forms.replaceform.is_regexp.checked;
  sr_dlg.prompt = document.forms.replaceform.prompt.checked;
  sr_dlg.ignore_case = document.forms.replaceform.ignore_case.checked;
  sr_dlg.whole_word = document.forms.replaceform.whole_word.checked;
  sr_dlg.search_dir_list = document.forms.replaceform.search_dir;
  sr_dlg.search_dir = 'global';
  sr_dlg.replacement = document.forms.replaceform.replace_by.value.replace(/([^\\](\\\\)*|^)\\n/, '$1\n');
  sr_dlg.replacement = sr_dlg.replacement.replace(/\\\\/, '\\');
  for(i=0; i<sr_dlg.search_dir_list.length; i++)
  {
   if (sr_dlg.search_dir_list[i].checked) sr_dlg.search_dir = sr_dlg.search_dir_list[i].value;
  }
  sr_dlg.wE_getDefines();
  sr_dlg.save_prev = sr_dlg.save_next = '';
  if (sr_dlg.search_dir == 'bw')
  {  
   sr_dlg.dir_global_next = false;
  }
  if ((sr_dlg.search_dir == 'fw') || sr_dlg.dir_global_next) {
   sr_dlg.save_prev = wE.sel1;
   sr_dlg.save_next = '';
   sr_dlg.text = wE.sel+wE.sel2;
  } else if (sr_dlg.search_dir == 'bw') {
   sr_dlg.save_prev = '';
   sr_dlg.save_next = wE.sel2;
   sr_dlg.text = wE.sel1+wE.sel;
  } else if (sr_dlg.search_dir == 'global') {
   sr_dlg.text = wE.sel1+wE.sel+wE.sel2;
  }
 },
 replace_next:function()
 {
  sr_dlg.getDefines();
  if (sr_dlg.search_dir == 'bw')
  {
   wE_setAreaContent = (sr_dlg.cursS+wE.begin+wE.end+sr_dlg.cursl+sr_dlg.cursE);
   sr_dlg.cursE = sr_dlg.cursl+sr_dlg.cursE;
  }
  else
  {
   wE_setAreaContent = (sr_dlg.cursS+sr_dlg.cursl+wE.begin+wE.end+sr_dlg.cursE);
   sr_dlg.cursS = sr_dlg.cursS+sr_dlg.cursl;
  }
  sr_dlg.cursl = '';
  wE.setAreaContent(wE_setAreaContent);
  if (sr_dlg.search_dir == 'global')
  {
   sr_dlg.dir_global_next = true;
  }
  sr_dlg.prep_repl();
 },
 replace_do:function()
 {
  wE.area.focus(); //IE
  sr_dlg.getDefines();
  sr_dlg.cursl = sr_dlg.replacement;
  sr_dlg.wE_undo();
  if (sr_dlg.search_dir == 'bw')
  {
   wE_setAreaContent = (sr_dlg.cursS+wE.begin+wE.end+sr_dlg.replacement+sr_dlg.cursE);
  }
  else
  {
   wE_setAreaContent = (sr_dlg.cursS+sr_dlg.replacement+wE.begin+wE.end+sr_dlg.cursE);
  }
  wE.setAreaContent(wE_setAreaContent);
  sr_dlg.prep_repl();
 },
 wE_undo:function()
 {
  wE.undotext = wE.area.value;
  wE.undosels = wE.area.selectionStart;
  wE.undosele = wE.area.selectionEnd;
 },
 prep_repl:function()
 {
  sr_dlg.getDefines();
  if (!sr_dlg.is_regexp)
  {
   sr_dlg.replp = sr_dlg.replp.replace(/('|\.|\\|\+|\*|\?|\[|\^|\]|\$|\(|\)|\{|\}|\=|\!|\<|\>|\|)/g, '\\$1');
  }
  else
  {
   sr_dlg.replr = sr_dlg.replr.replace(/\\n/g, '##sign\nngis##');
   sr_dlg.replr = sr_dlg.replr.replace(/\\\\/g, '##sign\\ngis##');
   sr_dlg.replr = sr_dlg.replr.replace(/\\\$/g, '##sign$ngis##');
   sr_dlg.replr = sr_dlg.replr.replace(/\\t/g, '##sign\tngis##');
   if (sr_dlg.replr.match(/\$0/))
   {
    if (sr_dlg.replr.match(/\$9/)) { alert(sr_i18n_err.nodol9); return (false); }
    for (_i=8; _i>=0; _i--)
    {
     _regexp = new RegExp("\\$"+_i, 'g');
     sr_dlg.replr = sr_dlg.replr.replace(_regexp, '$'+(_i+1));
    }
    sr_dlg.replp = '('+sr_dlg.replp+')';
   }
  }
  if (sr_dlg.whole_word) sr_dlg.replp = '\\b'+sr_dlg.replp+'\\b';
  replaced_text = sr_dlg.text;
  if (!sr_dlg.prompt)
  {
   modifier = sr_dlg.ignore_case ? "gi" : "g";
   try
   {
    var rrepl = new RegExp(sr_dlg.replp, modifier);
   }
   catch(e) { alert(sr_i18n_err.badregexp.replace(/\$1/, sr_dlg.replp) +e); return(false);}
   try 
   {
    replaced_text = sr_dlg.text.replace(rrepl, sr_dlg.replr);
    replaced_text = replaced_text.replace(/##sign(.|\n)ngis##/g, '$1');
   }
   catch(e){alert(e);};
   sr_dlg.wE_undo();
   wE.setAreaContent(replaced_text+wE.begin+wE.end);
  }
  else
  {
   sr_dlg.setAttrReadOnly(document.forms.replaceform.replacement);
   document.getElementById("replacement2").style.display = 'inline';
   modifier = sr_dlg.ignore_case ? "i" : "";
   try 
   {
    var rrepl;
   if (sr_dlg.search_dir == 'bw') rrepl = new RegExp('(.*)('+sr_dlg.replp+')', modifier);
   else rrepl = new RegExp('('+sr_dlg.replp+')', modifier);
   } catch(e) { alert(sr_i18n_err.badregexp.replace(/\$1/, sr_dlg.replp) +e); return(false);}
   try
   {
    if (sr_dlg.search_dir == 'bw') matched = replaced_text.replace(rrepl, '$1'+wE.begin+'$2'+wE.end);
    else matched = replaced_text.replace(rrepl, wE.begin+'$1'+wE.end);
   }
   catch (e) { alert(e+replaced_text); return (false)};
   if (matched && (matched != replaced_text))
   {
    sr_dlg.setAttrReadOnly(wE.area);
    wE.setAreaContent(sr_dlg.save_prev+matched+sr_dlg.save_next);
    sr_dlg.wE_getDefines();
    sr_dlg.cursS = wE.sel1;
    sr_dlg.cursE = wE.sel2;
    sr_dlg.cursl = wE.sel;
    rrepl2 = new RegExp(sr_dlg.replp, modifier);
    sr_dlg.replr_dol0 = sr_dlg.replr.replace(/\$0/, wE.sel);
    replacement = wE.sel.replace(rrepl2, sr_dlg.replr_dol0);
    replacement = replacement.replace(/##sign(\n)ngis##/g, '\\n');
    replacement = replacement.replace(/##sign(\\)ngis##/g, '\\\\');
    replacement = replacement.replace(/##sign(.)ngis##/g, '$1');
    document.forms.replaceform.replace_by.value = replacement;
   }
   else
   {
    document.getElementById("replacement2").style.display = 'none';
    sr_dlg.setAttrReadOnly(document.forms.replaceform.replacement, false);
    sr_dlg.dir_global_next = false;
    sr_dlg.setAttrReadOnly(wE.area, false);
   }
  }
  wE.area.focus();
  return false;
 },
 show:function()
 {
  if (sr_dlg.obj) //If div object already exists, we just show it
  {
   sr_dlg.obj.style.display = 'block';
  }
  else
  {
   if (typeof(sr_i18n) == 'undefined')
   var sr_i18n = {
    title:"Search &amp; replace",
    label_search:"Search for",
    label_repl:"Replace with",
    regexp:"Regular expression",
    prompt:"Prompt before replacing",
    icase:"Ignore case",
    whole_word:"Whole word",
    globals:"Wrap search",
    fws:"Forward",
    bws:"Backward",
    go:"Go",
    reset:"Reset",
    next:"Next",
    replace_by:"Replace with"
   };
   sr_i18n_err = {
    nodol9:"Error: You cannot use both $0 and $9 !",
    badregexp:"Regular expression $1 not wellformed:\n"
   };
   sr_dlg.obj = document.createElement('div');
   sr_dlg.obj.setAttribute('id', 'replacediv');
   _style = document.createElement('style');

   opacity = isIE ? ' filter: Alpha(Opacity: 88);' : 'opacity: 0.88;';
   _head = document.getElementsByTagName('head');
   if (_head) _head[0].appendChild(_style);
   _innerHTML = 
   '#replacediv'+
   '{'+
     ' background: #eee;'+
     ' position: absolute;'+
     ' width: 530px;'+
     ' height: auto;'+
     ' left: 50;'+
     ' top: 50;'+
     ' padding: 3px;'+
     opacity+
     ' border: 1px solid #666;'+
   '}'+
   '#replacediv, #replacediv * input'+
   '{'+
     ' font-family: Verdana, Trebuchet MS, Arial, serif;'+
     ' font-size: 10px;'+
   '}'+
   '#replacediv * td'+
   '{'+
     ' padding: 3px;'+
     ' line-height: 1em;'+
   '}'+
   '#replacediv #sr_menuline a'+
   '{'+
     ' text-decoration: none;'+
     ' color: #FFF;'+
     ' padding: 2px;'+
   '}'+
   '#replacediv #sr_menuline'+
   '{'+
     ' font-size: 12px;'+
     ' text-align: right;'+
     ' background: #666;'+
     ' color: #eff;'+
     ' line-height: 1.2em;'+
     ' padding: 0;'+
     ' cursor: move;'+
   '}'+
   '#replacediv * input[readonly], textarea[readonly], .IEreadonly'+
   '{'+
     'background: #eee;'+
   '}';
   if (isIE) 
   {
    _style = document.styleSheets[0];
    _styleRules0 = _innerHTML.split("}");
    for (_i=0; _i<_styleRules0.length-1; _i++)
    {
     _styleRules1 = _styleRules0[_i].split("{");
     rules = _styleRules1[0].split(",");
     for (_j= 0; _j<rules.length; _j++)
     {
      if (/\[/.test(rules[_j])) continue;
      _style.addRule(rules[_j], _styleRules1[1]);
     }
    }
   }
   else
   {
    _style.innerHTML = _innerHTML;
   }
   document.body.appendChild(sr_dlg.obj);
   sr_dlg.obj.innerHTML =  '<form id="replaceform" onsubmit="return sr_dlg.prep_repl();">'+
     ' <table cellpadding="0" cellspacing="0" width="100%">'+
     ' <tr id="sr_menuline"><td style="text-align:left">'+sr_i18n.title+'</td><td style="text-align:right"><a href="javascript:sr_dlg.close();">x</a></td></tr>'+
     ' <tr><td>'+sr_i18n.label_search+':</td><td><input name="search" maxlength="70" /></td></tr>'+
     ' <tr><td>'+sr_i18n.label_repl+':</td><td><input name="replacement" id="replacement" maxlength="256" /></td></tr>'+
     ' <tr>'+
     '  <td>'+
     '   <input id="is_regexp" name="is_regexp" type="checkbox" /><label for="is_regexp">'+sr_i18n.regexp+'</label>'+
     '  </td>'+
     '  <td>'+
     '   <input type="checkbox" name="prompt" checked="checked" id="prompt" /><label for="prompt">'+sr_i18n.prompt+'</label><br />'+
     '  </td>'+
     '  </tr>'+
     '  <tr>'+
     '  <td>'+
     '   <input name="ignore_case" id="ignore_case" type="checkbox" /><label for="ignore_case">'+sr_i18n.icase+'</label>'+
     '  </td>'+
     '  <td>'+
     '   <input name="whole_word" id="whole_word" type="checkbox" /><label for="whole_word">'+sr_i18n.whole_word+'</label>'+
     '  </td>'+
     ' </tr>'+
     ' <tr>'+
     '  <td colspan="2">'+
     '   <input name="search_dir" value="global" type="radio" checked="checked" id="search_dir_global" /><label for="search_dir_global">'+sr_i18n.globals+'</label> &nbsp;'+
     '   <input name="search_dir" value="fw" type="radio" id="search_dir_fw" /><label for="search_dir_fw">'+sr_i18n.fws+'</label> &nbsp;'+
     '   <input name="search_dir" value="bw" type="radio" id="search_dir_bw" /><label for="search_dir_bw">'+sr_i18n.bws+'</label> &nbsp;'+
     '  </td>'+
     ' </tr>'+
     ' <tr>'+
     '  <td colspan="2">'+
     '   <input type="submit" value="'+sr_i18n.go+'" /> &nbsp; '+
     '   <input type="button" value="'+sr_i18n.reset+'" onclick="sr_dlg.close(true);" /> &nbsp;'+
     '   <div style="display:none" id="replacement2">'+
     '    <input type="button" value="'+sr_i18n.next+'" onclick="sr_dlg.replace_next();" accesskey="n" title="Alt+N" /> &nbsp;'+
     '    <input type="button" value="'+sr_i18n.replace_by+'" onclick="sr_dlg.replace_do();" accesskey="r" title="Alt+R" /> &nbsp;'+
     '    <input name="replace_by" />'+
     '   </div>'+
     '  </td>'+
     ' </tr>'+
     ' </table>'+
     ' </form>';
   document.getElementById("sr_menuline").onmousedown=sr_dlg.doMouseDown;
   new_X = wE.area.offsetLeft ? wE.area.offsetLeft : 0;
   new_Y = wE.area.offsetTop ? wE.area.offsetTop : 0;
   if (isNaN(sr_dlg.getX()) || isNaN(sr_dlg.getY())) sr_dlg.setXY(new_X+50,new_Y+50);
   document.body.appendChild(sr_dlg.obj);
  }
  document.forms.replaceform.search.focus();
  document.forms.replaceform.search.select();
  sr_dlg.wE_getDefines();
 }
};
loaded_x_wikiedit_sr_js = true;