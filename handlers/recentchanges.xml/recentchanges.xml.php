<?php
 /**
 * Generates a feed for recently changed pages.
 *
 * This handler generates a list of recently changed pages in the wiki. Specific filters can be applied as
 * URL parameters. The output format can be specified in the URL.
 *
 * @package		Handlers
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (rewrite using FeedCreator)
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @access	public
 * @since	1.0.0
 *
 * @uses	FeedCreator
 * @uses	Config::$base_url
 * @uses	Config::$wakka_name
 * @uses	Config::$root_page
 * @uses	Config::$xml_recent_changes
 * @uses	Wakka::existsUser()
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::LoadRecentlyChanged()
 * @uses	Wikka::StaticHref()
 *
 * @todo	move some defaults to wiki configuration files
 * @todo	add flexible filtering support
 * @todo	check feed validation
 * @todo	add better phpdoc documentation
 * @todo	move i18n strings to language file
 * @todo	use stylesheet compatible with multiple feed formats
 *
 * @input	string	$f	optional GET parameter: output format, can be any of the
 *					following: RSS0.91, RSS1.0, RSS2.0, ATOM1.0
 *					default: RSS2.0
 *					the default can be overridden by providing a URL parameter 'f'.
 * @output	feed for recently changed pages in the specified format.
 * @todo	replace htmlspecialchars() in FeedCreator with our secure version (!)
 * @todo	either do not escape a <link> in FeedCreator, or feed it a URL that
 *			does not already have '&' in a URL escaped (as Href() is doing!)
 *			because it gets "double-escaped" now; in fact, I think it should not
 *			be escaped at all in a feed, only in HTML
 * @todo	replace current feed image 'images/wikka_logo.jpg' by a more
 *			appropriate smaller feed image
 */

/**#@+
 * Default value.
 */
if (!defined('FEED_VALID_FORMATS')) define('FEED_VALID_FORMATS', 'RSS0.91,RSS1.0,RSS2.0,ATOM1.0');	// no whitespace around commas!
if (!defined('FEED_DESCRIPTION_TRUNCATE_SIZE')) define('FEED_DESCRIPTION_TRUNCATE_SIZE', 200); #character limit to truncate description	// expects integer
if (!defined('FEED_DESCRIPTION_HTML')) define('FEED_DESCRIPTION_HTML',TRUE); #Indicates whether the description field should be rendered in HTML	// expects boolean
if (!defined('FEED_DEFAULT_OUTPUT_FORMAT')) define('FEED_DEFAULT_OUTPUT_FORMAT','RSS2.0'); #any of the valid formats specified in VALID_FORMATS
//if (!defined('FEED_DEFAULT_OWNER_FILTER')) define('FEED_DEFAULT_OWNER_FILTER', ''); #empty, modifications of pages owned by any user
//if (!defined('FEED_DEFAULT_USER_FILTER')) define('FEED_DEFAULT_USER_FILTER', ''); #empty, modifications by any user
//if (!defined('FEED_DEFAULT_CATEGORY_FILTER')) define('FEED_DEFAULT_CATEGORY_FILTER', ''); #empty, pages belonging to any category
//if (!defined('FEED_DEFAULT_DAY_LIMIT')) define('FEED_DEFAULT_DAY_LIMIT', 30); #default number of days
//if (!defined('FEED_DEFAULT_ITEMS_LIMIT')) define('FEED_DEFAULT_ITEMS_LIMIT', 20); #default number of items to display
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
if (!defined('FEED_TITLE_RECENT_CHANGES')) define('FEED_TITLE_RECENT_CHANGES',"%s - recently changed pages");	// %s - name of the wiki
if (!defined('FEED_DESCRIPTION_RECENT_CHANGES')) define('FEED_DESCRIPTION_RECENT_CHANGES',"New and recently changed pages from %s");	// %s - name of the wiki
if (!defined('FEED_IMAGE_TITLE')) define('FEED_IMAGE_TITLE',"Wikka logo");
if (!defined('FEED_IMAGE_DESCRIPTION')) define('FEED_IMAGE_DESCRIPTION',"Feed provided by Wikka");
if (!defined('FEED_ITEM_DESCRIPTION')) define('FEED_ITEM_DESCRIPTION',"By %s");	// %s - user name
/**#@-*/

