<div class="page">
<?php

/**
 * Revert to revision immediately preceding current version of page
 *
 * This handler reverts the current version of a page to the version 
 * immediately preceding the current version. The previous version is
 * re-created as the new version for auditing purposes. An optional GET 
 * parameter, "comment", is permitted:
 *
 * .../SomePage/revert?comment="Replaces spammed page"
 *
 * This handler can also be called via IncludeBuffered(). Pages to be
 * reverted must be passed in via the IncludeBuffered() $vars
 * argument. Each key of the array is formatted "id_#",
 * where "#" is the page to be reverted. (This strange format is due
 * to the way the PageAdmin mass action formats multiple pages, and
 * will need to be modified here if/when mass actions are updated in
 * PageAdmin.)
 *
 *
 * @name	    Revert	
 *
 * @package		Handlers
 * @subpackage  Page
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @since		Wikka 1.1.6.4
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 *
 * Based upon the Delete handler written by DarTar, NilsLindenberg,
 * and MinusF
 *
 * @uses Wakka::Query()
 * @uses Wakka::LoadAll()
 * @uses Wakka::IsAdmin()
 * @uses Wakka::htmlspecialchars_ent()
 * @uses Wakka::GetPageTag()
 * @uses Wakka::Redirect()
 *
 */

//i18n
if(!defined('DEFAULT_COMMENT')) define ('DEFAULT_COMMENT', 'Reverted to previous revision');
if(!defined('MESSAGE_SUCCESS')) define ('MESSAGE_SUCCESS', 'Reverted to previous version');
if(!defined('MESSAGE_FAILED')) define ('MESSAGE_FAILED', 'Reversion to previous version FAILED!');

$message = MESSAGE_FAILED;
if ($this->IsAdmin())
{
	$comment = DEFAULT_COMMENT;
	if(isset($_GET['comment']))
	{
		$comment = $this->htmlspecialchars_ent($_GET['comment']);
	}
	// If GET params of form "id_#" have been passed, then this
	// handler was called from the pageadmin handler!
	$tags = array();
	foreach($_GET as $key=>$val)
	{
		if(FALSE !== strpos($key, "id_"))
		{
			$id = substr($key, strpos($key,'_')+1);
			$res = $this->LoadPageById($id);
			array_push($tags, $res['tag']);
		}
	}
	if(count($tags)==0)
	{
		array_push($tags, mysql_real_escape_string($this->GetPageTag()));
	}

	foreach($tags as $tag)
	{
		// Select current version of this page and version immediately preceding
		$res = $this->LoadAll("SELECT * FROM ".$this->config['table_prefix']."pages WHERE tag='".$tag."' ORDER BY time DESC LIMIT 2");
		if($res && 2===count($res))
		{
			// $res[0] is current page, $res[1] is page we're reverting to
			$time = strftime("%F %H:%M:%S");
			$body = $res[1]['body'];
			$owner = $res[1]['owner'];
			$user = $res[1]['user'];
			$latest = 'Y';
			$handler = $res[1]['handler'];
			$this->Query("INSERT INTO ".$this->config['table_prefix']."pages (tag, time, body, owner, user, latest, note, handler) VALUES ('$tag', '$time', '$body', '$owner', '$user', '$latest', '$comment', '$handler')");
			// Reset 'latest' flag on older version to 'N'
			$this->Query("UPDATE ".$this->config['table_prefix']."pages SET latest='N' where id=".$res[0]['id']);
			$message = MESSAGE_SUCCESS;
		}
	}
	// Redirect to page
	if(count($tags)==1)
	{
		$this->Redirect($this->Href(), $message);
	}
	else
	{
		$this->Redirect($this->Href());
	}
}

?>
</div>
