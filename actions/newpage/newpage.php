<?php
/**
 * Display a form to create a new page.
 *
 * @package	Actions
 * @version	$Id:newpage.php 369 2007-03-01 14:38:59Z DarTar $
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author 	{@link http://www.comawiki.org/CoMa.php?CoMa=Costal_Martignier Costal Martignier} (initial version)
 * @author	{@link http://wikkawiki.org/JsnX JsnX} (modified 2005-1-17)
 * @author	{@link http://wikkawiki.org/JavaWoman JavaWoman} (modified 2005-1-17)
 * @uses	Wakka::Redirect()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::GetSafeVar()
 * @uses	Wakka::ExistsPage()
 * @uses	Wakka::Href()
 * @uses	Wakka::IsWikiName()
 * @input	int		$size	optional: size of input box (40 is default)
 * @input	int		$menu	optional: format as menulet (0 is default)	
 * @output	input text box to create new page
 * @filesource
 *
 * @todo user central regex library #34
 */
if (!defined('NEWPAGE_INVALID_CHARS')) define('NEWPAGE_INVALID_CHARS', '| ? = &lt; &gt; / \ " % &amp;');

$showform = TRUE;
$pagename = '';
$size = 40;
$menu = 0;
$maxlength = 40;
// *** User input section ***
if (is_array($vars))
{
	foreach ($vars as $param => $value)
	{
		$value = $this->htmlspecialchars_ent($value);
		if ($param == 'size')
		{
			if(!is_numeric($value)) {
				$size = 40;
			} else {
				$size = $value;
			}
		}
		if($param == 'menu')
		{
			if(is_numeric($value) && $value > 0) {
				$menu = 1;
			}
		}
	}
}
if (isset($_POST['pagename']))
{
	$pagename = $this->GetSafeVar('pagename', 'post');

	if(!$this->IsWikiName($pagename))
	{
		echo '<em class="error">'.sprintf(T_("This page name is
		invalid. Valid page names must not contain the characters
		%s."), NEWPAGE_INVALID_CHARS).'<br/></em>';
	}
	else if ($this->ExistsPage($pagename))
	{
		echo '<em class="error">'.T_("Sorry, the target page already exists").'<br/></em>';
	}
	else
	{
        $this->SetRedirectMessage('Please provide a new page name to create');
        $this->Redirect();
	}
}

if ($showform)
{ 
	if(!$menu) { //Not a menulet
	  ?>
		<?php echo $this->FormOpen(); ?>
			<fieldset class="newpage"><legend><?php echo T_("Create a new page"); ?></legend>
			<input type="text" name="pagename" size="40" value="<?php echo $pagename; ?>" />
			<input type="submit" value="<?php echo T_("Create"); ?>" />
			</fieldset>
		<?php echo $this->FormClose();
	} else { // Menulet formatting
	  ?>
		<?php echo $this->FormOpen(); ?>
			<input type="text" name="pagename" size=<?php echo $size; ?> maxlength=<?php echo $maxlength; ?> value="<?php echo $pagename; ?>" /> <input type="submit" value="<?php echo T_("New page"); ?>" />
		<?php echo $this->FormClose();
	}
}
?>
