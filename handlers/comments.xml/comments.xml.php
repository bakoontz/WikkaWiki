<?php
 /**
 * Creates a feed with recent comments
 *
 * This handler generates a list of recently posted comments. Specific filters can be applied as
 * URL parameters. The output format can be specified in the URL.
 *
 * @package		Handlers
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @version		$Id: comments.xml.php 892 2008-02-07 08:20:26Z DotMG $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @access	public
 * @since	1.1.7
 *
 * @uses	FeedCreator
 * @uses	Config::$wakka_name
 * @uses	Config::$root_page
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::LoadRecentComments()
 * @uses	Wakka::StaticHref()
 *
 * @input	string	$f	optional GET parameter: output format, can be any of the
 *					following: RSS0.91, RSS1.0, RSS2.0, ATOM1.0
 *					default: RSS2.0
 *					the default can be overridden by providing a URL parameter 'f'.
 * @output	feed for recent comments in the specified format.
 * @todo	move some defaults to wiki configuration files
 * @todo	add flexible filtering support
 * @todo	check feed validation
 * @todo	add better phpdoc documentation
 * @todo	move i18n strings to language file
 * @todo	use stylesheet compatible with multiple feed formats
 * @todo	allow feed for one page only, e.g. Feed for comments on my userpage only. Perhaps by adding params in $_GET.
 * @todo	replace htmlspecialchars() in FeedCreator with our secure version (!)
 * @todo	either do not escape a <link> in FeedCreator, or feed it a URL that
 *			does not already have '&' in a URL escaped (as Href() is doing!)
 *			because it gets "double-escaped" now; in fact, I think it should not
 *			be escaped at all in a feed, only in HTML
 *			(for now : use str_replace() to unescape result of Href)
 * @todo	review whether page, or actual comment, should be used for the item
 *			'source' attribute in RSS 1.0
 * @todo	replace current feed image 'images/wikka_logo.jpg' by a more
 *			appropriate smaller feed image
 */

/**#@+
 * Default value.
 */
if (!defined('FEED_VALID_FORMATS')) define('FEED_VALID_FORMATS', 'RSS0.91,RSS1.0,RSS2.0,ATOM1.0'); 
if (!defined('FEED_DESCRIPTION_TRUNCATE_SIZE')) define('FEED_DESCRIPTION_TRUNCATE_SIZE', 200); #character limit to truncate description	// expects integer
if (!defined('FEED_DESCRIPTION_HTML')) define('FEED_DESCRIPTION_HTML',TRUE); #Indicates whether the description field should be rendered in HTML	// expects boolean
if (!defined('FEED_DEFAULT_OUTPUT_FORMAT')) define('FEED_DEFAULT_OUTPUT_FORMAT','RSS2.0'); #any of the valid formats specified in FEED_VALID_FORMATS
//if (!defined('FEED_DEFAULT_USER_FILTER')) define('FEED_DEFAULT_USER_FILTER', ''); #empty, modifications by any user
//if (!defined('FEED_DEFAULT_DAY_LIMIT')) define('FEED_DEFAULT_DAY_LIMIT', 30); #default number of days
//if (!defined('FEED_DEFAULT_ITEMS_LIMIT')) define('FEED_DEFAULT_ITEMS_LIMIT', 20); #default number of items to display @@@
/**#@-*/

/**
 * Stylesheet to be used.
 */
if (!defined('FEED_CSS')) define('FEED_CSS','xml.css');
/**
 * Logo image to be used.
 */
if (!defined('FEED_IMAGE_URL')) define('FEED_IMAGE_URL', $this->StaticHref('images/wikka_logo.jpg'));

/**#@+
 * i18n string.
 */
if (!defined('FEED_TITLE_RECENT_COMMENTS')) define('FEED_TITLE_RECENT_COMMENTS',"%s - recent comments");	// %s - name of the wiki
if (!defined('FEED_DESCRIPTION_RECENT_COMMENTS')) define('FEED_DESCRIPTION_RECENT_COMMENTS',"Recent comments from %s");	// %s - name of the wiki
if (!defined('FEED_IMAGE_TITLE')) define('FEED_IMAGE_TITLE',"Wikka logo");
if (!defined('FEED_IMAGE_DESCRIPTION')) define('FEED_IMAGE_DESCRIPTION',"Feed provided by Wikka");
if (!defined('FEED_ITEM_DESCRIPTION')) define('FEED_ITEM_DESCRIPTION',"By %s");	// %s - user name
/**#@-*/

