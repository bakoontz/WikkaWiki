<?php

// This may look a bit strange, but all possible formatting tags have to be in a single regular expression for this to work correctly. Yup!

if (!function_exists("wakka2callback"))
{
	function wakka2callback($things)
	{
		$thing = $things[1];
		$result='';

		static $oldIndentLevel = 0;
		static $oldIndentLength= 0;
		static $indentClosers = array();
		static $newIndentSpace= array();
		static $br = 1;

		global $wakka;

		// convert HTML thingies
		if ($thing == "<")
			return "&lt;";
		else if ($thing == ">")
			return "&gt;";
		// float box left
		else if ($thing == "<<")
		{
			static $floatl = 0;
			return (++$floatl % 2 ? "<div class=\"floatl\">\n" : "\n</div>\n");
		}
		// float box right
		else if ($thing == ">>")
		{
			static $floatl = 0;
			return (++$floatl % 2 ? "<div class=\"floatr\">\n" : "\n</div>\n");
		}
		// clear floated box
		else if ($thing == "::c::")
		{
			return ("<div class=\"clear\">&nbsp;</div>\n");
		}
		// keyboard
		else if ($thing == "#%")
		{
			static $keys = 0;
			return (++$keys % 2 ? "<kbd class=\"keys\">" : "</kbd>");
		}
		// bold
		else if ($thing == "**")
		{
			static $bold = 0;
			return (++$bold % 2 ? "<strong>" : "</strong>");
		}
		// italic
		else if ($thing == "//")
		{
			static $italic = 0;
			return (++$italic % 2 ? "<em>" : "</em>");
		}
		// underlinue
		else if ($thing == "__")
		{
			static $underline = 0;
			return (++$underline % 2 ? "<span class=\"underline\">" : "</span>");
		}
		// monospace
		else if ($thing == "##")
		{
			static $monospace = 0;
			return (++$monospace % 2 ? "<tt>" : "</tt>");
		}
		// notes
		else if ($thing == "''")
		{
			static $notes = 0;
			return (++$notes % 2 ? "<span class=\"notes\">" : "</span>");
		}
		// strikethrough
		else if ($thing == "++")
		{
			static $strike = 0;
			return (++$strike % 2 ? "<span class=\"deletions\">" : "</span>");
		} 
        // Inserted
        else if ($thing == "&pound;&pound;")
        {
                static $inserted = 0;
                return (++$inserted % 2 ? "<span class=\"additions\">" : "</span>");
        }
		// centre
		else if ($thing == "@@")
		{
			static $center = 0;
			return (++$center % 2 ? "<div class=\"centre\">\n" : "\n</div>\n");
		}         
		// urls
		else if (preg_match("/^([a-z]+:\/\/\S+?)([^[:alnum:]^\/])?$/", $thing, $matches)) {
			$url = $matches[1];
	        // if (preg_match("/^(.*)\.(gif|jpg|png)/si", $url)) return "<img src=\"$url\" />".$matches[2]; 
			// return $wakka->Link($url).$matches[2]; 
			if (preg_match("/^(.*)\.(gif|jpg|png)/si", $url)) return "<img src=\"$url\" />"; 
			return $wakka->Link($url); 
		}
		// header level 5
		else if ($thing == "==")
		{
				static $l5 = 0;
				$br = 0;
				return (++$l5 % 2 ? "<h5>" : "</h5>\n");
		}
		// header level 4
		else if ($thing == "===")
		{
				static $l4 = 0;
				$br = 0;
				return (++$l4 % 2 ? "<h4>" : "</h4>\n");
		}
		// header level 3
		else if ($thing == "====")
		{
				static $l3 = 0;
				$br = 0;
				return (++$l3 % 2 ? "<h3>" : "</h3>\n");
		}
		// header level 2
		else if ($thing == "=====")
		{
				static $l2 = 0;
				$br = 0;
				return (++$l2 % 2 ? "<h2>" : "</h2>\n");
		}
		// header level 1
		else if ($thing == "======")
		{
				static $l1 = 0;
				$br = 0;
				return (++$l1 % 2 ? "<h1>" : "</h1>\n");
		}
		// forced line breaks
		else if ($thing == "---")
		{
			return "<br />";
		}
		// escaped text
		else if (preg_match("/^\"\"(.*)\"\"$/s", $thing, $matches))
		{
			if ($wakka->GetConfigValue("allow_doublequote_html"))
			{
				return $matches[1];
			} else 
			{
				return htmlspecialchars($matches[1]);
			}
		}
		// code text
		else if (preg_match("/^\%\%(.*)\%\%$/s", $thing, $matches))
		{
			// check if a language has been specified
			$code = $matches[1];
			$language = "";
			if (preg_match("/^\((.+?)\)(.*)$/s", $code, $matches))
			{
				list(, $language, $code) = $matches;
			}
			switch ($language)
			{
			case "php":
				$formatter = "php";
				break;
			case "ini":
				$formatter = "ini";
				break;
			case "email":
				$formatter = "email";
				break;
			default:
				$formatter = "code";
			}

			$output = "<div class=\"code\">\n";
			$output .= $wakka->Format(trim($code), $formatter);
			$output .= "</div>\n";

			return $output;
		}
		// forced links
		// \S : any character that is not a whitespace character
		// \s : any whitespace character
		else if (preg_match("/^\[\[(\S*)(\s+(.+))?\]\]$/", $thing, $matches))
		{
			list (, $url, , $text) = $matches;
			if ($url)
			{
				//if ($url!=($url=(preg_replace("/@@|&pound;&pound;||\[\[/","",$url))))$result="</span>";
				if (!$text) $text = $url;
				//$text=preg_replace("/@@|&pound;&pound;|\[\[/","",$text);
				return $result.$wakka->Link($url, "", $text);
			}
			else
			{
				return "";
			}
		}
		// indented text
 		elseif (preg_match("/\n([\t,~]+)(-|([0-9,a-z,A-Z,ÄÖÜ,ßäöü]+)\))?(\n|$)/s", $thing, $matches))
		{
			// new line
			$result .= ($br ? "<br />\n" : "\n");
			
			// we definitely want no line break in this one.
			$br = 0;

			// find out which indent type we want
			$newIndentType = $matches[2];
         if (!$newIndentType) { $opener = "<div class=\"indent\">"; $closer = "</div>"; $br = 1; }
         elseif ($newIndentType == "-") { $opener = "<ul><li>"; $closer = "</li></ul>"; $li = 1; }
         else { $opener = "<ol type=\"". substr($newIndentType, 0, 1)."\"><li>"; $closer = "</li></ol>"; $li = 1; }

			// get new indent level
			$newIndentLevel = strlen($matches[1]);
			if ($newIndentLevel > $oldIndentLevel) 
			{
				for ($i = 0; $i < $newIndentLevel - $oldIndentLevel; $i++)
				{
					$result .= $opener;
					array_push($indentClosers, $closer);
				}
			}
			else if ($newIndentLevel < $oldIndentLevel)
			{
				for ($i = 0; $i < $oldIndentLevel - $newIndentLevel; $i++)
				{
					$result .= array_pop($indentClosers);
				}
			}
			
			$oldIndentLevel = $newIndentLevel;

			if (isset($li) && !preg_match("/".str_replace(")", "\)", $opener)."$/", $result))
			{
				$result .= "</li><li>";
			}

			return $result;
		}
		// new lines
		else if ($thing == "\n")
		{
			// if we got here, there was no tab in the next line; this means that we can close all open indents.
			$c = count($indentClosers);
			for ($i = 0; $i < $c; $i++)
			{
				$result .= array_pop($indentClosers);
				$br = 0;
			}
			$oldIndentLevel = 0;
			$oldIndentLength= 0;
			$newIndentSpace=array();

			$result .= ($br ? "<br />\n" : "\n");
			$br = 1;
			return $result;
		}
		// Actions
		else if (preg_match("/^\{\{(.*?)\}\}$/s", $thing, $matches))
		{
			if ($matches[1])
				return $wakka->Action($matches[1]);
			else
				return "{{}}";
		}
		// interwiki links!
				else if (preg_match("/^[A-Z,ÄÖÜ][A-Z,a-z,ÄÖÜ,ßäöü]+[:]([A-Z,a-z,0-9,ÄÖÜ,ßäöü]*)$/s", $thing))

		{
			return $wakka->Link($thing);
		}
		// wiki links!
		else if (preg_match("/^[A-Z,ÄÖÜ][a-z,ßäöü]+[A-Z,0-9,ÄÖÜ][A-Z,a-z,0-9,ÄÖÜ,ßäöü]*$/s", $thing))
		{
			return $wakka->Link($thing);
		}
		// separators
		else if (preg_match("/-{4,}/", $thing, $matches))
		{
			// TODO: This could probably be improved for situations where someone puts text on the same line as a separator.
			//       Which is a stupid thing to do anyway! HAW HAW! Ahem.
			$br = 0;
			return "<hr />\n";
		}
		// if we reach this point, it must have been an accident.
		return $thing;
	}
}


