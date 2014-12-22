<?php
/**
 * The Wikka Formatting Engine
 * 
 * This is the main formatting engine used by Wikka to parse wiki markup and render valid XHTML.
 * 
 * @package		Formatters
 * @version		$Id: html.php,v 1.6 2009/06/08 20:27:21 brian Exp brian $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://wikkawiki.org/JsnX Jason Tourtelotte}
 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @author	{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (code cleanup)
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} (grab handler and filename support for codeblocks)
 * @author	{@link http://wikkawiki.org/TormodHaugen Tormod Haugen} (table formatter support)
 * 
 * @uses	Wakka::htmlspecialchars_ent()
 * 
 * @todo	add support for formatter plugins;
 * @todo	use a central RegEx library #34;
 * @todo	add further improvements from ImprovedFormatter
 */

/**#@+
 * Code block pattern.
 */
if (!defined('PATTERN_OPEN_BRACKET')) define('PATTERN_OPEN_BRACKET', '\(');
if (!defined('PATTERN_FORMATTER')) define('PATTERN_FORMATTER', '([^;\)]+)');
if (!defined('PATTERN_LINE_NUMBER')) define('PATTERN_LINE_NUMBER', '(;(\d*?))?');
if (!defined('PATTERN_FILENAME')) define('PATTERN_FILENAME', '(;([^\)\x01-\x1f\*\?\"<>\|]*)([^\)]*))?');
if (!defined('PATTERN_CLOSE_BRACKET')) define('PATTERN_CLOSE_BRACKET', '\)');
if (!defined('PATTERN_CODE')) define('PATTERN_CODE', '(.*)');
/**#@-*/
/**
 * Match heading tags.
 *
 * - $result[0] : the entire node representation, including the closing tag
 * - $result[1] : the nodename (h1, h2, .. , h6)
 * - $result[2] : the heading attribute, ie all the strings after the tagname and before the first ">" character
 * - $result[3] : the content of the heading tag, just like the innerHTML method in DOM.
 * This pattern will match only if the text it is applied to is valid XHTML: it should use lowercase in the tagName,
 * it should not contain the character ">" inside attributes.
 */
if (!defined('PATTERN_MATCH_HEADINGS')) define('PATTERN_MATCH_HEADINGS', '#^<(h[1-6])(.*?)>(.*?)</\\1>$#s');
/**
 * Match id in attributes.
 *
 * - $result[0] : a string like <code>id="h1_id"</code>, starting with the letters id=, and followed by a string
 *   enclosed in either single or double quote. It doesn't match if the term id is not preceded by any whitespace.
 * - $result[1] : The single character used to enclose the string, either a single or a double quote.
 * - $result[2] : The content of the string, ie the value of the id attribute.
 * The RE uses a backref to match both single and double enclosing quotes.
 */
if (!defined('PATTERN_MATCH_ID_ATTRIBUTES')) define('PATTERN_MATCH_ID_ATTRIBUTES', '/(?<=\\s)id=("|\')(.*?)\\1/');
/**
 * The string $format_option is a semicolon separated list of strings, including the word `page'
 */
if (!defined('PATTERN_MATCH_PAGE_FORMATOPTION')) define('PATTERN_MATCH_PAGE_FORMATOPTION', '/(^|;)page(;|$)/');
/**
 * Match "<a " when it isn't preceded by "</a>"
 */
if (!defined('PATTERN_OPEN_A_ALONE')) define('PATTERN_OPEN_A_ALONE', '(?<!</a>|^)<a ');
/**
 * Match the end of a string when the string doesn't end with </a>
 */
if (!defined('PATTERN_END_OF_STRING_ALONE')) define('PATTERN_END_OF_STRING_ALONE', '(?<!</a>)$');
/**
 * Match "</a>" when it is not followed by an opening link markup (<a )
 */
if (!defined('PATTERN_CLOSE_A_ALONE')) define('PATTERN_CLOSE_A_ALONE', '</a>(?!<a |$)');
/**
 * Match the start of a string when the string doesn't start with "<a "
 */
