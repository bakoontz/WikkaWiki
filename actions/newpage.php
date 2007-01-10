<?php
/**
 * Display a form to create a new page.
 * 
 * @package Actions
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author 	{@link http://www.comawiki.org/CoMa.php?CoMa=Costal_Martignier Costal Martignier} (initial version)
 * @author	{@link http://wikkawiki.org/JsnX JsnX} (modified 2005-1-17)
 * @author	{@link http://wikkawiki.org/JavaWoman JavaWoman} (modified 2005-1-17)
 * 
 * @uses	Wakka::redirect()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 * @filesource
 */

$showform = TRUE;

if (isset($_POST['pagename']))
{
	$pagename = $_POST['pagename'];

	if (!(preg_match("/^[A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*$/s", $pagename))) 
	{
		echo '<em>'.sprintf(ERROR_INVALID_PAGE_NAME, $pagename).'</em>';
	}
	else 
	{
		$url = $this->config['base_url'];
		$this->redirect($url.$pagename.'/edit');
		$showform = FALSE;
	}
}

if ($showform)
{ ?>
	<br />
	<?php echo $this->FormOpen(); ?>
		<input type="text" name="pagename" size="50" value="<?php echo $pagename; ?>" />  
		<input type="submit" value="<?php echo NEW_PAGE_FORM_LABEL; ?>" />
	<?php echo $this->FormClose(); 
} 
?>