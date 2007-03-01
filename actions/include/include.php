<?php
/**
 * Include another wikipage.
 * 
 * Circular references are detected and prevented. This action sets a value $this->_included_page
 * which holds the name of the page being included. If such page, when formatted, needs the name
 * of the included page instead of the page that includes it, it should use $this->_included_page
 * instead of $this->GetPageTag(). (E.g. Trac:#232)
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::LoadPage()
 * @uses	Wakka::Format()
 */

if (!$page) $page = $wikka_vars;
$_included_page = $page;
$page = strtolower($page);
if (!$this->config["includes"]) $this->config["includes"][] = strtolower($this->tag);

if (!in_array($page, $this->config["includes"]) && $page != $this->tag) 
{
	if ($this->HasAccess("read", $page)) 
	{
		$this->config["includes"][] = $page;
		$this->_included_page = $_included_page;
		$page = $this->LoadPage($page);
		print $this->Format($page["body"]);
		unset($this->_included_page);
	}
} 
else print '<em class="error">'.ERROR_CIRCULAR_REFERENCE.'</em>';

?>