if (!defined('PATTERN_START_OF_STRING_ALONE')) define('PATTERN_START_OF_STRING_ALONE', '^(?!<a )');

// @@@	is this condition handy? would prevent generating IDs on a page fragment
//		- unless formatter is called on those with an explicit foprmat option!
if (isset($format_option) && preg_match(PATTERN_MATCH_PAGE_FORMATOPTION, $format_option))
{
	if (!function_exists('wakka3callback'))
	{
		/**
		 * "Afterburner" formatting: extra handling of already-generated XHTML code.
		 *
		 * 1. For headings:
		 * a) Use heading to derive a document title
		 * b) Ensure every heading has an id, either specified or generated. (May be
		 * extended to generate section TOC data.)
		 * If an id is already specified, that is used without any modification.
		 * If no id is specified, it is generated on the basis of the heading context:
		 * - any image tag is replaced by its alt text (if specified)
		 * - all tags are stripped
		 * - all characters that are not valid in an ID are stripped (except whitespace)
		 * - the resulting string is then used by makedId() to generate an id out of it
		 *
		 * @access	private
		 * @uses	Wakka::HasPageTitle()
		 * @uses	Wakka::SetPageTitle()
		 * @uses	Wakka::CleanTextNode()
		 * @uses	Wakka::makeId()
		 *
		 * @param	array	$things	required: matches of the regex in the preg_replace_callback
		 * @return	string	heading with an id attribute
		 */
		function wakka3callback($things)
		{
			global $wakka;
			$thing = $things[1];

			// heading
			if (preg_match(PATTERN_MATCH_HEADINGS, $thing, $matches))
			{
				list($h_element, $h_tagname, $h_attribs, $h_heading) = $matches;
				// @@@ apply nodeToTextOnly() on $h_heading so stored title is always valid
				if ((!$wakka->HasPageTitle()) && ('h5' > $h_tagname))
				{
					$wakka->SetPageTitle($h_heading);
				}

				if (preg_match(PATTERN_MATCH_ID_ATTRIBUTES, $h_attribs))
				{
					// existing id attribute: nothing to do (assume already treated as embedded code)
					// @@@ we *may* want to gather ids and heading text for a TOC here ...
					// heading text should then get partly the same treatment as when we're creating ids:
					// at least replace images and strip tags - we can leave entities etc. alone - so we end up with
					// plain text-only
					// do this if we have a condition set to generate a TOC
					return $h_element;
				}
				else
				{
					// no id: we'll have to create one
					$headingtext = $wakka->CleanTextNode($h_heading);		// @@@ replace with headingToTextOnly()
					// now create id based on resulting heading text
					$h_id = $wakka->makeId('hn', $headingtext);

					#503 - The text of a heading is now becoming a link to this heading, allowing an easy way to copy link to clipboard.
					// For this, we take the textNode child of a heading, and if it is not enclosed in <a...></a>, we enclose it in 
					// $opening_anchor and $closing_anchor.
					$opening_anchor = '<a class="heading" href="#'.$h_id.'">';
					$closing_anchor = '</a>';
					$h_heading = preg_replace('@('.PATTERN_OPEN_A_ALONE. '|'.PATTERN_END_OF_STRING_ALONE.  ')@', $closing_anchor.'\\0', $h_heading);
					$h_heading = preg_replace('@('.PATTERN_CLOSE_A_ALONE.'|'.PATTERN_START_OF_STRING_ALONE.')@', '\\0'.$opening_anchor, $h_heading);

					// rebuild element, adding id
					return '<'.$h_tagname.$h_attribs.' id="'.$h_id.'">'.$h_heading.'</'.$h_tagname.'>';
				}
			}
			// other elements to be treated go here (tables, images, code sections...)
		}
	}
}
// Note: all possible formatting tags have to be in a single regular expression for this to work correctly.

