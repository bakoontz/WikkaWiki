SAFEHTML
--------
Version 1.0.3
http://www.npj.ru/kukutz/safehtml/
--------

This parser strip down all potentially dangerous content within HTML:
  * opening tag without its closing tag
  * closing tag without its opening tag
  * any of these tags: "base", "basefont", "head", "html", "body", "applet", "object", 
    "iframe", "frame", "frameset", "script", "layer", "ilayer", "embed", "bgsound", 
    "link", "meta", "style", "title", "blink", "xml" etc.
  * any of these attributes: on*, data*, dynsrc
  * javascript:/vbscript:/about: etc. protocols
  * expression/behavior etc. in styles
  * any other active content

If you found any bugs in this parser, please inform me -- ICQ:551593 or 
mailto:thingol@mail.ru

Please, subscribe to http://www.npj.ru/kukutz/safehtml/rss feed in order to receive notices 
when SAFEHTML will be updates.

-- Roman Ivanov.



--------
Version history:
--------
1.0.3.
 Added array of elements that can have no closing tag.
1.0.2.
 Bug fix: <img src="&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;alert(1);"> attack.
 Thanks to shmel.
1.0.1.
 Bug fix: safehtml hangs on <style></style></style> code.
 Thanks to lj user=electrocat.
1.0.0.
 First public release
