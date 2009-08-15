<?php
/**
 * Wikka Formatting Engine
 * 
 * This is the main formatting engine used by Wikka to parse wiki markup and render valid XHTML.
 * 
 * @package Formatters
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author {@link http://wikkawiki.org/JsnX Jason Tourtelotte}
 * @author {@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @author {@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author {@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (code cleanup)
 * @author {@link http://wikkawiki.org/DarTar Dario Taraborelli} (grab handler and filename support for codeblocks)
 * @author {@link http://wikkawiki.org/TormodHaugen Tormod Haugen} (table formatter support)
 * 
 * @uses	Wakka::htmlspecialchars_ent()
 * 
 * @todo		add support for formatter plugins;
 * @todo		use a central RegEx library #34;
 */

// i18n strings
if (!defined('GRABCODE_BUTTON_VALUE')) define('GRABCODE_BUTTON_VALUE', 'Grab');
if (!defined('GRABCODE_BUTTON_TITLE')) define('GRABCODE_BUTTON_TITLE', 'Download %s');

// code block patterns
if (!defined('PATTERN_OPEN_BRACKET')) define('PATTERN_OPEN_BRACKET', '\(');
if (!defined('PATTERN_FORMATTER')) define('PATTERN_FORMATTER', '([^;\)]+)');
if (!defined('PATTERN_LINE_NUMBER')) define('PATTERN_LINE_NUMBER', '(;(\d*?))?');
if (!defined('PATTERN_FILENAME')) define('PATTERN_FILENAME', '(;([^\)\x01-\x1f\*\?\"<>\|]*)([^\)]*))?');
if (!defined('PATTERN_CLOSE_BRACKET')) define('PATTERN_CLOSE_BRACKET', '\)');
if (!defined('PATTERN_CODE')) define('PATTERN_CODE', '(.*)');

// Note: all possible formatting tags have to be in a single regular expression for this to work correctly.

