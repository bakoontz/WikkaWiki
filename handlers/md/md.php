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
  // Make robust the way to find the css and the js. The handler can be on many directorys.
  $relative_path = explode( ',', $this->GetConfigValue('handler_path'));

  // If nothing works try with this very basic prism.
  $prims_css_default = "https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/themes/prism.min.css";
  $prims_js_default = "https://cdnjs.cloudflare.com/ajax/libs/prism/1.6.0/prism.min.js";

  // Check for the css and the js files. If exist overwrite the black/withe web.
  foreach ($relative_path as $key => $value) {
    if (is_file($relative_path[$key].'/md/prism.css') && is_file($relative_path[$key].'/md/prism.js'))
    {
      $prims_css_default = $relative_path[$key].'/md/prism.css';
      $prims_js_default = $relative_path[$key].'/md/prism.js';
    }
  }

	// display markdown page.

  // TODO this options must be prefered on configuration file of the wikka or even database.
	// traditional markdown and parse full text
  //$parser = new \cebe\markdown\Markdown();
	// use github markdown
  $parser = new \cebe\markdown\GithubMarkdown();
	// use markdown extra
  //$parser = new \cebe\markdown\MarkdownExtra();

	// TODO ask to use and put this on the header.php. (head html) Maybe this dont work on very old browers becose of this.
	echo '<link rel="stylesheet" type="text/css" href="'.$prims_css_default.'" />';
	echo '<script src="'.$prims_js_default.'"></script>';

  // TODO This make "like" the wiki, this needs more (check for other missing css options and sets).
	echo "\n".'<!--starting page content-->'."\n";
	echo '<div id="content"';
	echo (($user = $this->GetUser()) && ($user['doubleclickedit'] == 'N') || !$this->HasAccess('write')) ? '' : ' ondblclick="document.location=\''.$this->Href('edit', '', 'id='.$this->page['id']).'\';" '; #268
	echo '>'."\n";

	// TODO Needs a TOC generetor.

	echo $parser->parse($this->page['body']);

  echo '</div>';
}
?>
