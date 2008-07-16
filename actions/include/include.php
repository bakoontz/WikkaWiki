<?php
/**
 * Include another wikipage.
 *
 * Circular references are detected and prevented. This action sets a value $this->_included_page
 * which holds the name of the page being included. If such page, when formatted, needs the name
 * of the included page instead of the page that includes it, it should use $this->_included_page
 * instead of $this->GetPageTag(). (E.g. Trac:#232)
 *
 * Usage:
 *		{{include page="WikkaName"}}
 *		{{include WikkaName}}		(deprecated)
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::LoadPage()
 * @uses	Wakka::Format()
 *
 * DONE	misuse of the config array! included pages have nothing to do with
 *			configuration and should be stored in their own data structure
 * @todo	use new method to retrieve and sanitize action parameters
 * @todo	implement (improved version of) #60: included_pages array should
 *			use only "real" page names, not as passed to the action.
 * @todo	the "circular reference" check for included pages isn't completely
 *			valid: if pageA includes both pageB and pageC, where both pageB and
 *			pageC include pageShared, there is no circular reference - but
 *			pageShared would still be shown as part of included pageB only, and
 *			be skipped in pageC.This will break wiki's where pages are built
 *			from fragments. IOW: inclusion <b>level</b>, or an inclusion
 *			<b>chain</b> needs to be taken into account instead of just a list
 *			of "included pages"! Maybe a push-pop mechanism...
 * @todo	check if the object variable $this->_included_page even works when
 *			we have recursion via multi-level includes: maybe we need a static
 *			function variable instead to handle the recursion!?
 */

// get parameters
if (!$page) $page = $wikka_vars;	// @@@ sanitize (update Action() first!) @@@ and get real name from DB #60
$page = $this->htmlspecialchars_ent($page);

// init variables
$_included_page = $page;
$page = strtolower($page);					// @@@ #60
if (count($this->included_pages) == 0)		// check for empty array
{
	$this->included_pages[] = strtolower($this->tag);	// @@@ #60 initialize with (including) current page to avoid it including itself
}

if (!in_array($page, $this->included_pages))
{
	if ($this->HasAccess('read', $page))
	{
		$this->included_pages[] = $page;
		// signal to page it is being included and what its actual name is #232
		$this->_included_page = $_included_page;
		$page = $this->LoadPage($page);
		print $this->Format($page['body']);
		unset($this->_included_page);
	}
	else printf("<em class='error'>".ERROR_TARGET_ACL."</em>", $_included_page);
}
else
{
	print '<em class="error">'.ERROR_CIRCULAR_REFERENCE.'</em>';
}
?>
