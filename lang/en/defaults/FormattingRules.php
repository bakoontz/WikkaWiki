<?php
printf('======%s======', T_('Wikka Formatting Guide'));
printf('---');
printf('<<===%s===', T_('General Guidelines'));
printf(T_('If a markup sequence is not in these guidelines, then it is not officially supported. Use caution when implementing markup that is not officially supported by this document, as undocumented functionality may change with version updates.'));
printf('--- ---');
printf(T_('Unless otherwise stated in these guidelines, all markup is line based, that is, a newline character should not intervene. These are identified as \'inline\' markup.'));
printf('<<::c::');
printf('---');
printf('===%s===', T_('Escaping Markup'));
printf('---');
printf(T_('Anything between 2 sets of double-quotes is not formatted.
This is the equivalent of escaping a character(s):'));
printf('--- ---');
printf('<<');
printf('
~##""**%s &#34&#34**&#34&#34 %s**""##
', T_('two bold'), T_('stars'));
printf('
~**%s ""**"" %s**
', T_('two bold'), T_('stars'));
printf('<<');
printf(T_('In this case, the second set of %s (escaped) stars will be ignored, %si.e.,%s will not terminate the bold markup.'), '""**""', '//', '//');
printf('--- ---');
printf(T_('You can also use double-quote escaping to avoid linking of %s words, e.g.'), '""CamelCase""');
printf('---');
printf('<<');
printf('
~##""&quot;&quot;WikiWord&quot;&quot; %s""##
', T_('is not parsed as a link'));
printf('
~""WikiWord"" %s
', T_('is not parsed as a link'));
printf('<<::c::');
printf('===%s===', T_('1. Text Formatting'));
printf('---');
printf('<<');
printf('
~##""**%s**""##
', T_('I\'m bold'));
printf('
~**%s**
', T_('I\'m bold'));
printf('<<::c::');
printf('<<');
printf('
~##""//%s//""##
', T_('I\'m italic text!'));
printf('
~//%s//
', T_('I\'m italic text!'));
printf('<<::c::');
printf('<<');
printf('
~##""__%s__""##
', T_('And I\'m underlined!'));
printf('
~__%s__
', T_('And I\'m underlined!'));
printf('<<::c::');
printf('<<');
printf('
~##""##%s##""##
', T_('Monospace text'));
printf('
~##%s##
', T_('Monospace text'));
printf('<<::c::');
printf('<<');
printf('
~##""\'\'%s\'\'""## (%s)
', T_('Highlighted text'), T_('using 2 single quotes'));
printf('
~\'\'%s\'\'
', T_('Highlighted text'));
printf('<<::c::');
printf('<<');
printf('
~##""++%s++""##
', T_('Strike through text'));
printf('
~++%s++
', T_('Strike through text'));
printf('<<::c::');
printf('<<');
printf('
~##""&pound;&pound;%s&pound;&pound;""##
', T_('Text insertion'));
printf('
~ &pound;&pound;Text insertion&pound;&pound;
', T_('Text insertion'));
printf('<<::c::');
printf('<<');
printf('
~##""&yen;&yen;%s&yen;&yen;""##
', T_('Text deletion'));
printf('
~ &yen;&yen;%s&yen;&yen;
', T_('Text deletion'));
printf('<<::c::');
printf('<<');
printf('
~##""#%%%s#%%""##
', T_('Press any key'));
printf('
~#%%%s#%%
', T_('Press any key'));
printf('<<::c::');
printf('<<');
printf('
~##""@@%s@@""##
', T_('Center text'));
printf('
~@@%s@@
', T_('Center text'));
printf('<<::c::');
printf(T_('Elides (hides) content from displaying.  Eliminates trailing whitespace so there are no unsightly gaps in output. Useful for commenting Wikka markup.'));
printf('---');
printf('<<');
printf('
~##""/*%s*/""##
', T_('Elided content (eliminates trailing whitespace)'));
printf('
~/*%s*/
', T_('Elided content (eliminates trailing whitespace)'));
printf('
<--//%s//
', T_('there was a comment here followed by whitespace in the markup'));
printf('<<::c::');
printf(T_('Elides (hides) content from displaying.  Preserves trailing
whitespace (note the gap).'));
printf('---');
printf('<<');
printf('
~##""``%s``""##
', T_('Elided content (preserves trailing whitespace)'));
printf('
~``%s``
', T_('Elided content (preserves trailing whitespace)'));
printf('
<--//%s//
', T_('there was a comment here followed by ws in the markup'));
printf('<<::c::');
printf('---');
printf('===%s===', T_('2. Headers'));
printf('---');
printf(T_('Use between six %s (for the biggest header) and two %s (for the smallest header) on both sides of a text to render it as a header.'), '##=##', '##=##');
printf('--- ---');
printf('<<');
printf('
~##""======%s======""##
', T_('Really big header'));
printf('
~======%s======
', T_('Really big header'));
printf('<<::c::');
printf('<<');
printf('
~##""=====%s=====""##
', T_('Rather big header'));
printf('
~=====%s=====
', T_('Rather big header'));
printf('<<::c::');
printf('<<');
printf('
~##""====%s====""##
', T_('Medium header'));
printf('
~====%s====
', T_('Medium header'));
printf('<<::c::');
printf('<<');
printf('
~##""===%s===""##
', T_('Not-so-big header'));
printf('
~===%s===
', T_('Not-so-big header'));
printf('<<::c::');
printf('<<');
printf('
~##""==%s==""##
', T_('Smallish header'));
printf('
~==%s==
', T_('Smallish header'));
printf('<<::c::');
printf('---');
printf('===%s===', T_('3. Horizontal separator'));
printf('
~##""----""##
');
printf('
----
');
printf('---');
printf('===%s===', T_('4. Forced line break'));
printf('---');
printf('
~##""%s---%s""##
', T_('Line 1'), T_('Line 2'));
printf('
%s---%s
', T_('Line 1'), T_('Line 2'));
printf('---');
printf('===%s===', T_('5. Lists and indents'));
printf('---');
printf(T_('You can indent text using a tilde (~), a tab, or four spaces (which will auto-convert into a tab).'));
printf('---');
printf('<<');
printf('
##""~%s<br />~~%s<br />&nbsp;&nbsp;&nbsp;&nbsp;%s""## ', T_('This text is indented'), T_('This text is double-indented'), T_('This text is also indented'));
printf('<<::c::');
printf('<<');
printf('
~%s
~~%s
~%s
', T_('This text is indented'), T_('This text is double-indented'), T_('This text is also indented'));
printf('<<::c::');
printf('---');
printf(T_('To create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a tilde):'));
printf('--- ---');
printf('**%s**', T_('Bulleted lists'));
printf('---');
printf('<<');
printf('
##""~- %s""##
##""~- %s""##
', T_('Line one'), T_('Line two'));
printf('<<::c::');
printf('<<');
printf('
~- %s
~- %s
', T_('Line one'), T_('Line two'));
printf('<<::c::');
printf('---');
printf('**%s**', T_('Numbered lists'));
printf('---');
printf('<<');
printf('
##""~1) %s""##
##""~1) %s""##
', T_('Line one'), T_('Line two'));
printf('<<::c::');
printf('<<');
printf('
~1) %s
~1) %s
', T_('Line one'), T_('Line two'));
printf('<<::c::');
printf('---');
printf('**%s**', T_('Ordered lists using uppercase characters'));
printf('---');
printf('<<');
printf('
##""~A) %s""##
##""~A) %s""##
', T_('Line one'), T_('Line two'));
printf('<<::c::');
printf('<<');
printf('
~A) %s 
~A) %s 
', T_('Line one'), T_('Line two'));
printf('<<::c::');
printf('---');
printf('**%s**', T_('Ordered lists using lowercase characters'));
printf('---');
printf('<<');
printf('
##""~a) %s""##
##""~a) %s""##
', T_('Line one'), T_('Line two')); 
printf('<<::c::');
printf('<<');
printf('
~a) %s 
~a) %s
', T_('Line one'), T_('Line two'));  
printf('<<::c::');
printf('---');
printf('**%s**', T_('Ordered lists using uppercase Roman numerals'));
printf('---');
printf('<<');
printf('
##""~I) %s""##
##""~I) %s""##
', T_('Line one'), T_('Line two'));  
printf('<<::c::');
printf('<<');
printf('
~I) %s 
~I) %s
', T_('Line one'), T_('Line two'));  
printf('<<::c::');
printf('---');
printf('**%s**', T_('Ordered lists using lowercase Roman numerals'));
printf('---');
printf('<<');
printf('
##""~i) %s""##
##""~i) %s""##
', T_('Line one'), T_('Line two'));   
printf('<<::c::');
printf('<<');
printf('
~i) %s 
~i) %s 
', T_('Line one'), T_('Line two'));    
printf('<<::c::');
printf('---');
printf('===%s===', T_('6. Inline comments'));
printf('---');
printf(T_('To format some text as an inline comment, use an indent (tilde, tab, or four spaces) followed by a %s.'), '**""&amp;""**');
printf('--- ---');
printf('<<');
printf('
##""~&amp; %s""##
##""~~&amp; %s""##
##""~~~&amp; %s""##
', T_('Comment'), T_('Subcomment'), T_('Subsubcomment'));
printf('<<::c::');
printf('<<');
printf('
~& %s 
~~& %s 
~~~& %s 
', T_('Comment'), T_('Subcomment'), T_('Subsubcomment')); 
printf('<<::c::');
printf('--- ---');
printf('===%s===', T_('7. Images'));
printf('---');
printf(T_('To place images on a Wiki page, you can use the %s action.'), '##image##');
printf('--- ---');
printf(T_('Image links can be external or internal Wiki links. You don\'t need to enter a link at all, and in that case just an image will be inserted. You can use the optional classes %s and %s to float images left and right. You don\'t need to use all those attributes, only %s is required while %s is recommended for
accessibility.'), '##left##', '##right##', '##url##', '##alt##');
printf('--- ---');
printf('<<');
printf('
~##""{{image class="center" alt="%s" title="%s" url="images/dvdvideo.gif" link="RecentChanges"}}""##
', T_('DVD logo'), T_('An image link'));
printf('---');
printf('
~{{image class="center" alt="%s" title="%s" url="images/dvdvideo.gif" link="RecentChanges"}}
', T_('DVD logo'), T_('An image link')); 
printf('<<::c::');
printf('---');
printf('===%s===', T_('8. Links'));
printf('---');
printf(T_('To create a %s link to a wiki page %s you can use any of the
following options:'), '**', '**');
printf('--- ---');
printf('
~- %s ##""WikiName""## (%s): --- --- ##""FormattingRules""## --- FormattingRules --- ---
', T_('Type a'), T_('works only for page names with no whitespace'));
printf('---');
printf('
~- %s ##""[[ target URL or page | description ]]""## (%s): --- --- ##""[[SandBox | %s]]""## --- [[SandBox | %s]] --- --- ##""[[SandBox | &#27801;&#31665;]]""## --- [[SandBox | &#27801;&#31665;]] --- --- ##""[[http://docs.wikkawiki.org | Wikka %s]]""## --- [[http://docs.wikkawiki.org | Wikka %s]] --- --- ##""[[community@wikkawiki.org | Wikka %s]]""## --- [[community@wikkawiki.org | Wikka %s]] --- ---', 
T_('Add a forced link surrounding the page name by'),
T_('everything after the | will be shown as description'), T_('Test
your formatting skills'), T_('Test your formatting skills'),
T_('documentation'), T_('documentation'), T_('community list'),
T_('community list'));
printf('---');
printf('
~- %s
', T_('Add an image with a link (see instructions above)'));
printf('--- ---');
printf(T_('To %s link to external pages %s, you can do any of the following:'), '**', '**');
printf('--- ---');
printf('
~- %s: --- --- ##""http://blog.wikkawiki.org""## --- http://blog.wikkawiki.org --- --- 
', T_('Type a URL inside the page'));
printf('---');
printf('
~- %s 
', T_('Add an image with a link (see instructions above)'));
printf('---');
printf('
~- %s ([[InterWiki | %s]]): --- --- ##""WikiPedia:WikkaWiki""## --- WikiPedia:WikkaWiki --- --- ##""Google:CSS""## --- Google:CSS --- --- ##""Thesaurus:%s""## --- Thesaurus:%s --- ---
', T_('Add an interwiki link'), T_('browse the list of available interwiki tags'), T_('Happy'), T_('Happy'));
printf('--- ---');
printf('===%s===', T_('9. Tables'));
printf('---');
printf('<<');
printf(T_('The %s action has been deprecated as of Wikka version 1.2 and has been replaced with the syntax that follows. Please visit the [[Docs:TableActionInfo|Wikka %s]] for information about the older %s action.'), '##table##', T_('documentation server'), '##table##');
printf('<<::c::');
printf('---');
printf(T_('Tables can be created using two pipe (%s) symbols.  Everything in a single line is rendered as a table row.'), '##""||""##');
printf('--- ---');
printf('**%s**', T_('Example:'));
printf('--- ---');
printf('
##""||Cell 1||Cell 2||""##

||Cell 1||Cell 2||
');
printf('--- ---');
printf(T_('Header cells can be rendered by placing an equals sign between the pipes.'));
printf('--- ---');
printf('**%s**', T_('Example:'));
printf('
##""|=|Header 1|=|Header 2||""##
##""||Cell 1||Cell 2||""##

|=|Header 1|=|Header 2||
||Cell 1||Cell 2||
');
printf('--- ---');
printf(T_('Row and column spans are specified with %s and %s in parentheses just after the pipes.'), '##x:##', '##y:##');
printf('--- ---');
printf('**%s**', T_('Example:'));
printf('--- ---');
printf('
##""|=| |=|(x:2)Columns||""##
##""|=|(y:2) Rows||Cell 1||Cell 2||""##
##""||Cell 3||Cell 4||""##

|=| |=|(x:2)Columns||
|=|(y:2) Rows||Cell 1||Cell 2||
||Cell 3||Cell 4||
');
printf('--- ---');
printf(T_('Many additional features are available using table markup.  A
more comprehensive table markup guide is available on this server\'s
TableMarkup page. A complete syntax reference is available on the document
server [[Docs:TableMarkupReference TableMarkupReference]] page.'));
printf('--- ---');
printf('===%s===', T_('10. Colored Text'));
printf('---');
printf(T_('Colored text can be created using the %s action:'), '##color##');
printf('---');
printf('<<');
printf('
~##""{{color c="blue" text="%s"}}""##
~{{color c="blue" text="%s"}}
', T_('This is a test'), T_('This is a test'));
printf('<<::c::');
printf('---');
printf(T_('You can also use hex values:'));
printf('---');
printf('<<');
printf('
~##""{{color hex="#DD0000" text="%s"}}""## 
~{{color hex="#DD0000" text="%s"}}
', T_('This is another test'), T_('This is another test'));
printf('<<::c::');
printf('---');
printf(T_('Alternatively, you can specify a foreground and background color using the %s and %s parameters (they accept both named and hex values):'), '##fg##', '##bg##');
printf('---');
printf('<<');
printf('
~##""{{color fg="#FF0000" bg="#000000" text="%s"}}""##
~{{color fg="#FF0000" bg="#000000" text="%s"}}
', T_('This is colored text on colored background'), T_('This is colored text on colored background'));
printf('<<::c::');
printf('<<');
printf('
~##""{{color fg="yellow" bg="black" text="This is colored text on colored background"}}""##
~{{color fg="yellow" bg="black" text="This is colored text on colored background"}}
', T_('This is colored text on colored background'), T_('This is colored text on colored background'));
printf('<<::c::');
printf('--- ---');
printf('===%s===', T_('11. Floats'));
printf('---');
printf(T_('To create a %s left floated box %s, use two %s characters before and after the block.'), '**', '**', '##<##');
printf('--- ---');
printf('**%s**', T_('Example:'));
printf('--- ---');
printf('
~'.T_('%s Some text in a left-floated box hanging around. %s Some more text as a filler. Some more text as a filler. Some more text as a filler.  Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.%s')
, '~##""&lt;&lt;', '&lt;&lt;', '""##');
printf('--- ---');
printf(T_('%s Some text in a left-floated box hanging around. %s Some more text as a filler. Some more text as a filler. Some more text as a filler.  Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.'), '<<', '<<');
printf('
::c::
');
printf(T_('To create a %s right floated box %s, use two %s characters before and after the block.'), '**', '**', '##>##');
printf('--- ---');
printf('**%s**', T_('Example:'));
printf('--- ---');
printf('
~'.T_('%s Some text in a right-floated box hanging around. %s Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.%s'), '##"">>', '>>', '""##');
printf('--- ---');
printf(T_('%s Some text in a right-floated box hanging around. %s Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.'), '>>', '>>');
printf('
::c::
');
printf(T_('%s Note: Use %s to clear floated blocks. %s'), '**', '##""::c::""##', '**');
printf('--- ---');
printf('===%s===', T_('12. Code formatters'));
printf('---');
printf(T_('You can easily embed code blocks in a wiki page using a simple markup. Anything within a code block is displayed literally.  To create a %s generic code block %s you can use the following markup:'), '**', '**');
printf('--- ---');
printf('
~##""%%%% %s %%%%""##. 
', T_('This is a code block'));
printf('
%%%% This is a code block %%%%
');
printf('---');
printf(T_('To create a %s code block with syntax highlighting %s, you need to specify a %s code formatter %s (see below for a list of available code formatters).'), '**', '**', '//', '//'); 
printf('--- ---');
printf('
~##""%%%%(""{{color c="red" text="php"}}"")<br />&lt;?php<br />echo "%s";<br />?&gt;<br />%%%%""##
', T_('Hello, World!'));
printf('---');
printf('
%%%%(php)
<?php
echo "%s";
?>
%%%%
', T_('Hello, World!'));
printf('--- ---');
printf(T_('You can also specify an optional %s starting line %s number.'), '//', '//');
printf('---');
printf('
~##""%%%%(php;""{{color c="red" text="15"}}"")<br />&lt;?php<br />echo "%s";<br />?&gt;<br />%%%%""##
', T_('Hello, World!'));
printf('---');
printf('
%%%%(php;15)
<?php
echo "Hello, World!";
?>
%%%%
', T_('Hello, World!'));
printf('--- ---');
printf(T_('If you specify a %s filename %s, this will be used for downloading the code.'), '//', '//');
printf('---');
printf('
~##""%%%%(php;15;""{{color c="red" text="test.php"}}"")<br />&lt;?php<br />echo "%s";<br />?&gt;<br />%%%%""##
', T_('Hello, World!'));
printf('---');
printf('
%%%%(php;15;test.php)
<?php
echo "%s";
?>
%%%%
', T_('Hello, World!'));
printf('--- ---');
printf('
|?|%s||
||
|=|%s|=|%s|=|%s|=|%s|=|%s|=|%s||
|#|
|=|Actionscript||##actionscript##|=|ABAP||##abap##|=|ADA||##ada##||
|=|Apache Log||##apache##|=|""AppleScript""||##applescript##|=|ASM||##asm##||
|=|ASP||##asp##|=|""AutoIT""||##autoit##|=|Bash||##bash##||
|=|""BlitzBasic""||##blitzbasic##|=|""Basic4GL""||##basic4gl##|=|bnf||##bnf##||
|=|C||##c##|=|C for Macs||##c_mac##|=|C#||##csharp##||
|=|C""++""||##cpp##|=|C""++"" (+QT)||##cpp-qt##|=|CAD DCL||##caddcl##||
|=|""CadLisp""||##cadlisp##|=|CFDG||##cfdg##|=|""ColdFusion""||##cfm##||
|=|CSS||##css##|=|CSV ""<sup>†</sup""||##csv##|=|D||##d##||
|=|Delphi||##delphi##|=|Diff-Output||##diff##|=|DIV||##div##||
|=|DOS||##dos##|=|Dot||##dot##|=|Eiffel||##eiffel##||
|=|Fortran||##fortran##|=|""FreeBasic""||##freebasic##|=|FOURJ\'s Genero 4GL||##genero##||
|=|GML||##gml##|=|Groovy||##groovy##|=|Haskell||##haskell##||
|=|HTML||##html4strict##|=|INI||##ini##|=|Inno Script||##inno##||
|=|Io||##io##|=|Java 5||##java5##|=|Java||##java##||
|=|Javascript||##javascript##|=|""LaTeX""||##latex##|=|Lisp||##lisp##||
|=|Lua||##lua##|=|Matlab||##matlab##|=|mIRC Scripting||##mirc##||
|=|Microchip Assembler||##mpasm##|=|Microsoft Registry||##reg##|=|Motorola 68k Assembler||##m68k##||
|=|""MySQL""||##mysql##|=|NSIS||##nsis##|=|Objective C||##objc##||
|=|""OpenOffice"" BASIC||##oobas##|=|Objective Caml||##ocaml##|=|Objective Caml (brief)||##ocaml-brief##||
|=|Oracle 8||##oracle8##|=|Pascal||##pascal##|=|Per (FOURJ\'s Genero 4GL)||##per##||
|=|Perl||##perl##|=|PHP||##php##|=|PHP (brief)||##php-brief##||
|=|PL/SQL||##plsql##|=|Python||##phyton##|=|Q(uick)BASIC||##qbasic##||
|=|robots.txt||##robots##|=|Ruby on Rails||##rails##|=|Ruby||##ruby##||
|=|SAS||##sas##|=|Scheme||##scheme##|=|sdlBasic||##sdlbasic##||
|=|Smarty||##smarty##|=|SQL||##sql##|=|TCL/iTCL||##tcl##||
|=|T-SQL||##tsql##|=|Text||##text##|=|thinBasic||##thinbasic##||
|=|Unoidl||##idl##|=|VB.NET||##vbnet##|=|VHDL||##vhdl##||
|=|Visual BASIC||##vb##|=|Visual Fox Pro||##visualfoxpro##|=|""WinBatch""||##winbatch##||
|=|XML||##xml##|=|X""++""||##xpp##|=|""ZiLOG"" Z80 Assembler||##z80##||
""<sup>†</sup>"" CSV is not handled by GeSHi, but  by a Wikka handler: [[http://docs.wikkawiki.org/FormatterCSV|FormatterCSV]]
', T_('List of available code formatters'), T_('Language'), T_('Formatter'), T_('Language'), T_('Formatter'), T_('Language'), T_('Formatter'));
printf('--- ---');
printf('===%s===', T_('13. Mindmaps'));
printf('---');
printf(T_('Wikka has native support for %smindmaps%s.  There are two options for embedding a mindmap in a wiki page.'), '[[Wikka:FreeMind|', ']]');
printf('--- ---');
printf(T_(' %sOption 1:%s Upload a %s file to a webserver, and then place a link to it on a wikka page: %s No special formatting is necessary.  '), '**', '**', '""FreeMind""', '##""http://yourdomain.com/freemind/freemind.mm""##');
printf('--- ---');
printf(T_('%sOption 2:%s Paste the %s data directly into a wikka page:
'), '**', '**', '""FreeMind""');
printf('
~-'.T_('Open a %s file with a text editor.').'
~-'.T_('Select all, and copy the data.').'
~-'.T_('Browse to your Wikka site and paste the Freemind data into a page.'), '""FreeMind""'); 
printf('
::c::
');
printf('---');
printf('===%s===', T_('14. Embedded HTML'));
printf('---');
printf(T_('You can easily paste HTML in a wiki page by wrapping it into two sets of doublequotes.')); 
printf('--- ---');
printf('
~##&quot;&quot;[html code]&quot;&quot;##
');
printf('<<');
printf('
~##&quot;&quot;y = x<sup>n+1</sup>&quot;&quot;##
~""y = x<sup>n+1</sup>""
');
printf('<<::c::');
printf('<<');
printf('
~##&quot;&quot;<acronym title="Cascade Style Sheet">CSS</acronym>&quot;&quot;##
~""<acronym title="Cascade Style Sheet">CSS</acronym>""
');
printf('<<::c::');
printf('---');
printf(T_('By default, some HTML tags are removed by the %s parser to protect against potentially dangerous code.  The list of tags that are stripped can be found on the %s page.'), '""SafeHTML""', 'Wikka:SafeHTML');
printf('--- ---');
printf(T_('It is possible to allow %s all %s HTML tags to be used, see %s for more information.'), '//', '//', 'Wikka:UsingHTML');
printf('--- --- ----');
printf('CategoryWiki');
?>