$text = str_replace("\r", "", $text);
$text = chop($text)."\n";

$text = preg_replace_callback(
	"/(\%\%.*?\%\%|".
	"\"\".*?\"\"|".
	"\[\[.*?\]\]|".
	"-{4,}|---|".
	"\b[a-z]+:\/\/\S+|".
	"\*\*|\'\'|\#\#|\#\%|@@|::c::|\>\>|\<\<|&pound;&pound;|\+\+|__|<|>|\/\/|".
	"======|=====|====|===|==|".
	"\n([\t,~]+)(-|[0-9,a-z,A-Z]+\))?|".
	"\{\{.*?\}\}|".
	"\b[A-Z,ÄÖÜ][A-Z,a-z,ÄÖÜ,ßäöü]+[:]([A-Z,a-z,0-9,ÄÖÜ,ßäöü]*)\b|".
	"\b([A-Z,ÄÖÜ][a-z,ßäöü]+[A-Z,0-9,ÄÖÜ][A-Z,a-z,0-9,ÄÖÜ,ßäöü]*)\b|".
	"\n)/ms", "wakka2callback", $text);
	
/*$pattern2 = "/^(\040|\t)*(?!<|\040)(.+)$/m"; //matches any line with no <element> (and variable leading space) - assume a paragraph
$replace2 = "<p>\\2</p>";
$text=preg_replace($pattern2,$replace2,$text);

$pattern1 = "/^(\040|\t)*(<(?!hr|(\/|)h[1-6]|br|(\/|)li|(\/|)[uo]l|(\/|)div|(\/|)p).*)$/m"; //matches any <element>text lines not considered block formatting
$replace1 = "<p>\\2</p>";
$text=preg_replace($pattern1,$replace1,$text);*/

$pattern3 = "/(\n{2,})/m"; //strips multiple newlines
$replace3 = "\n";
$text=preg_replace($pattern3,$replace3,$text);

// we're cutting the last <br />
$text = preg_replace("/<br \/>$/","", trim($text));
echo ($text);
?>
