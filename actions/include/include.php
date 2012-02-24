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
	if (!isset($this->config['includes'])) $this->config['includes'][] = strtolower($this->GetPageTag());
	
	if (!in_array($page, $this->GetConfigValue('includes')) && $page != $this->GetPageTag()) 
	{
		if ($this->HasAccess('read', $page)) 
		{
	      	$this->config['includes'][] = $page;
	        	$page = $this->LoadPage($page);
			print $this->Format($page['body']);
		}
		else printf('<p class="error">'.T_("You aren't allowed to read included page <tt>%s</tt>")."</p>\n", $orig_page);
	} 
	else print '<p class="error">'.T_("Circular reference detected!")."</p>\n";
}
?>
