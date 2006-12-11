<?php
/**
 * Display a searchbox for searching google.
 *
 * <p>Usage: <tt>{{googleform q="WikkaSites"}}</tt>.
 * <br> The above usage will display a googleform, with search term input field filled with the term <tt>WikkaSites</tt>.
 * If no parameter is supplied, this action will fill the search input field with the name of the page where it lays.
 * The content of the input field is of course editable, maximum length of search query is limited to 2048, as you can see
 * on Google itself.
 * When the user clicks on the button labelled <tt>Google</tt>, a google search will start in a new window.</p>
 * 
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::FormClose()
 * @todo Use [advanced] FormOpen()
 * @todo Add a behaviour scripts to select the content of the input field when focused.
 * @link http://docs.wikkawiki.org/GoogleFormActionInfo
 */

//Note: The classname WBselectonfocus, (WB means Wikka Behaviour) is used to (todo further) let add a behaviour scripts that
//will select the content of the input field when it is focused.
$query = '';

// *** param section ***
if (is_array($vars)) 
{
	foreach ($vars as $param => $value) 
	{
		if ($param == 'q')
		{
			$query = $value;
		}
	}
}
if ($query == '')
{// backward compatibility for {{googleform query}} usage
	$query = (isset($vars['wikka_vars']))  ? $vars['wikka_vars'] : $this->GetPageTag();
}
// Sanitization: Passing $query to htmlspecialchars_ent instead of ReturnSafeHTML(). Inside the value parameter of the input field,
// we definitely don't want any occurence of ", <, > or unescaped &. IMPORTANT: Use doublequote to enclose value supplied by user, W3C
// validation can be broken with syntax like {{googleform q="Wikka's Website"}} if you use singlequote.
$query = $this->htmlspecialchars_ent($query);

// *** output section ***
?>
<form action="http://www.google.com/search" method="get" target="_blank">
	<input type="text" value="<?php echo $query; ?>" name="q" size="30" maxlength="2048" class="WBselectonfocus" /> 
	<input name="btnG" type="submit" value="Google" />
<?php echo $this->FormClose(); ?>
