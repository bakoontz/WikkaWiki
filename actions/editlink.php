<?php
/**
 * Edit link menulet
 */
echo $this->HasAccess('write') ? '<a href="'.$this->Href('edit').'" title="Click to edit this page">Edit</a>' : '<a href="'.$this->Href('showcode').'" title="Display the markup for this page">Source</a>';
?>
