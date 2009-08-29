<?php
/**
 * Edit link menulet
 */
//i18n
if (!defined('EDITLINK_TEXT')) define('EDITLINK_TEXT', '[Edit]');
if (!defined('SHOWLINK_TEXT')) define('SHOWLINK_TEXT', '[Show]');
if (!defined('SHOWCODELINK_TEXT')) define('SHOWCODELINK_TEXT', '[Source]');
if (!defined('EDITLINK_TITLE')) define('EDITLINK_TITLE', 'Click to edit this page');
if (!defined('SHOWLINK_TITLE')) define('SHOWLINK_TITLE', 'Displayed the formatted version of this page');
if (!defined('SHOWCODELINK_TITLE')) define('SHOWCODELINK_TITLE', 'Display the markup for this page');

if ($this->method == 'edit' || $this->method == 'showcode')
{
	echo '<a href="'.$this->Href().'" title="'.SHOWLINK_TITLE.'">'.SHOWLINK_TEXT.'</a>';
}
else
{
	if ($this->HasAccess('write'))
	{
		echo '<a href="'.$this->Href('edit').'" title="'.EDITLINK_TITLE.'">'.EDITLINK_TEXT.'</a>';
	}
	else
	{
		echo '<a href="'.$this->Href('showcode').'" title="'.SHOWCODELINK_TITLE.'">'.SHOWCODELINK_TEXT.'</a>';
	}
}
?>