//initialize variables
$f = ''; #feed format	empty string will force default format
/*
$o = ''; #owner
$u = ''; #username
$c = ''; #category
$d = ''; #days limit
$n = ''; #number of items
*/

//get URL parameters
$formats = explode(',',FEED_VALID_FORMATS);
#$f = (in_array($_GET['f'], $formats))? $_GET['f'] : FEED_DEFAULT_OUTPUT_FORMAT;
$f = (isset($_GET['f']) && in_array($_GET['f'], $formats)) ? $_GET['f'] : FEED_DEFAULT_OUTPUT_FORMAT;

//create object
#include_once('3rdparty'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'feedcreator'.DIRECTORY_SEPARATOR.'feedcreator.class.php'); // MAKE THIS CONFIGURABLE
$feedcreator_classpath = $this->GetConfigValue('feedcreator_path').DIRECTORY_SEPARATOR.'feedcreator.class.php';
/**
 * FeedCreator class file.
 */
include_once $feedcreator_classpath;
#$rss = new UniversalFeedCreator();
$rss = instantiate('UniversalFeedCreator');

//initialize feed (general settings)
$rss->useCached(); //TODO: make this configurable
$rss->title = sprintf(FEED_TITLE_RECENT_CHANGES, $this->GetConfigValue('wakka_name')); 
$rss->description = sprintf(FEED_DESCRIPTION_RECENT_CHANGES, $this->GetConfigValue('wakka_name')); 
$rss->cssStyleSheet = $this->StaticHref('css/'.FEED_CSS);
$rss->descriptionTruncSize = FEED_DESCRIPTION_TRUNCATE_SIZE;
$rss->descriptionHtmlSyndicated = FEED_DESCRIPTION_HTML;
$rss->link = $this->Href('', $this->GetConfigValue('root_page'));
$rss->syndicationURL = $this->Href($this->handler,'','f='.$f); 

//create feed image
#$image = new FeedImage();
$image = instantiate('FeedImage');
$image->title = FEED_IMAGE_TITLE;
$image->url = FEED_IMAGE_URL;
$image->link = $this->Href('', $this->GetConfigValue('root_page'));
$image->description = FEED_IMAGE_DESCRIPTION;
$image->descriptionTruncSize = FEED_DESCRIPTION_TRUNCATE_SIZE;
$image->descriptionHtmlSyndicated = FEED_DESCRIPTION_HTML;
$rss->image = $image;

//get feed items
if ($pages = $this->LoadRecentlyChanged())
{
	$max = (int) $this->GetConfigValue('xml_recent_changes');
	$c = 0;
	foreach ($pages as $page)
	{
		$c++;
		if (($this->HasAccess('read', $page['tag'])) && (($c <= $max) || !$max))
		{
			#$item = new FeedItem();
			$item = instantiate('FeedItem');
			$item->title = $page['tag'];
			#$item->link = $this->Href('show', $page['tag'], 'time='.urlencode($page['time'])); 
			$item->link = $this->Href('', $page['tag'], 'time='.urlencode($page['time']));
			// @@@ ^ uses &amp;amp; in all formats - this is FC escaping the &amp; that Href() outputs
			// WARNING: the double escape comes from the use of htmlspecialchars()
			// 1. we need to replace this with our own secure version
			// 2. we should NOT use it on already-escaped links -OR- feed it
			//    links that that have & not escaped (yet)
			$item->date = date('r',strtotime($page['time']));	// RFC2822
			$item->description = sprintf(FEED_ITEM_DESCRIPTION, $page['user']).($page['note'] ? ' ('.$page['note'].')' : '')."\n";
			#$item->source = $this->GetConfigValue('base_url');
			#$item->source = $this->base_url;	// home page
			// @@@ JW: ^ should link to *actual* page not root
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
				$item->source = $this->Href('', $page['tag']);	// correct, I think, but actually redundant -- JW
			}
			#if (($f == 'ATOM1.0' || $f == 'RSS1.0') && $this->LoadUser($page['user']))	//only check for existence of user here
			if (('ATOM1.0' == $f || 'RSS1.0' == $f) && $this->existsUser($page['user']))	// check for existence of user
			{
				$item->author = $page['user']; # RSS0.91 and RSS2.0 require authorEmail
			}
			$rss->addItem($item);
		}
	}
}

//output feed
echo $rss->createFeed($f);
?>
