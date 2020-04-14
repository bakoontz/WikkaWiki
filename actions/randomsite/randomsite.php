<?php
/** 
 * Select and display a random Wikka powered website, retrieved from a list stored in an admin page.
 *
 * When adding an item, please make sure that
 * - all the information is correctly spelled and semicolon-separated;
 * - short_desc is terminated by a dot;
 * - every line is terminated by a newline;
*/
//Set defaults
$output = '';
$image_folder = '/images/wikka_powered/';
$admin_page = 'AdminFeaturedSites';

//get list of websites from admin page
$load_page = $this->LoadPage($admin_page);

//create list of items from page
$list = explode("\n",trim($load_page['body'],'%'));
//get rid of empty items
foreach($list as $key => $value)
{
	if($value == '') { unset($list[$key]); } 
} 
if ($showall == 1)
{
	$output .= '<ul style="list-style: none">';
	foreach($list as $item)
	{
		list($title, $url, $image_name, $shortdesc) = explode(';', $item);
		$output .= '<li style="background-color: #EEE; border:1px solid #DDD; margin:10px; float: left; width: 380px; height: auto; padding:10px">';
		$output .= '<h2>'.$title.'</h2>'."\n";
		$output .= '<p><span class="small">'.$shortdesc.'</span></p>'."\n";
		$output .= '<a href="'.$url.'" title="'.$title.'" target="_blank"><img class="border center" src="'.$image_folder.$image_name.'" alt="'.$title.' screenshot"/></a>'."\n";
		$output .= '</li>';
	}	
	$output .= '</ul>';
	$output .= '<div class="clear"></div>';
}
else
{
	//get random item
	$random_item = array_rand($list);
	list($title, $url, $image_name, $shortdesc) = explode(';', $list[$random_item]);
	$output .= '<p>'.$this->Link($url, '', $title).'<br />'."\n";
	$output .= '<span class="small">'.$shortdesc.'</span></p>'."\n";
	$output .= '<a href="'.$url.'" title="'.$title.'" target="_blank"><img class="border" src="'.$image_folder.$image_name.'" alt="'.$title.' screenshot"/></a>'."\n";
}
echo $output;
?>