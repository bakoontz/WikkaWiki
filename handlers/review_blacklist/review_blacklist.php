<?php
/**
 * Display, filter and search a list of blacklisted domains.
 *
 * Usage: append /review_blacklist to the URL of the page
 *
 * This handler allows logged-in users to display and search the blacklist; an admin may
 * remove blacklisted domains from the database.
 *
 * @package		Handlers
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman} - code cleanup, search/filter functionality added, valid XHTML, accessibility
 * @since		Wikka 1.1.7
 *
 * @todo		
 *				- clean up debug code
 *				- configurable choice hostname (NAME_GLOBAL) or 'this site' (config, installer)
 *				- make index on the spammer column in the referrer_blacklist table _unique_ (installer) and remove extra query
 *				later:
 *				- (global) icons to represent each of the five views, small and larger versions (menu/page)
 *				- adapt FormOpen() to accept id; then fix form kluge here and in stylesheet
 *				- adapt text definitions to take singular-plural into account
 *				- add paging
 *				- turn list into form with checkboxes to allow mass removing
 *
 * @input		string  $q  optional: string used to filter the referrers;
 *				default: 'NULL;
 *				the default can be overridden by providing a POST parameter 'q'
 * @input		integer $qo optional: determines the kind of search to be performed for string $q:
 *				1: search for all referrers containing a given string
 *				0: search for all referrers not containing a given string
 *				default: 1;
 *				the default can be overridden by providing a POST parameter 'qo'
 * @input		string  $remove  optional: GET parameter - domain to be removed from the blacklist
 *				default: NULL;
 */

// constants

define('DEBUG',FALSE);		# @@@ set TRUE to generate debugging output

define('SEARCH_LIKE','LIKE');			# search string operator
define('SEARCH_UNLIKE','NOT LIKE');		# search string operator

// -------------------------------------

// initialize parameters

$q = NULL;								# search string
$qo = 1;								# search string option
$remove = NULL;							# domain to be removed from the blacklist

// -------------------------------------

// initialize internal variables

$string_option = SEARCH_LIKE;			# LIKE or NOT LIKE
$tag = $this->GetPageTag();
$isAdmin = $this->IsAdmin();
$loggedin = ($isAdmin) ? TRUE : (bool)$this->GetUser();
$pre = $this->config['table_prefix'];
$r = 1;									# row counter

$queryd = '';
$querys = '';
$rows = 0;

// -------------------------------------

// User-interface strings

define('TITLE','Blacklisted domains');

define('REPORT_REMOVED','Removed: %d records');							# @@@ does not take account of singular

define('TOTAL_BL','Total: %d blacklisted domain');

// current target
# you can use NAME_GLOBAL instead of 'this site' if the site name is short enough
# @@@ JW: choice between 'this site' and NAME_GLOBAL could be set via configuration (later)
define('TARGET_GLOBAL','this site');
define('TARGET_PAGE',$tag);

// menus don't use current target but *possible* targets
define('MENU_REFERRERS','Referrers to %s');
define('MENU_SITES','Domains linking to %s');
define('MENU_REFERRERS_PAGE',sprintf(MENU_REFERRERS,TARGET_PAGE));
define('MENU_SITES_PAGE',sprintf(MENU_SITES,TARGET_PAGE));
define('MENU_REFERRERS_GLOBAL',sprintf(MENU_REFERRERS,TARGET_GLOBAL));
define('MENU_SITES_GLOBAL',sprintf(MENU_SITES,TARGET_GLOBAL));
define('MENU_BLACKLIST','Blacklisted sites');

define('FORM_LEGEND','Filter view:');
define('FORM_URL_OPT_LABEL','Domain:');
define('FORM_URL_OPT_TITLE','Select search option');
define('FORM_URL_OPT_1','containing');
define('FORM_URL_OPT_0','not containing');
define('FORM_URL_STRING_LABEL','string');
define('FORM_URL_STRING_TITLE','Enter a search string');
define('FORM_SUBMIT_BLACKLIST','Show blacklisted domains');

define('LIST_SUMMARY_BL','Filtered list of blacklisted domains%s, sorted alphabetically');
define('LIST_HEAD_ACTION','Action');
define('LIST_HEAD_BL','Blacklisted domains');
define('LIST_ACTION_DESC',' and links to remove domains from the blacklist');
define('LIST_ACTION_BL','Remove');
define('LIST_ACTION_BL_TITLE','Remove this domain from the blacklist');

define('LOGIN_NOTE','You need to login to see blacklisted domains.');

