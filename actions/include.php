<?php
/**
 * Include contents of a target page at the point of invocation 
 *
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::LoadPage()
 * @uses		Wakka::Format()
 * 
 * @todo	Should not tell the user if he has no right to read the page
 */

// defaults
if(!defined('ERROR_CIRCULAR_REF')) define('ERROR_CIRCULAR_REF', 'Circular reference detected');
if(!defined('ERROR_TARGET_ACL')) define('ERROR_TARGET_ACL', "You aren't allowed to read included page <tt>%s</tt>");

$page ='';

// getting params
if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {
    	if ($param == 'page') 
    	{
    		$page = $value;
    	}
    }
}

// compatibilty for {{include SandBox}}
if (('' == $page) && isset($wikka_vars)) $page = $wikka_vars;

// check for circular reference and include the page
if('' != $page)
{
	$orig_page = $page;
	$page = strtolower($page);
	if (!isset($this->config["includes"])) $this->config["includes"][] = strtolower($this->tag);
	
	if (!in_array($page, $this->config["includes"]) && $page != $this->tag) 
	{
		if ($this->HasAccess("read", $page)) 
		{
	      	$this->config["includes"][] = $page;
	        	$page = $this->LoadPage($page);
			print $this->Format($page["body"]);
		}
		else printf("<em class='error'>".ERROR_TARGET_ACL."</em>", $orig_page);
	} 
	else print "<em class='error'>".ERROR_CIRCULAR_REF."</em>";
}
?>