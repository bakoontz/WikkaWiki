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
 */

// defaults
if(!defined('ERROR_CIRCULAR_REF')) define('ERROR_CIRCULAR_REF', 'Circular reference detected');
if(!defined('ERROR_TARGET_ACL')) define('ERROR_TARGET_ACL', "You aren't allowed to read included page <tt>%s</tt>");

if (!$page) $page = $wikka_vars;
$orig_page = $page;
$page = strtolower($page);
if (!$this->config["includes"]) $this->config["includes"][] = strtolower($this->tag);

if (!in_array($page, $this->config["includes"]) && $page != $this->tag) {
	if ($this->HasAccess("read", $page)) {
      	$this->config["includes"][] = $page;
        	$page = $this->LoadPage($page);
		print $this->Format($page["body"]);
	}
	else printf("<em class='error'>".ERROR_TARGET_ACL."</em>", $orig_page);
} else print "<em class='error'>".ERROR_CIRCULAR_REF."</em>";

?>
