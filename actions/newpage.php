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
 *
 * @filesource
 *
 * @todo user central regex library #34
 */

$showform = TRUE;

if (isset($_POST['pagename']))
{
	$pagename = $_POST['pagename'];

	if (!(preg_match("/^[A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*$/s", $pagename))) 
	{
		echo '<em class="error">The page name "'.$pagename.'" is invalid. Valid page names must start with a capital letter, contain only letters and numbers, and be in CamelCase format.</em>';
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
		<input type="submit" value="Create and Edit" />
	<?php echo $this->FormClose(); 
} 
?>