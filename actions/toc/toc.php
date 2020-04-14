<?php
/**
 * Generate a Table of Contents (ToC) based upon headings
 *
 * This action generates a Table of Contents (ToC) based upon
 * headings.  The ToC is displayed with heading
 * strings indented depending upon the order of the "levels" attribute
 * (see below).  Please note that this action * requires the use of the
 * post-processing action symbols: {{{toc}}}.
 *
 * To place the ToC in a right-justified text box, try
 *
 * >>{{{toc}}}>>::c::
 *
 * @package		Post-processing actions
 * @version		$Id$
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @since		Wikka 1.3.6
 *
 * @input		string $levels  optional: Designate order of 
 *                                        indentation for heading levels
 *              Specify a comma-delimited list of heading designators
 *              (h1 through h5) corresponding to indentation level
 *              desired
 *				default: "h1,h2,h3,h4,h5"
 *
 * @input		string $title  optional: Title of text box 
 *				default: "Table of Contents" 
 *
 * @input		boolean $links  optional: Whether or not ToC links are
 *                                        linked to headings
 *              default: "true"
 *
 * @output		Table of contents based upon headings
 *
 */

$levelMarkupMap = array( "~-", "~~-", "~~~-", "~~~~-", "~~~~~-" );

# 'levels' option
$levels = "h1,h2,h3,h4,h5";
if(isset($vars['levels']))
	$levels = $this->htmlspecialchars_ent($vars['levels']);

# 'title' option
$title = "Table of Contents";
if(isset($vars['title'])) 
	$title = $this->htmlspecialchars_ent($vars['title']);

$links = true;
if(isset($vars['links']) && FALSE !== $vars['links'])
	$links = false;

# Set up level map
$levels = array("h1", "h2", "h3", "h4", "h5");
if(isset($vars['levels']))
	$levels = preg_split("/,/", $this->htmlspecialchars_ent($vars['levels']));
# Truncate array if necessary
while(count($levels) > 5) array_pop($levels);
$errorMsg = '<em class="error">'.sprintf(T_("Action error: The only valid levels for the toc action are h1, h2, h3, h4, and h5.")).'</em>';
foreach($levels as $level) {
	if(!in_array($level, array("h1","h2","h3","h4","h5"))) {
		print $errorMsg;
		return;
	}
}
	
# Parse foreach level markup, store in string
$displayString = "@@" . $title . "@@\n";
$text = $this->config['text'];
foreach(preg_split("/\n/", $text) as $line) {
	if(preg_match("/<h([1-5]).*?id=\"(.*?)\">.*?<a(.*?)>(.*?)<\/a><\/h[1-5]>/", $line, $matches)) {
		$level = $matches[1];
		$indentLevel = array_search('h'.$level, $levels);
		if(FALSE !== $level && FALSE !== $indentLevel) {
			if(TRUE === $links)
				$displayString .= $levelMarkupMap[$indentLevel] 
								  . "\"\"<a href=\""
								  . $this->Href('', $this->tag)
								  . "#"
								  . $matches[2]
								  . "\">"
								  . $matches[4]
								  . "</a><br />\"\"\n";
			else
				$displayString .= $levelMarkupMap[$indentLevel]
				                  . $matches[4]
								  . "\n";
		}
	}
}	
print $this->Format($displayString);
?>