define('LIST_RESULT_COUNTER_SITES','Filtered result: %d domain(s) matching these criteria');				# @@@ does not take account of singular
define('LIST_RESULT_NONE','Filtered result:');
define('NONE_NOTE','No blacklisted domains found');

// -------------------------------------

// fetch and validate parameters

// get query string and comparison method
if (isset($_POST['q']))
{
	$tq = trim(strip_tags($_POST['q']));
	if ('' != $tq)
	{
		$q = mysql_real_escape_string($tq);
		if (isset($_POST['qo']))
		{
			$qo = ($_POST['qo'] == '1') ? 1 : 0;
			$string_option = ($qo == 1) ? SEARCH_LIKE : SEARCH_UNLIKE;
		}
	}
}
// get host(s) to be removed
if (isset($_GET['remove']))
{
	$remove = mysql_real_escape_string(strip_tags($_GET['remove']));
}

// -------------------------------------

// build remove query

if ($isAdmin)
{
	$queryd = 'DELETE FROM '.$pre.'referrer_blacklist'
			. ' WHERE spammer = "'.$remove.'"';
}

// build filter query

if ($loggedin)
{
	$querys = 'SELECT * FROM '.$pre.'referrer_blacklist';
	if (isset($q))
	{
		$querys .= ' WHERE spammer '.$string_option." '%".$q."%'";	# filter by string
	}
	$querys .= ' ORDER BY spammer ASC';								# set order

	// get total number of domains in blacklist
	$query_refcount  = 'SELECT COUNT(spammer) AS total';
	$query_refcount .= ' FROM '.$pre.'referrer_blacklist';
}

// -------------------------------------

// execute query (if logged in)

// do a 'remove' query first, then follow with the select query:
// the list should then reflect the situation after removal of a domain
if ($loggedin)
{
	if ($isAdmin && isset($remove))
	{
		$rc = $this->Query($queryd);								# TRUE on success
		$numbldeleted = mysql_affected_rows();						# @@@ report back as GET parameter (in $removeurl/$removelink!)
	}
	$blacklist = $this->LoadAll($querys);
	$totalrefs = $this->LoadSingle($query_refcount);
}

// -------------------------------------

// build UI elements

// title
$title = TITLE;

if ($isAdmin)
{
	if (isset($numbldeleted)) $rptremoved = sprintf(REPORT_REMOVED,$numbldeleted);

	$removeurl = $this->Href('review_blacklist','','remove=').'%s';
	$removelink = '<a class="keys" href="'.$removeurl.'" title="'.LIST_ACTION_BL_TITLE.'">'.LIST_ACTION_BL.'</a>';
}

