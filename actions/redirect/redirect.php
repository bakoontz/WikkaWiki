<?php
/**
 * Redirect the user to another, existing, wiki page.
 * 
 * ACL for the page have precedence, therefore the user will not be redirected 
 * if he is not allowed to see the page. The redirect only occurs if the method is 'show'.
 * Append 'redirect=no' as a param to the page URL to be not redirected.
 * 
 * To indicate a temporary redirect, use 'temporary=yes' as an action param. 
 * The default type of redirect is 'Moved permanently'.
 * 
 * @usage		{{redirect target="SandBox"}}
 * @usage		{{redirect to="CategoryCategory"}}
 * @usage		{{redirect page="HomePage" [temporary="yes"] }}
 * @package		Actions
 * @version		$Id$
 *
 * @author		NilsLindenberg
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @input		string $page mandatory: target wiki page [may substitute "target" or "to" for "page"]
 * @input		bool $temporary	optional: indiacte a temporary redirect
 * 
 * @uses		Wakka::cleanUrl()
 * @uses		Wakka::existsPage
 * @uses		Wakka::GetSafeVar()
 * @uses		Wakka::Link()
 * @uses		Wakka::Redirect()
 * 
 * @todo		test
 * @todo		move i18n constants to language file
 */

// defaults
$headercode = "HTTP/1.0 301 Moved Permanently";
$redirect = TRUE;
$page = '';
$target = '';

// only redirect if we show the page
if('show' != $this->GetHandler())
{
	$redirect = FALSE;
}

// do not redirect when 'redirect=no' is appended to the pages URL.
$stop_redirect = $this->GetSafeVar('redirect');
if(null != $stop_redirect)
{
	$redirect = FALSE;
} 
 

// getting params
if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {
    	if ($param == 'target' || $param == 'to' || $param == 'page') 
    	{
			if ($this->existsPage($this->htmlspecialchars_ent($value))) $target = $value;
    	}
    	if ($param == 'temporary')
    	{
    		$headercode = "HTTP/1.0 302 Moved Temporarily";
    	}	
    }
}

$full_target = $this->Href('',$target, 'redirect=no');
$full_target = str_replace('&amp;', '&', $full_target); # workaround for Href masking & in urls

//the actual redirect  	
if($redirect && '' != $full_target)
{
	header($headercode);
	#$message = sprintf(T_("Redirected from %s."), $this->Link($this->GetPageTag()));
	$message = sprintf(T_("Redirected from %s."), $this->GetPageTag());
	$this->Redirect($full_target, $message);
}

// only display a link 
else 
{
	if ('' != $target)	
	{
		printf(T_("This page has been moved to %s."), $this->Link($target)); 	
	}
	else
	{
		echo '<em class="error">'.T_("Invalid redirect. Target must be an existing wiki page.").'</em>';
	}
}
?>