//initialize variables
$f = ''; #feed format
$n = 50; #number of items	// @@@ turn this (back) into constant!
/*
$u = ''; #user
$d = ''; #days limit
*/
//get URL parameters
$formats = explode(",",FEED_VALID_FORMATS);
#$f = (in_array($_GET['f'], $formats))? $_GET['f'] : FEED_DEFAULT_OUTPUT_FORMAT;
$f = (isset($_GET['f']) && in_array($_GET['f'], $formats)) ?
$this->GetSafeVar('f', 'get') : FEED_DEFAULT_OUTPUT_FORMAT;

//create object
#include_once('3rdparty'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'feedcreator'.DIRECTORY_SEPARATOR.'feedcreator.class.php');
$feedcreator_classpath = $this->GetConfigValue('feedcreator_path').DIRECTORY_SEPARATOR.'feedcreator.class.php';
/**
 * FeedCreator class library.
 */
include_once $feedcreator_classpath;
#$rss = new UniversalFeedCreator();
$rss = instantiate('UniversalFeedCreator');

//initialize feed (general settings)
$rss->useCached(); //TODO: make this configurable
$rss->title = sprintf(FEED_TITLE_RECENT_COMMENTS, $this->GetConfigValue('wakka_name')); 
$rss->description = sprintf(FEED_DESCRIPTION_RECENT_COMMENTS, $this->GetConfigValue('wakka_name'));
$rss->cssStyleSheet = str_replace('&amp;', '&', $this->StaticHref('css/'.FEED_CSS));
$rss->descriptionTruncSize = FEED_DESCRIPTION_TRUNCATE_SIZE;
$rss->descriptionHtmlSyndicated = FEED_DESCRIPTION_HTML;
$rss->link = str_replace('&amp;', '&', $this->Href('', $this->GetConfigValue('root_page')));
$rss->syndicationURL = str_replace('&amp;', '&', $this->Href($this->handler,'','f='.$f));

//create feed image
#$image = new FeedImage();
$image = instantiate('FeedImage');
$image->title = FEED_IMAGE_TITLE;
$image->url = FEED_IMAGE_URL;
$image->link = str_replace('&amp;', '&', $this->Href('', $this->GetConfigValue('root_page')));
$image->description = FEED_IMAGE_DESCRIPTION;
$image->descriptionTruncSize = FEED_DESCRIPTION_TRUNCATE_SIZE;
$image->descriptionHtmlSyndicated = FEED_DESCRIPTION_HTML;
$rss->image = $image;


$n = (int) $n;
// get feed items
// To optimize memory usage, we should load the minimum items. But, we must load
// more than what we need, because we may have no rights on some items.
// Twice the number we need is just an arbitrary value!!! (2*$n)
if ($comments = $this->LoadRecentComments(2*$n))
{
	$c = 0;
	foreach ($comments as $comment)
	{
		if (!$this->HasAccess('comment_read', $comment['page_tag']))
		{
			continue;
		}
		$c++;
		#$item = new FeedItem();
		$item = instantiate('FeedItem');
		$item->title = $comment['page_tag']; 
		$item->link = str_replace('&amp;', '&', $this->Href('', $comment['page_tag'], 'show_comments=1').'#comment_'.$comment['id']);
		// @@@ ^ uses &amp;amp; in all formats - this is FC escaping the &amp; that Href() outputs
		// WARNING: the double escape comes from the use of htmlspecialchars()
		// see also recentchanges.xml.php
		$item->date = date('r', strtotime($comment['time'])); 
		$item->description = 'By '.$comment['user'].': '.$comment['comment']."\n";
		#$item->source = $this->GetConfigValue('base_url');
		#$item->source = $this->base_url;	// home page
		// @@@ ^ JW: should link to actual comment, or (maybe) the page that has the comment
/*
http://dublincore.org/documents/1999/07/02/dces/
Element: Source

  Name:        Source
  Identifier:  Source
  Definition:  A Reference to a resource from which the present resource
               is derived.
  Comment:     The present resource may be derived from the Source resource
               in whole or in part.  Recommended best practice is to reference 
               the resource by means of a string or number conforming to a 
               formal identification system.
*/
		if ('RSS1.0' == $f)		// dc:source used only here
		{
			$item->source = str_replace('&amp;', '&', $this->Href('', $comment['page_tag']));	// use page, rather than comment, for now
		}
		#if ($f == 'ATOM1.0' || $f == 'RSS1.0')
		if (('ATOM1.0' == $f || 'RSS1.0' == $f) && $this->existsUser($comment['user']))	// check for existence of user
		{
			$item->author = $comment['user']; # RSS0.91 and RSS2.0 require authorEmail
		}
		$rss->addItem($item);

		if ($c == $n)					// enough!
		{
			break;
		}
	}
} 

//output feed
echo $rss->createFeed($f);
?>
