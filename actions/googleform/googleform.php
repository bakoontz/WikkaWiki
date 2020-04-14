<?php
/**
 * Display a searchbox for searching google.
 *
 * <p>Usage: <kbd>{{googleform q="WikkaSites"}}</kbd>.
 * <br> The above usage will display a googleform, with search term input field filled with the term <tt>WikkaSites</tt>.
 * If no parameter is supplied, this action will fill the search input field with the name of the page where it lays.
 * The content of the input field is of course editable, maximum length of search query is limited to 2048, as you can see
 * on Google itself.
 * When the user clicks on the button labelled <tt>Google</tt>, a google search will start in a new window.</p>
 *
 * @package		Actions
 * @version		$Id: googleform.php 1196 2008-07-16 04:25:09Z BrianKoontz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::FormClose()
 * @link	http://docs.wikkawiki.org/GoogleFormActionInfo
 *
 * @todo	Use [advanced] FormOpen() - which should accept an external URL (and
 *			extra attributes)
 * @todo	Add a behaviour script to select the content of the input field when
 *			focused.
 *			JW: whatever for? it's mostly annoying, and not accessible either
 *			(unexpected behavior)!
 * @todo	[accessibility] Add a label with a prompt
 * @todo	[accessibility] Add signal for the user that the result will open in
 *			a new window
 * @todo	define maxlength in a constant - no "magic numbers"!
 */

// Note: The classname WBselectonfocus, (WB means Wikka Behaviour) is used to (todo further) let add a behaviour scripts that
// will select the content of the input field when it is focused.
$query = '';

// *** param section ***
if (is_array($vars))
{
	foreach ($vars as $param => $value)
	{
		$value = $this->htmlspecialchars_ent($value);
		if ($param == 'q')
		{
			$query = $value;
		}
	}
}
if ($query == '')
{
	// backward compatibility for {{googleform query}} usage
	#$query = (isset($vars['wikka_vars'])) ? $vars['wikka_vars'] : $this->GetPageTag();
	$query = (isset($vars['wikka_vars'])) ?  $this->htmlspecialchars_ent($vars['wikka_vars']) : $this->GetPageTag();
}
// Sanitization: Passing $query to htmlspecialchars_ent instead of ReturnSafeHTML(). Inside the value parameter of the input field,
// we definitely don't want any occurence of ", <, > or unescaped &. IMPORTANT: Use doublequote to enclose value supplied by user, W3C
// validation can be broken with syntax like {{googleform q="Wikka's Website"}} if you use singlequote.
$query = $this->htmlspecialchars_ent($query);

// *** output section *** @@@
?>
<form action="http://www.google.com/search" method="get" target="_blank">
	<input type="text" value="<?php echo $query; ?>" name="q" size="30" maxlength="2048" class="WBselectonfocus" />
	<input name="btnG" type="submit" value="<?php echo T_("Google");?>" />
<?php echo $this->FormClose(); ?>