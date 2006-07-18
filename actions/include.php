<?php
/**
 * Include another wikipage.
 * 
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
$page = strtolower($page);
if (!$this->config["includes"]) $this->config["includes"][] = strtolower($this->tag);

if (!in_array($page, $this->config["includes"]) && $page != $this->tag) {
	if ($this->HasAccess("read", $page)) {
      	$this->config["includes"][] = $page;
        	$page = $this->LoadPage($page);
		print $this->Format($page["body"]);
	}
} else print "<span class='error'>Circular reference detected</span>"; #i18n

?>