if (!function_exists("wakka2callback")) # DotMG [many lines] : Unclosed tags fix!
{
	function wakka2callback($things)
	{
		$thing = $things[0];
		$result='';
		$valid_filename = '';
		
		static $oldIndentLevel = 0;
		static $oldIndentLength= 0;
		static $indentClosers = array();
		static $newIndentSpace= array();
		static $br = 1;
                static $trigger_table = 0;
                static $trigger_rowgroup = 0;
                static $trigger_colgroup = 0;
 		static $trigger_bold = 0;
		static $trigger_italic = 0;
		static $trigger_underline = 0;
		static $trigger_monospace = 0;
		static $trigger_notes = 0;
		static $trigger_strike = 0;
		static $trigger_inserted = 0;
		static $trigger_deleted = 0;
		static $trigger_floatl = 0;
		static $trigger_keys = 0;
		static $trigger_strike = 0;
		static $trigger_inserted = 0;
		static $trigger_center = 0;
		static $trigger_l = array(-1, 0, 0, 0, 0, 0);
		static $output = '';
		static $invalid = '';
		static $curIndentType;

		global $wakka;

		if ((!is_array($things)) && ($things == 'closetags'))
		{
                        if (3 < $trigger_table) echo ('</caption>');
                        elseif (2 < $trigger_table) echo ('</th></tr>');
                        elseif (1 < $trigger_table) echo ('</td></tr>');
                        if (2 < $trigger_rowgroup) echo ('</tbody>');
                        elseif (1 < $trigger_rowgroup) echo ('</tfoot>');
                        elseif (0 < $trigger_rowgroup) echo ('</thead>');
                        if (0 < $trigger_table) echo ('</table>');
			if ($trigger_strike % 2) echo ('</span>');
			if ($trigger_notes % 2) echo ('</span>');
			if ($trigger_inserted % 2) echo ('</div>');
			if ($trigger_deleted % 2) echo ('</div>');
			if ($trigger_underline % 2) echo('</span>');
			if ($trigger_floatl % 2) echo ('</div>');
			if ($trigger_center % 2) echo ('</div>');
			if ($trigger_italic % 2) echo('</em>');
			if ($trigger_monospace % 2) echo('</tt>');
			if ($trigger_bold % 2) echo('</strong>');
			for ($i = 1; $i<=5; $i ++)
				if ($trigger_l[$i] % 2) echo ("</h$i>");
			$trigger_bold = $trigger_center = $trigger_floatl = $trigger_inserted = $trigger_deleted = $trigger_italic = $trigger_keys = 0;
			$trigger_l = array(-1, 0, 0, 0, 0, 0);
			$trigger_monospace = $trigger_notes = $trigger_strike = $trigger_underline = 0;
			return;
		}
                // Ignore the closing delimiter if there is nothing to close.
                elseif ( preg_match("/^\|\|\n$/", $thing, $matches) && $trigger_table == 1 )
                {
                        return '';
                }

                // $matches[1] is element, $matches[2] is attributes, $matches[3] is styles and $matches[4] is linebreak
                elseif ( preg_match("/^\|([^\|])?\|(\(.*?\))?(\{.*?\})?(\n)?$/", $thing, $matches) )
                {
                        for ( $i = 1; $i < 5; $i++ ) #38
                        {
                                if (!isset($matches[$i])) $matches[$i] = '';
                        }
                        //Set up the variables that will aggregate the html markup
                        $close_part = '';
                        $open_part  = '';
                        $linebreak_after_open = '';
                        $selfclose = '';

                        // $trigger_table == 0 means no table, 1 means in table but no cell, 2 is in datacell, 3 is in headercell, 4 is in caption.

                        //If we have parsed the caption, close it, set trigger = 1 and return.
                        if ( $trigger_table == 4 )
                        {
                                $close_part = '</caption>'."\n";
                                $trigger_table = 1;
                                return $close_part;
                        }

                        //If we have parsed a cell - close it, go on to open new.
                        if ( $trigger_table == 3 )
                        {
                                $close_part = '</th>';
                        }
                        elseif ( $trigger_table == 2 )
                        {
                                $close_part = '</td>';
                        }
                        // If no cell, or we want to open a table; then there is nothing to close
                        elseif ( $trigger_table == 1 || $matches[1] == '!')
                        {
                                $close_part = '';
                        }
                        else
                        {
                                //This is actually opening the table (i.e. nothing at all to close). Go on to open a cell.
                                $trigger_table = 1;
                                $close_part = '<table class="data">'."\n";
                        }

                        //If we are in a cell and there is a linebreak - then it is end of row.
                        if ( $trigger_table > 1 && $matches[4] == "\n" )
                        {
                                $trigger_table = 1;
                                return $close_part .= '</tr>'."\n"; //Can return here, it is closed-
                        }

                        //If we were in a colgroup and there is a linebreak, then it is the end.
                        if ( $trigger_colgroup == 1 && $matches[4] == "\n" )
                        {
                                $trigger_colgroup = 0;
                                return $close_part .= '</colgroup>'."\n"; //Can return here, it is closed-
                        }

                        //We want to start a new table, and most likely have attributes to parse.
                        //TODO: Need to find out if class="data" should be auto added, and if so - put it in the attribute list to add up.
                        if ( $matches[1] == '!' )
                        {
                                $trigger_table = 1;
                                $open_part = '<table class="data"';
                                $linebreak_after_open = "\n";
                        }
                        //Open a caption.
                        elseif ( $matches[1] == '?' )
                        {
                                $trigger_table = 4;
                                $open_part = '<caption';
                        }
                        //Start a rowgroup.
                        elseif ( $matches[1] == '#' || $matches[1] == '[' || $matches[1] == ']' )
                        {
                                //If we're here, we want to close any open rowgroup.
                                if (2 < $trigger_rowgroup)
                                {
                                        $close_part .= '</tbody>'."\n";
                                }
                                elseif (1 < $trigger_rowgroup)
                                {
                                        $close_part .= '</tfoot>'."\n";
                                }
                                elseif (0 < $trigger_rowgroup)
                                {
                                        $close_part .= '</thead>'."\n";
                                }

                                //Then open the appropriate rowgroup.
                                if ($matches[1] == '[' )
                                {
                                        $open_part .= '<thead';
                                        $trigger_rowgroup = 1;
                                }
                                elseif ($matches[1] == ']' )
                                {
                                        $open_part .= '<tfoot';
                                        $trigger_rowgroup = 2;
                                }
                                else
                                {
                                        $open_part .= '<tbody';
                                        $trigger_rowgroup = 3;
                                }

                                $linebreak_after_open = "\n";
                        }
                        //Here we want to add colgroup.
                        elseif ( $matches[1] == '_' )
                        {
                                //close any open colgroup
                                if ( $trigger_colgroup == 1 )
                                {
                                        $close_part .= '</colgroup>'."\n";
                                }

                                $trigger_colgroup = 1;
                                $open_part .= '<colgroup';
                        }
                        //And col elements
                        elseif ( $matches[1] == '-' )
                        {
                                $open_part .= '<col';
                                $selfclose = ' /';
                                if ( $matches[4] ) $linebreak_after_open = "\n";
                        }
                        //Ok, then it is cells.
                        else
                        {
                                $open_part = '';
                                //Need a tbody if no other rowgroup open.
                                if ($trigger_rowgroup == 0)
                                {
                                        $open_part .= '<tbody>'."\n";
                                        $trigger_rowgroup = 3;
                                }

                                //If no row, open a new one.
                                if ( $trigger_table == 1 )
                                {
                                        $open_part .= '<tr>';
                                }

                                //Header cell.
                                if ( $matches[1] == '=' )
                                {
                                        $trigger_table = 3;
                                        $open_part .= '<th';
                                }
                                //Datacell
                                else
                                {
                                        $trigger_table = 2;
                                        $open_part .= '<td';
                                }
                        }

                        //If attributes...
                        if ( preg_match("/\((.*)\)/", $matches[2], $attribs ) )
                        {
                                $hints = array();
                                //allow / disallow different attribute keys. (ie. data/header cell only.
                                if ($trigger_table == 2 || $trigger_table == 3)
                                {
                                        $hints['cell'] = 'cell';
                                }
                                else
                                {
                                        $hints['other_table'] = 'other_table';
                                }
                                $open_part .= parse_attributes($attribs[1], $hints);
                        }

                        //If styles, just make attribute of it and parse again.
                        if ( preg_match("/\{(.*)\}/", $matches[3], $attribs ) )
                        {
                                $attribs = "s:".$attribs[1];
                                $open_part .= parse_attributes($attribs, array() );
                        }

                        //the variable $selfclose is "/" if this is a <col/> element.
                        $open_part .= $selfclose.'>';
                        return $close_part . $open_part . $linebreak_after_open;
                }
                //Are in table, no cell - but not asked to open new: please close and parse again. ;)
                else if ( $trigger_table == 1 )
                {
                        $close_part = '';
                        if (2 < $trigger_rowgroup)
                        {
                                $close_part .= '</tbody>'."\n";
                        }
                        elseif (1 < $trigger_rowgroup)
                        {
                                $close_part .= '</tfoot>'."\n";
                        }
                        elseif (0 < $trigger_rowgroup)
                        {
                                $close_part .= '</thead>'."\n";
                        }

                        $close_part .= '</table>'."\n";

                        $trigger_table = $trigger_rowgroup = 0;

                        //And remember to parse what we got.
                        return $close_part.wakka2callback($things);
                }

		// convert HTML thingies
		if ($thing == "<")
			return "&lt;";
		else if ($thing == ">")
			return "&gt;";
		// float box left
		else if ($thing == "<<")
		{
			return (++$trigger_floatl % 2 ? "<div class=\"floatl\">\n" : "\n</div>\n");
		}
		// float box right
		else if ($thing == ">>")
		{
			return (++$trigger_floatl % 2 ? "<div class=\"floatr\">\n" : "\n</div>\n");
		}
		// clear floated box
		else if ($thing == "::c::")
		{
			return ("<div class=\"clear\">&nbsp;</div>\n");
		}
		// keyboard
		else if ($thing == "#%")
		{
			return (++$trigger_keys % 2 ? "<kbd class=\"keys\">" : "</kbd>");
		}
		// bold
		else if ($thing == "**")
		{
			return (++$trigger_bold % 2 ? "<strong>" : "</strong>");
		}
		// italic
		else if ($thing == "//")
		{
			return (++$trigger_italic % 2 ? "<em>" : "</em>");
		}
		// underlinue
		else if ($thing == "__")
		{
			return (++$trigger_underline % 2 ? "<span class=\"underline\">" : "</span>");
		}
		// monospace
		else if ($thing == "##")
		{
			return (++$trigger_monospace % 2 ? "<tt>" : "</tt>");
		}
		// notes
		else if ($thing == "''")
		{
			return (++$trigger_notes % 2 ? "<span class=\"notes\">" : "</span>");
		}
		// strikethrough
		else if ($thing == "++")
		{
			return (++$trigger_strike % 2 ? "<span class=\"strikethrough\">" : "</span>");
		}
		// additions
		else if ($thing == "&pound;&pound;")
		{
			return (++$trigger_inserted % 2 ? "<ins>" : "</ins>");
		}
		// deletions
		else if ($thing == "&yen;&yen;")
		{
			return (++$trigger_deleted % 2 ? "<del>" : "</del>");
		}
		// center
		else if ($thing == "@@")
		{
			return (++$trigger_center % 2 ? "<div class=\"center\">\n" : "\n</div>\n");
		}
		// urls
		else if (preg_match("/^([a-z]+:\/\/\S+?)([^[:alnum:]^\/])?$/", $thing, $matches))
		{
			$url = $matches[1];
			/* Inline images are disabled for security reason, use {{image action}} #142
			But if you still need this functionality, update this file like below
			if (preg_match("/\.(gif|jpg|png|svg)$/si", $url)) {
				return '<img src="'.$wakka->Link($url).'" alt="image" />'.$wakka->htmlspecialchars_ent($matches[2]);
			} else */
			// Mind Mapping Mod
			if (preg_match("/\.(mm)$/si", $url)) { #145
				return $wakka->Action("mindmap ".$url);
			} else
				$link = $wakka->Link($url);
				if(isset($matches[2])) $link .= $matches[2];
				return $link;
		}
		// header level 5
		else if ($thing == "==")
		{
				$br = 0;
				return (++$trigger_l[5] % 2 ? "<h5>" : "</h5>\n");
		}
		// header level 4
		else if ($thing == "===")
		{
				$br = 0;
				return (++$trigger_l[4] % 2 ? "<h4>" : "</h4>\n");
		}
		// header level 3
		else if ($thing == "====")
		{
				$br = 0;
				return (++$trigger_l[3] % 2 ? "<h3>" : "</h3>\n");
		}
		// header level 2
		else if ($thing == "=====")
		{
				$br = 0;
				return (++$trigger_l[2] % 2 ? "<h2>" : "</h2>\n");
		}
		// header level 1
		else if ($thing == "======")
		{
				$br = 0;
				return (++$trigger_l[1] % 2 ? "<h1>" : "</h1>\n");
		}
		// forced line breaks
		else if ($thing == "---")
		{
			return "<br />";
		}
		// escaped text
		else if (preg_match("/^\"\"(.*)\"\"$/s", $thing, $matches))
		{
			$allowed_double_doublequote_html = $wakka->GetConfigValue("double_doublequote_html");
			if ($allowed_double_doublequote_html == 'safe')
			{
				$filtered_output = $wakka->ReturnSafeHTML($matches[1]);
				return $filtered_output;
			}
			elseif ($allowed_double_doublequote_html == 'raw')
			{
				return $matches[1];
			}
			else
			{
				return $wakka->htmlspecialchars_ent($matches[1]);
			}
		}
		// code text
		else if (preg_match("/^%%(.*?)%%$/s", $thing, $matches))
		{
			/*
			* Note: this routine is rewritten such that (new) language formatters
			* will automatically be found, whether they are GeSHi language config files
			* or "internal" Wikka formatters.
			* Path to GeSHi language files and Wikka formatters MUST be defined in config.
			* For line numbering (GeSHi only) a starting line can be specified after the language
			* code, separated by a ; e.g., %%(php;27)....%%.
			* Specifying >= 1 turns on line numbering if this is enabled in the configuration.
			* An optional filename can be specified as well, e.g. %%(php;27;myfile.php)....%%
			* This filename will be used by the grabcode handler.			
			*/
			$output = ''; //reinitialize variable
			$code = $matches[1];
			// if configuration path isn't set, make sure we'll get an invalid path so we
			// don't match anything in the home directory
			$geshi_hi_path = isset($wakka->config['geshi_languages_path']) ? $wakka->config['geshi_languages_path'] : '/:/';
			$wikka_hi_path = isset($wakka->config['wikka_highlighters_path']) ? $wakka->config['wikka_highlighters_path'] : '/:/';
			// check if a language (and an optional starting line or filename) has been specified
			if (preg_match('/^'.PATTERN_OPEN_BRACKET.PATTERN_FORMATTER.PATTERN_LINE_NUMBER.PATTERN_FILENAME.PATTERN_CLOSE_BRACKET.PATTERN_CODE.'$/s', $code, $matches))
			{
				list(, $language, , $start, , $filename, $invalid, $code) = $matches;
			}
			// get rid of newlines at start and end (and preceding/following whitespace)
			// Note: unlike trim(), this preserves any tabs at the start of the first "real" line
			$code = preg_replace('/^\s*\n+|\n+\s*$/','',$code);
			
			// check if GeSHi path is set and we have a GeSHi highlighter for this language
#			if (isset($language) && isset($wakka->config['geshi_path']) && file_exists($geshi_hi_path.'/'.$language.'.php'))
			if (isset($language) && isset($wakka->config['geshi_path']) && file_exists($geshi_hi_path.DIRECTORY_SEPARATOR.$language.'.php')) #89
			{
				// check if specified filename is valid and generate code block header
				if (isset($filename) && strlen($filename) > 0 && strlen($invalid) == 0) # #34 TODO: use central regex library for filename validation
				{
					$valid_filename = $filename;
					// create code block header
					$output .= '<div class="code_header">';
					// display filename and start line, if specified
					$output .= $filename;
					if (strlen($start)>0)
					{
						$output .= ' (line '.$start.')';
					}
					$output .= '</div>'."\n";
				}
				// use GeSHi for highlighting
				$output .= $wakka->GeSHi_Highlight($code, $language, $start);
			}
			// check Wikka highlighter path is set and if we have an internal Wikka highlighter
#			elseif (isset($language) && isset($wakka->config['wikka_formatter_path']) && file_exists($wikka_hi_path.'/'.$language.'.php') && 'wakka' != $language)
			elseif (isset($language) && isset($wakka->config['wikka_formatter_path']) && file_exists($wikka_hi_path.DIRECTORY_SEPARATOR.$language.'.php') && 'wakka' != $language) #89
			{
				// use internal Wikka highlighter
				$output = '<div class="code">'."\n";
				$output .= $wakka->Format($code, $language);
				$output .= "</div>\n";
			}
			// no language defined or no formatter found: make default code block;
			// IncludeBuffered() will complain if 'code' formatter doesn't exist
			else
			{
				$output = '<div class="code">'."\n";
				$output .= $wakka->Format($code, 'code');
				$output .= "</div>\n";
			}

			// display grab button if option is set in the config file
			if ($wakka->config['grabcode_button'] == '1')
			{
				$output .= $wakka->FormOpen("grabcode");
				// build form
				$output .= '<input type="submit" class="grabcode" name="save" value="'.GRABCODE_BUTTON_VALUE.'" title="'.rtrim(sprintf(GRABCODE_BUTTON_TITLE, $valid_filename)).'" />';
				$output .= '<input type="hidden" name="filename" value="'.urlencode($valid_filename).'" />';
				$output .= '<input type="hidden" name="code" value="'.urlencode($code).'" />';
				$output .= $wakka->FormClose();
			}
			// output
			return $output;
		}
		// forced links
		// \S : any character that is not a whitespace character
		// \s : any whitespace character
		else if (preg_match("/^\[\[(\S*)(\s+(.+))?\]\]$/s", $thing, $matches))		# recognize forced links across lines
		{
			if (isset($matches[1])) // url?
			{
				//if ($url!=($url=(preg_replace("/@@|&pound;&pound;||\[\[/","",$url))))$result="</span>";
				$text = '';
				$url = $matches[1];
				if (isset($matches[3])) $text = $matches[3]; // forced link title
				//$text=preg_replace("/@@|&pound;&pound;|\[\[/","",$text);
				return $result.$wakka->Link($url, "", $text);
			}
			else
			{
				return "";
			}
		}
		// indented text
		elseif (preg_match("/(^|\n)([\t~]+)(-|&|([0-9a-zA-Z]+)\))?(\n|$)/s", $thing, $matches))
		{
			// new line
			$result .= ($br ? "<br />\n" : "\n");

			// we definitely want no line break in this one.
			$br = 0;

			// find out which indent type we want
			$newIndentType = $matches[3];
			if (!$newIndentType) { $opener = "<div class=\"indent\">"; $closer = "</div>"; $br = 1; }
			elseif ($newIndentType == "-") { $opener = "<ul><li>"; $closer = "</li></ul>"; $li = 1; }
			elseif ($newIndentType == "&") { $opener = "<ul class=\"thread\"><li>"; $closer = "</li></ul>"; $li = 1; } #inline comments
			else
			{
				if     (ereg('[0-9]', $newIndentType[0])) { $newIndentType = '1'; }
				elseif (ereg('[IVX]', $newIndentType[0])) { $newIndentType = 'I'; }
				elseif (ereg('[ivx]', $newIndentType[0])) { $newIndentType = 'i'; }
				elseif (ereg('[A-Z]', $newIndentType[0])) { $newIndentType = 'A'; }
				elseif (ereg('[a-z]', $newIndentType[0])) { $newIndentType = 'a'; }

				$opener = '<ol type="'.$newIndentType.'"><li>';
				$closer = '</li></ol>';
				$li = 1;
			}

			// get new indent level
			$newIndentLevel = strlen($matches[2]);
			if (($newIndentType != $curIndentType) && ($oldIndentLevel > 0))
			{
				for (; $oldIndentLevel > $newIndentLevel; $oldIndentLevel --)
				{
					$result .= array_pop($indentClosers);
				}
			}
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

			$curIndentType = $newIndentType;
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
		else if (preg_match("/^[A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+[:]\S*$/s", $thing))
		{
			return $wakka->Link($thing);
		}
		// wiki links!
		else if (preg_match("/^[A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*$/s", $thing))
		{
			return $wakka->Link($thing);
		}
		// separators
		else if (preg_match("/-{4,}/", $thing, $matches))
		{
			// TODO: This could probably be improved for situations where someone puts text on the same line as a separator.
			//	   Which is a stupid thing to do anyway! HAW HAW! Ahem.
			$br = 0;
			return "<hr />\n";
		}
		// mind map xml
		else if (preg_match("/^<map.*<\/map>$/s", $thing))
		{
			return $wakka->Action("mindmap ".$wakka->Href()."/mindmap.mm");
		}
		// if we reach this point, it must have been an accident.
		return $thing;
	}
}

if (!function_exists('parse_attributes'))
{
        function parse_attributes($attribs, $hints) {

                //Sort different attributes / keys to use for different elements.
                static $attributes = array(
                        'core' => array( 'c' => 'class','i' => 'id','s' => 'style','t' => 'title'),
                        'i18n' => array( 'd' => 'dir','l' => 'xml:lang'),
                        'cell' => array( 'a' => 'abbr','h' => 'headers','o' => 'scope','x' => 'colspan','y' => 'rowspan','z' => 'axis'),
                        'other_table' => array( 'p' => 'span','u' => 'summary')
                        );

                //adds in default hints ( core + i18n )
                $hints['core'] = 'core';
                $hints['i18n'] = 'i18n';

                $attribs = preg_split('/;(?=.:)/', $attribs);
                $return_value = '';

                foreach ( $attribs as $attrib )
                {
                        list ($key, $value) = explode(':', $attrib, 2);
                        foreach ( $hints as $hint )
                        {
                                $temp = $attributes[$hint];
                                if ($temp) $a = $temp[$key];
                                if ($a) break;
                        }

                        if (!$a)
                        {
                                //This attribute isn't allowed here / is wrong.
                                // WARNING: JS vulnerability: two minus signs are not allowed in a comment, so we replace any occurence of them by underscore.
                                // Consider the code ||(p--><font size=1px><a href=...<!--:blabla
                                // When migrating to UTF-8, we could use str_replace('--', 'â<88><92>â<88><92>', $key) to make things more pretty. //TODO garbled ... mdash?
                                echo '<!--Cannot find attribute for key "'.str_replace('--', '__', $key).'" from hints given.-->'."\n"; #i18n
                        }
                        else
                        {
                                // WARNING: JS vulnerability: use htmlspecialchars_ent to prevent JS attack!
                                $return_value .= ' '.$a.'="'.$GLOBALS['wakka']->htmlspecialchars_ent($value).'"';
                        }
                }

                return $return_value;
        }
}

$text = str_replace("\r\n", "\n", $text);

// replace 4 consecutive spaces at the beginning of a line with tab character
// $text = preg_replace("/\n[ ]{4}/", "\n\t", $text); // moved to edit.php

if ($this->method == "show") $mind_map_pattern = "<map.*?<\/map>|"; else $mind_map_pattern = "";

$text = preg_replace_callback(
	"/".
	"%%.*?%%|".																				# code
	"\"\".*?\"\"|".																			# literal
	$mind_map_pattern.
	"\[\[[^\[]*?\]\]|".																		# forced link
	"-{3,}|".
			# forced linebreak and separator (hr)
	"\b[a-z]+:\/\/\S+|".																	# URL
	"\*\*|\'\'|\#\#|\#\%|@@|::c::|\>\>|\<\<|&pound;&pound;|&yen;&yen;|\+\+|__|<|>|\/\/|".	# Wiki markup
	"======|=====|====|===|==|".															# headings
	"(^|\n)[\t~]+(-(?!-)|&|([0-9]+|[a-zA-Z]+)\))?|".										# indents and lists
    "\|(?:[^\|])?\|(?:\(.*?\))?(?:\{[^\{\}]*?\})?(?:\n)?|".
	        # simple tables
	"\{\{.*?\}\}|".																			# action
	"\b[A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+[:](?![=_])\S*\b|".											# InterWiki link
	"\b([A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*)\b|".								# CamelWords
	"\n".																					# new line
	"/ms", "wakka2callback", $text."\n"); #append \n (#444)

// we're cutting the last <br />
$text = preg_replace("/<br \/>$/","", $text);

echo ($text);
wakka2callback('closetags');

?>
