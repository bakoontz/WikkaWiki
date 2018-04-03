<?php
/**
 * Display the markdown formated wiki page.
 *
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id: md.php 2016-12-04 00:00:00Z oemunoz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Wakka::HasAccess()
 */
 $loader = require 'vendor/autoload.php';
 $loader->add('AppName', __DIR__.'/../src/');

if ($this->HasAccess('read') && $this->page)
{
	// display markdown page.

  // TODO this options must be prefered on configuration file.
	// traditional markdown and parse full text
  //$parser = new \cebe\markdown\Markdown();
	// use github markdown
  //$parser = new \cebe\markdown\GithubMarkdown();
	// use markdown extra
  $parser = new \cebe\markdown\MarkdownExtra();

	// TODO ask to use and put this on the header.php. (head html) Maybe this dont work on very old browers becose of this.
	echo '<link rel="stylesheet" type="text/css" href="'.$this->GetThemePath('/').'/css/prism.css" />';
	echo '<script src="'.$this->GetThemePath('/').'/js/prism.js"></script>';

  // TODO This make more "like" the wiki, this needs more (check for other missing options and sets).
	echo "\n".'<!--starting page content-->'."\n";
	echo '<div id="content"';
	echo (($user = $this->GetUser()) && ($user['doubleclickedit'] == 'N') || !$this->HasAccess('write')) ? '' : ' ondblclick="document.location=\''.$this->Href('edit', '', 'id='.$this->page['id']).'\';" '; #268
	echo '>'."\n";

	// TODO Needs a TOC generetor.

	echo $parser->parse($this->page['body']);

  echo '</div>';
}
?>