if ($loggedin)
{
	// results
	$tot = $totalrefs['total'];
	$total = sprintf(TOTAL_BL,$tot);
	$cdomains = count($blacklist);

	if ($cdomains > 0)
	{
		$result = sprintf(LIST_RESULT_COUNTER_SITES,$cdomains);
	}
	else
	{
		$result = LIST_RESULT_NONE;
	}

	// menu elements: prevent wrapping within element (these *don't* use current target!
	$menu_referrers_page	= str_replace(' ','&nbsp;',MENU_REFERRERS_PAGE);
	$menu_sites_page 		= str_replace(' ','&nbsp;',MENU_SITES_PAGE);
	$menu_referrers_global	= str_replace(' ','&nbsp;',MENU_REFERRERS_GLOBAL);
	$menu_sites_global		= str_replace(' ','&nbsp;',MENU_SITES_GLOBAL);
	$menu_blacklist			= str_replace(' ','&nbsp;',MENU_BLACKLIST);

	//menu links
	$m_referrers_page = '<a href="'.$this->Href('referrers').'">'.$menu_referrers_page.'</a>';
	$m_sites_page ='<a href="'.$this->Href('referrers','','sites=1').'">'.$menu_sites_page.'</a>';
	$m_referrers_global = '<a href="'.$this->Href('referrers','','global=1').'">'.$menu_referrers_global.'</a>';
	$m_sites_global = '<a href="'.$this->Href('referrers','','global=1&amp;sites=1').'">'.$menu_sites_global.'</a>';
	$m_blacklist = '<a href="'.$this->Href('review_blacklist').'">'.$menu_blacklist.'</a>';
	
	$menu = '<ul class="menu">'."\n";
	$menu .= '<li>'.$m_referrers_page.'</li>'."\n";
	$menu .= '<li>'.$m_sites_page.'</li>'."\n";
	$menu .= '<li>'.$m_referrers_global.'</li>'."\n";
	$menu .= '<li>'.$m_sites_global.'</li>'."\n";
	$menu .= '<li class="active">'.$m_blacklist.'</li>'."\n";
	$menu .= '</ul>'."\n";
	
	
	// form
	$form  = $this->FormOpen('review_blacklist','','POST');		# @@@ add parameter for id
	$form .= '<fieldset>'."\n";
	$form .= '<legend>'.FORM_LEGEND.'</legend>'."\n";

	$form .= '<label for="qo" class="mainlabel">'.FORM_URL_OPT_LABEL.'</label> '."\n";
	$form .= '<select name="qo" id="qo" title="'.FORM_URL_OPT_TITLE.'">'."\n";
	$form .= '<option value="1"'.(($qo == '1')? ' selected="selected"' : '').'>'.FORM_URL_OPT_1.'</option>'."\n";
	$form .= '<option value="0"'.(($qo == '0')? ' selected="selected"' : '').'>'.FORM_URL_OPT_0.'</option>'."\n";
	$form .= '</select> '."\n";
	$form .= '<label for="q">'.FORM_URL_STRING_LABEL.'</label> '."\n";
	$form .= '<input type ="text" name="q" id="q" title="'.FORM_URL_STRING_TITLE.'" size="10" maxlength="50" value="'.$q.'" />';

	$form .= '</fieldset>'."\n";

	$form .= '<input type="submit" value="'.FORM_SUBMIT_BLACKLIST.'" accesskey="b" />'."\n";
	$form .= $this->FormClose();

	// blacklist with admin link for removing
	$summary  = ($isAdmin) ? sprintf(LIST_SUMMARY_BL,LIST_ACTION_DESC) : sprintf(LIST_SUMMARY_BL,'');
	$refshead = LIST_HEAD_BL;

	// ids - use constant for variable-content heading
	$idTitle = $this->makeId('hn','title');
	$idTotal = $this->makeId('hn','total');
	$idResult = $this->makeId('hn','result');
}

// -------------------------------------

// show user interface (pre-template)

echo '<div class="page">'."\n";
echo '<h3 id="'.$idTitle.'">'.$title.'</h3>'."\n";
# debug
if (DEBUG)
{
	echo 'Query remove: '.$queryd.'<br />';
	echo 'Query blacklist: '.$querys.'<br />';
	echo 'remove: '.$remove.'<br/>';
	echo 'removed: '.$numbldeleted.'<br/>';
}
# debug
if ($loggedin)
{
	if ($isAdmin && isset($numbldeleted)) echo '<p class="notes">'.$rptremoved.'</p>';
	echo '<div class="refmenu">'.$menu.'</div>'."\n";
	echo '<div id="refbody">'."\n";
	echo '<h4 id="'.$idTotal.'">'.$total.'</h4>'."\n";
	echo '<div id="refform">'.$form.'</div>'."\n";	# @@@ kluge until FormOpen() is adapted: id should actually be on form itself and div not necessary!

	if ($cdomains != 0)
	{
		echo '<h4 id="'.$idResult.'">'.$result.'</h4>'."\n";
		echo '<table id="reflist" class="data" summary="'.$summary.'">'."\n";
		echo '<thead>';
		if ($isAdmin) echo '<th class="action c2" scope="col">'.LIST_HEAD_ACTION.'</th>';
		echo '<th class="refs" scope="col">'.$refshead.'</th></tr>'."\n";
		echo '</thead>'."\n";
		echo '<tbody>'."\n";
		foreach ($blacklist as $spammer)
		{
			$ref	= $this->htmlspecialchars_ent($spammer['spammer']);
			echo '<tr'.(($r%2)? '' : ' class="alt"').'>'."\n"; #enable alternate row color			echo '<td class="hits">'.$hits.'</td>';
			if ($isAdmin) echo '<td class="action c2">'.sprintf($removelink,$ref).'</td>';
			echo '<td class="refs">'.$ref.'</td>';
			echo '</tr>'."\n";
			$r++;
		}
		echo '</tbody>'."\n";
		echo '</table>'."\n";
	}
	else
	{
		echo '<h4 id="'.$idResult.'">'.$result.'</h4>'."\n";
		echo '<p><em class="error">'.NONE_NOTE.'</em></p>'."\n";
	}
}
else
{
	echo '<p><em class="error">'.LOGIN_NOTE.'</em></p>'."\n";
}
echo '</div>'."\n";
echo '</div>'."\n";
?>