if (!function_exists("wakka2callback")) # DotMG [many lines] : Unclosed tags fix!
										# JW: NOT a complete fix!
										# see http://wikkawiki.org/ImprovedFormatter#hn_Not_a_complete_solution
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
		static $trigger_floatr = 0;
		static $trigger_keys = 0;
		static $trigger_strike = 0;
		static $trigger_center = 0;
		static $trigger_l = array(-1, 0, 0, 0, 0, 0);
		static $output = '';
		static $invalid = '';
		static $curIndentType;

		global $wakka;

		// @@@	inline elements should be closed before block-level elements
		// 		(see ImprovedFormatter solution again)
		// TEST: are indents closed at end of page now??? (see... again)
		// @@@	<kbd> is missing
		if ((!is_array($things)) && ($things == 'closetags'))
		{
			$result = '';
			if (3 < $trigger_table){
				$result .=  '</caption>';
			}
			elseif (2 < $trigger_table)
			{
				$result .=  '</th></tr>';
			}
			elseif (1 < $trigger_table)
			{
				$result .=  '</td></tr>';
			}
			if (2 < $trigger_rowgroup)
			{
				$result .=  '</tbody>';
			}
			elseif (1 < $trigger_rowgroup)
			{
				$result .=  '</tfoot>';
			}
			elseif (0 < $trigger_rowgroup)
			{
				$result .=  '</thead>';
			}
			if (0 < $trigger_table)
			{
				$result .=  '</table>';
			}

			if ($trigger_strike % 2)
			{
				$result .=  '</span>';
			}
			if ($trigger_notes % 2)
			{
				$result .=  '</span>';
			}
			if ($trigger_inserted % 2)
			{
				$result .=  '</ins>';
			}
			if ($trigger_deleted % 2)
			{
				$result .=  '</del>';
			}
			if ($trigger_underline % 2)
			{
				$result .= '</span>';
			}
			if ($trigger_floatl % 2)
			{
				$result .=  '</div>';
			}
			if ($trigger_floatr % 2)
			{
				$result .=  '</div>';
			}
			if ($trigger_center % 2)
			{
				$result .=  '</div>';
			}
			if ($trigger_italic % 2)
			{
				$result .= '</em>';
			}
			if ($trigger_monospace % 2)
			{
				$result .= '</tt>';
			}
			if ($trigger_bold % 2)
			{
				$result .= '</strong>';
			}

			for ($i = 1; $i<=5; $i ++)
			{
				if ($trigger_l[$i] % 2) $result .=  "</h$i>";
			}

			$trigger_bold = $trigger_center = $trigger_floatl = $trigger_floatr = $trigger_inserted = $trigger_deleted = $trigger_italic = $trigger_keys = $trigger_table = 0;
			$trigger_l = array(-1, 0, 0, 0, 0, 0);
			$trigger_monospace = $trigger_notes = $trigger_strike = $trigger_underline = 0;
			return $result;
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
//				$hints = array('core' => 'core', 'i18n' => 'i18n');
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
		{
			return "&lt;";
		}
		elseif ($thing == ">")
		{
			return "&gt;";
		}
		// float box left
		elseif ($thing == "<<")
		{
			return (++$trigger_floatl % 2 ? '<div class="floatl">' : '</div>');
		}
		// float box right
		elseif ($thing == ">>")
		{
			return (++$trigger_floatr % 2 ? '<div class="floatr">' : '</div>');
		}
		// clear floated element
		elseif ($thing == "::c::")
		{
			return ("<div class=\"clear\">&nbsp;</div>\n");
		}
		// keyboard
		elseif ($thing == "#%")
		{
			return (++$trigger_keys % 2 ? "<kbd class=\"keys\">" : "</kbd>");
		}
		// bold
		elseif ($thing == "**")
		{
			return (++$trigger_bold % 2 ? "<strong>" : "</strong>");
		}
		// italic
		elseif ($thing == "//")
		{
			return (++$trigger_italic % 2 ? "<em>" : "</em>");
		}
		// underlinue
		elseif ($thing == "__")
		{
			return (++$trigger_underline % 2 ? "<span class=\"underline\">" : "</span>");
		}
		// monospace
		elseif ($thing == "##")
		{
			return (++$trigger_monospace % 2 ? "<tt>" : "</tt>");
		}
		// notes
		elseif ($thing == "''")
		{
			return (++$trigger_notes % 2 ? "<span class=\"notes\">" : "</span>");
		}
		// strikethrough
		elseif ($thing == "++")
		{
			return (++$trigger_strike % 2 ? "<span class=\"strikethrough\">" : "</span>");
		}
		// additions
		elseif ($thing == "&pound;&pound;")
		{
			return (++$trigger_inserted % 2 ? "<ins>" : "</ins>");
		}
		// deletions
		elseif ($thing == "&yen;&yen;")
		{
			return (++$trigger_deleted % 2 ? "<del>" : "</del>");
		}
		// center
		elseif ($thing == "@@")
		{
			return (++$trigger_center % 2 ? "<div class=\"center\">\n" : "\n</div>\n");
		}
		// urls (see RFC 1738 <http://www.ietf.org/rfc/rfc1738.txt>)
		elseif (preg_match("/^([a-z]+:\/\/[[:alnum:]\/?;:@&=\.]+[[:alnum:]\/])(.*)$/", $thing, $matches))
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
				return $wakka->Link($url).(isset($matches[2]) ? $matches[2] : ''); #38
		}
		// header level 5
		elseif ($thing == "==")
		{
				$br = 0;
				return (++$trigger_l[5] % 2 ? "<h5>" : "</h5>\n");
		}
		// header level 4
		elseif ($thing == "===")
		{
				$br = 0;
				return (++$trigger_l[4] % 2 ? "<h4>" : "</h4>\n");
		}
		// header level 3
		elseif ($thing == "====")
		{
				$br = 0;
				return (++$trigger_l[3] % 2 ? "<h3>" : "</h3>\n");
		}
		// header level 2
		elseif ($thing == "=====")
		{
				$br = 0;
				return (++$trigger_l[2] % 2 ? "<h2>" : "</h2>\n");
		}
		// header level 1
		elseif ($thing == "======")
		{
				$br = 0;
				return (++$trigger_l[1] % 2 ? "<h1>" : "</h1>\n");
		}
		// forced line breaks
		elseif ($thing == "---")
		{
			return "<br />";
		}
		// escaped text
		elseif (preg_match("/^\"\"(.*)\"\"$/s", $thing, $matches))
		{
			$ddquotes_policy = $wakka->GetConfigValue("double_doublequote_html");
			$embedded = $matches[1];
			if (($ddquotes_policy == 'safe') || ($ddquotes_policy == 'raw'))
			{
				// get tags with id attributes
				# use backref to match both single and double quotes
				$patTagWithId = '((<[a-z][^>]*)((?<=\\s)id=("|\')(.*?)\\4)(.*?>))';	// @@@ #34
				// with PREG_SET_ORDER we get an array for each match: easy to use with list()!
				// we do the match case-insensitive so we catch uppercase HTML as well;
				// SafeHTML will treat this but 'raw' may end up with invalid code!
				$tags2 = preg_match_all('/'.$patTagWithId.'/i', $embedded, $matches2, PREG_SET_ORDER);
				// step through code, replacing tags with ids with tags with new ('repaired') ids
				$tmpembedded = $embedded;
				$newembedded = '';
				for ($i=0; $i < $tags2; $i++)
				{
					list( , $tag, $tagstart, $attrid, $quote, $id, $tagend) = $matches2[$i];    # $attrid not needed, just for clarity
					$parts = explode($tag, $tmpembedded, 2); # split in two at matched tag
					if ($id != ($newid = $wakka->makeId('embed', $id)))    # replace if we got a new value
					{
						$tag = $tagstart.'id='.$quote.$newid.$quote.$tagend;
					}
					$newembedded .= $parts[0].$tag; # append (replacement) tag to first part
					$tmpembedded  = $parts[1]; # after tag: next bit to handle
				}
				$newembedded .= $tmpembedded; # add last part
			}
			switch ($ddquotes_policy)
			{
				case 'safe':
					return $wakka->ReturnSafeHTML($newembedded);
				case 'raw':
					return $newembedded; # may still be invalid code - 'raw' will not be corrected!
				default:
					return $wakka->htmlspecialchars_ent($embedded);	# display only
			}
		}
		// code text
		elseif (preg_match("/^%%(.*?)%%$/s", $thing, $matches))
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
			$output = ''; //reinitialize variables 
			$filename = '';
			$valid_filename = '';
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
			if (isset($language) &&
				isset($wakka->config['geshi_path']) &&
				file_exists($geshi_hi_path.DIRECTORY_SEPARATOR.$language.'.php'))
			{
				// check if specified filename is valid and generate code block header
				if (isset($filename) &&
					strlen($filename) > 0 &&
					strlen($invalid) == 0) # #34 TODO: use central regex library for filename validation
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
			elseif (isset($language) &&
					isset($wakka->config['wikka_formatter_path']) &&
					file_exists($wikka_hi_path.DIRECTORY_SEPARATOR.$language.'.php') && 
					'wakka' != $language)
			{
				// use internal Wikka highlighter
				$output = '<div class="code">'."\n";
				$output .= $wakka->Format($code, $language);
				$output .= "</div>\n";
			}
			// no language defined or no formatter found: make default code block;
			// IncludeBuffered() will complain if 'code' formatter doesn't exist!
			else
			{
				$output = '<div class="code">'."\n";
				$output .= $wakka->Format($code, 'code');
				$output .= "</div>\n";
			}

			// display grab button if option is set in the config file
			if ($wakka->GetConfigValue('grabcode_button') == '1')	// @@@ cast to boolean and compare to TRUE
			{
				$output .= $wakka->FormOpen("grabcode");
				// build form
				$output .= '<input type="submit" class="grabcode" name="save" value="'.GRABCODE_BUTTON.'" title="'.rtrim(sprintf(GRABCODE_BUTTON_TITLE, $valid_filename)).'" />';
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
		// @@@ regex accepts NO non-whitespace before whitespace, surely not correct? [[  something]]
		else if (preg_match("/^\[\[([\S|\.|\/]*)(\s+(.+))?\]\]$/s", $thing, $matches))      # recognize forced links across lines
		{
			if (!isset($matches[1])) $matches[1] = ''; #38
			if (!isset($matches[3])) $matches[3] = ''; #38
			list (, $url, , $text) = $matches;
			if ($url)
			{
				//if ($url!=($url=(preg_replace("/@@|&pound;&pound;||\[\[/","",$url))))$result="</span>";
				$link = $wakka->Link($url, "", $text);
				// Hack to handle relative URIs (i.e., links to files            // in the same directory)
				if(strstr($link, "http://.") || strstr($link, "http:///"))
					$link = preg_replace("/^(.*)http:\/\/(.*)$/",
										 "\${1}\${2}",
										 $link);
				return $result.$link;
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
			
			if (!$newIndentType)
			{
				$opener = "<div class=\"indent\">";
				$closer = "</div>"; $br = 1;
			}
			elseif ($newIndentType == "-")
			{
				$opener = "<ul><li>";
				$closer = "</li></ul>";
				$li = 1;
			}
			elseif ($newIndentType == "&")
			{
				$opener = "<ul class=\"thread\"><li>";
				$closer = "</li></ul>";
				$li = 1;
			} #inline comments
			else
			{
				if (preg_match('[0-9]', $newIndentType[0])) { $newIndentType = '1'; }
				elseif (preg_match('[IVX]', $newIndentType[0])) { $newIndentType = 'I'; }
				elseif (preg_match('[ivx]', $newIndentType[0])) { $newIndentType = 'i'; }
				elseif (preg_match('[A-Z]', $newIndentType[0])) { $newIndentType = 'A'; }
				elseif (preg_match('[a-z]', $newIndentType[0])) { $newIndentType = 'a'; }

				$opener = '<ol type="'.$newIndentType.'"><li>';
				$closer = '</li></ol>';
				$li = 1;
			}

			// get new indent level
			$newIndentLevel = strlen($matches[2]);
			if (($newIndentType != $curIndentType) && ($oldIndentLevel > 0))
			{
				for (; $oldIndentLevel > 0; $oldIndentLevel--)
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
			elseif ($newIndentLevel < $oldIndentLevel)
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
		elseif (preg_match("/^\{\{(.*?)\}\}$/s", $thing, $matches))
		{
			if ($matches[1])
			{
				return $wakka->Action($matches[1]);
			}
			else
			{
				return "{{}}";
			}
		}
		// interwiki links!
		elseif (preg_match("/^[A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+[:]\S*$/s", $thing))
		{
			return $wakka->Link($thing);
		}
		// wiki links!
		elseif (preg_match("/^[A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*$/s", $thing))
		{
			return $wakka->Link($thing);
		}
		// separators
		elseif (preg_match("/-{4,}/", $thing, $matches))
		{
			// TODO: This could probably be improved for situations where someone puts text on the same line as a separator.
			//		Which is a stupid thing to do anyway! HAW HAW! Ahem.
			$br = 0;
			return "<hr />\n";
		}
		// mind map xml
		elseif (preg_match("/^<map.*<\/map>$/s", $thing))
		{
			return $wakka->Action("mindmap ".$wakka->Href()."/mindmap.mm");
		}
		elseif ($thing[0] == '&')
		{
			return ($wakka->htmlspecialchars_ent($thing));
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
				// When migrating to UTF-8, we could use str_replace('--', 'âˆ’âˆ’', $key) to make things more pretty. //TODO garbled ... mdash?
				echo '<!--Cannot find attribute for key "'.str_replace('--', '__', $key).'" from hints given.-->'."\n";	#i18n
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

if ($this->handler == "show") $mind_map_pattern = "<map.*?<\/map>|"; else $mind_map_pattern = "";

$text = preg_replace_callback(
	"/".
	"%%.*?%%|".																				# code
	"\"\".*?\"\"|".																			# literal
	$mind_map_pattern.
	"\[\[[^\[]*?\]\]|".																		# forced link
	"-{3,}|".																				# forced linebreak and hr
	"\b[a-z]+:\/\/\S+|".																	# URL
	"\*\*|\'\'|\#\#|\#\%|@@|::c::|\>\>|\<\<|&pound;&pound;|&yen;&yen;|\+\+|__|<|>|\/\/|".	# Wiki markup
	"======|=====|====|===|==|".															# headings
	"(^|\n)[\t~]+(-(?!-)|&|([0-9]+|[a-zA-Z]+)\))?|".										# indents and lists
	"\|(?:[^\|])?\|(?:\(.*?\))?(?:\{[^\{\}]*?\})?(?:\n)?|".									# Simple Tables	
	"\{\{.*?\}\}|".																			# action
	# "\b[A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+[:](?![=_])\S*\b|".											# InterWiki link
	# "\b([A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*)\b|".								# CamelWords
	'\\&([#a-zA-Z0-9]+;)?|'. #ampersands! Track single ampersands or any htmlentity-like (&...;)
	"\n".																					# new line
	"/ms", "wakka2callback", $text."\n"); #append \n (#444)

// we're cutting the last <br />
$text = preg_replace("/<br \/>$/","", $text);

// @@@ don't report generation time unless some "debug mode" is on
if (isset($format_option) && preg_match(PATTERN_MATCH_PAGE_FORMATOPTION, $format_option))
{
	$text .= wakka2callback('closetags');	// attempt close open tags @@@ may be needed for more than whole page!
	$idstart = getmicrotime(TRUE);
	$text = preg_replace_callback(
		'#('.
		'<h[1-6].*?>.*?</h[1-6]>'.
		// other elements to be treated go here
		')#ms','wakka3callback', $text);
	printf('<!-- Header ID generation took %.6f seconds -->', (getmicrotime(TRUE) - $idstart));	#i18n
}
echo $text;
?>
