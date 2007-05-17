<?php
 /**
 * Creates a feed with recent comments
 *
 * This handler generates a list of recently posted comments. Specific filters can be applied as
 * URL parameters. The output format can be specified in the URL.
 *
 * @package		Handlers
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @access	public
 * @since	1.1.7
 * @uses	FeedCreator
 * @uses	Config::$base_url
 * @uses	Config::$wakka_name
 * @uses	Config::$root_page
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::LoadRecentComments()
 * 
 * @todo	move some defaults to wiki configuration files
 * @todo	add flexible filtering support
 * @todo	check feed validation
 * @todo	add better phpdoc documentation
 * @todo	move i18n strings to language file
 * @todo	use stylesheet compatible with multiple feed formats
 * @todo	allow feed for one page only, e.g. Feed for comments on my userpage only. Perhaps by adding params in $_GET.
 *
 * @input	string	$f	optional: output format, can be any of the following: 
 * 				RSS0.91, RSS1.0, RSS2.0, ATOM1.0
 *				default: RSS2.0
 *				the default format can be overridden by providing a URL parameter 'f'.
 * @output	feed for recently changed pages in the specified format.
 */

//defaults
define('FEED_VALID_FORMATS', "RSS0.91,RSS1.0,RSS2.0,ATOM1.0"); 
define('FEED_DESCRIPTION_TRUNCATE_SIZE',"200"); #character limit to truncate description
define('FEED_DESCRIPTION_HTML',"TRUE"); #Indicates whether the description field should be rendered in HTML
define('FEED_DEFAULT_OUTPUT_FORMAT',"RSS2.0"); #any of the valid formats specified in VALID_FORMATS
/*
define('FEED_DEFAULT_USER_FILTER', ''); #empty, modifications by any user
define('FEED_DEFAULT_DAY_LIMIT', 30); #default number of days
define('FEED_DEFAULT_ITEMS_LIMIT', 20); #default number of items to display
*/
//stylesheets & images
define('FEED_CSS',"xml.css");
define('FEED_IMAGE_URL',"/images/wikka_logo.jpg");

//i18n strings
define('FEED_TITLE',"%s - recent comments");
define('FEED_DESCRIPTION',"Recent comments from %s");
define('FEED_IMAGE_TITLE',"Wikka logo");
define('FEED_IMAGE_DESCRIPTION',"Feed provided by Wikka");
define('FEED_ITEM_DESCRIPTION',"By %s");

//initialize variables
$f = ''; #feed format
$n = 50; #number of items
/*
$u = ''; #user
$d = ''; #days limit
*/
//get URL parameters
$formats = explode(",",FEED_VALID_FORMATS);
$f = (in_array($_GET['f'], $formats))? $_GET['f'] : FEED_DEFAULT_OUTPUT_FORMAT;

//create object
include_once('3rdparty/core/feedcreator/feedcreator.class.php'); //TODO: MAKE THIS CONFIGURABLE

//initialize feed (general settings)
$rss = new UniversalFeedCreator(); 
$rss->useCached(); //TODO: make this configurable
$rss->title = sprintf(FEED_TITLE, $this->config['wakka_name']); 
$rss->description = sprintf(FEED_DESCRIPTION, $this->config['wakka_name']); 
$rss->cssStyleSheet = $this->config['base_url'].'css/'.FEED_CSS;
$rss->descriptionTruncSize = FEED_DESCRIPTION_TRUNCATE_SIZE; 
$rss->descriptionHtmlSyndicated = FEED_DESCRIPTION_HTML; 
$rss->link = $this->config['base_url'].$this->config['root_page']; 
$rss->syndicationURL = $this->Href($this->method,'','f='.$f); 

//create feed image
$image = new FeedImage(); 
$image->title = FEED_IMAGE_TITLE;
$image->url = FEED_IMAGE_URL;
$image->link = $this->config['base_url'].$this->config['root_page'];  //FIXME
$image->description = FEED_IMAGE_DESCRIPTION;
$image->descriptionTruncSize = FEED_DESCRIPTION_TRUNCATE_SIZE;
$image->descriptionHtmlSyndicated = FEED_DESCRIPTION_HTML;
$rss->image = $image;


$n = intval($n);
//get feed items
// To optimize memory usage, we should load the minimum item. But, we must load 
// more than what we need, because we may have no rights on some items.
// Twice the number we need is just an arbitrary value!!! (2*$n)
if ($comments = $this->LoadRecentComments(2*$n))
{
	$c = 0;
	foreach ($comments as $comment)
	{
		if (!$this->HasAccess('comment', $comment['page_tag']))
		{
			continue;
		}
		$c++;
		$item = new FeedItem(); 
		$item->title = $comment['page_tag']; 
		$item->link = $this->Href('', $comment['page_tag'], 'show_comments=1').'#comment_'.$comment['id'];
		$item->date = date('r', strtotime($comment['time'])); 
		$item->description = 'By '.$comment['user'].': '.$comment['comment']."\n";
		$item->source = $this->config['base_url'];
		if ($f == 'ATOM1.0' || $f == 'RSS1.0') 
		{
			$item->author = $comment['user']; # RSS0.91 and RSS2.0 require authorEmail
		}
		$rss->addItem($item); 
		if ($c == $n) break;
	}
} 

//print feed
echo $rss->createFeed($f);
?>
