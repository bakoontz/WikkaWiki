<?php
/*

SAFEHTML Parser.
v1.1.0.
22 May 2004.

http://www.npj.ru/kukutz/safehtml/

Copyright (c) 2004, Roman Ivanov.
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class safehtml {
  var $xhtml = "";
  var $Counter;
  var $Stack = array();
  var $dcStack = array();
  var $Protopreg = array();
  var $csspreg = array();
  var $dcCounter;

  // single tags ("<tag />")
  var $Singles = array("br", "area", "hr", "img", "input", "wbr");

  // dangerous tags
  var $Deletes = array("base", "basefont", "head", "html", "body", "applet", "object", "iframe", "frame", "frameset", "script", "layer", "ilayer", "embed", "bgsound", "link", "meta", "style", "title", "blink", "plaintext");

  // all content inside this tags will be also removed
  var $DeleteContent = array("script", "style", "title", "xml", );

  // dangerous protocols
  var $BlackProtocols = array("javascript", "vbscript", "about", "wysiwyg", "data", "view-source", "ms-its", "mhtml", "shell", "lynxexec", "lynxcgi", "hcp", "ms-help", "help", "disk", "vnd.ms.radio", "opera", "res", "resource", "chrome", "mocha", "livescript", );

  // pass only these protocols
  var $WhiteProtocols = array("http", "https", "ftp", "telnet", "news", "nntp", "gopher", "mailto", "file", );

  // white or black-listing of protocols?
  var $ProtocolFiltering = "white"; //or "black"

  // attributes that can contains protocols
  var $ProtocolAttributes = array("src", "href", "action", "lowsrc", "dynsrc", "background", "codebase", );

  // dangerous CSS keywords
  var $CSS = array("absolute", "fixed", "expression", "moz-binding", "content", "behavior", "include-source", );

  // tags that can have no "closing tag"
  var $noClose = array("p", "li");

  // dangerous attributes
  var $Attributes = array("dynsrc", );

  // constructor
  function safehtml() {

    //making regular expressions based on Proto & CSS arrays
    foreach ($this->BlackProtocols as $proto)
    {
     $preg = "/[\s\x01-\x1F]*";
     for ($i=0;$i<strlen($proto);$i++)
       $preg .= $proto{$i}."[\s\x01-\x1F]*";
     $preg .= ":/i";
     $this->Protopreg[] = $preg;
    }

    foreach ($this->CSS as $css)
     $this->csspreg[] = "/".$css."/i";
  }

  // Handles the writing of attributes - called from $this->openHandler()
  function writeAttrs ($attrs) {
    if (is_array($attrs)) {
      foreach ($attrs as $name => $value) {

        $name = strtolower($name);

        if (strpos($name, "on")===0) continue;
        if (strpos($name, "data")===0) continue;
        if (in_array($name, $this->Attributes)) continue;
        if (!preg_match("/^[a-z0-9]+$/i", $name)) continue;

        if ($value === TRUE || is_null($value)) $value = $name;

        if ($name == "style") 
        {
         $value = str_replace("\\", "", $value);
         $value = str_replace("&amp;", "&", $value);
         $value = str_replace("&", "&amp;", $value);
         foreach ($this->csspreg as $css)
         {
          if (preg_match($css, $value)) continue 2;
         }
         foreach ($this->Protopreg as $proto)
         {
          if (preg_match($proto, $value)) continue 2;
         }
        }

        $tempval = preg_replace( '/&#(\d+);/me' , "chr('\\1')" , $value ); //"'

        if (in_array($name, $this->ProtocolAttributes) && strpos($tempval, ":")!==false)
        if ($this->ProtocolFiltering=="black")
         foreach ($this->Protopreg as $proto)
         {
          if (preg_match($proto, $tempval)) continue 2;
         }
        else
        {
         $_tempval = explode(":", $tempval);
         $proto = $_tempval[0];
         if (!in_array($proto, $this->WhiteProtocols)) continue;
        }

        if (strpos($value, "\"")!==false) $q = "'";
        else $q = '"';
        $this->xhtml.=' '.$name.'='.$q.$value.$q;
      }
    }
  }

  // Opening tag handler
  function openHandler(& $parser,$name,$attrs) {

    $name = strtolower($name);

    if (in_array($name, $this->DeleteContent)) 
    {
     array_push($this->dcStack, $name);
     $this->dcCounter[$name]++;
    }
    if (count($this->dcStack)!=0) return true;

    if (in_array($name, $this->Deletes)) return true;
    
    if (!preg_match("/^[a-z0-9]+$/i", $name)) 
    {
      if (preg_match("!(?:\@|://)!i", $name))
        $this->xhtml.="&lt;".$name."&gt;";
      return true;
    }

    if (in_array($name, $this->Singles))
    {
      $this->xhtml.="<".$name;
      $this->writeAttrs($attrs);
      $this->xhtml.=" />";
      return true;
    }

    $this->xhtml.="<".$name;
    $this->writeAttrs($attrs);
    $this->xhtml.=">";
    array_push($this->Stack,$name);
    $this->Counter[$name]++;
  }

  // Closing tag handler
  function closeHandler(& $parser,$name) {

    $name = strtolower($name);

    if ($this->dcCounter[$name]>0 && in_array($name, $this->DeleteContent))
    {
     while ($name!=($tag=array_pop($this->dcStack)))
     {
      $this->dcCounter[$tag]--;
     }
    $this->dcCounter[$name]--;
    }

    if (count($this->dcStack)!=0) return true;

    if ($this->Counter[$name]>0)
    {
     while ($name!=($tag=array_pop($this->Stack)))
     {
      if (!in_array($tag, $this->noClose))
        $this->xhtml.="</".$tag.">";
      $this->Counter[$tag]--;
     }
     $this->xhtml.="</".$name.">";
     $this->Counter[$name]--;
    }
  }

  // Character data handler
  function dataHandler(& $parser,$data) {
    if (count($this->dcStack)==0)
      $this->xhtml.=$data;
  }

  // Escape handler
  function escapeHandler(& $parser,$data) {
  }

  // Return the XHTML document
  function getXHTML () {
    while ($tag=array_pop($this->Stack))
    {
      if (!in_array($tag, $this->noClose))
        $this->xhtml.="</".$tag.">\n";
    }
    return $this->xhtml;
  }

  function clear() {
   $this->xhtml = "";
  }

}

?>