<?php
/**
 * Prints a link to a random page.
 *
 * syntax:
 *      {{randompage [title="string"] [pos="PageName, PageName2"] [neg="PageName3, PageName4"]}}
 *
 * @package     Actions
 * @subpackage  Menulets
 * @name        RandomPage
 *
 * @author      {@link http://wikkawiki.org/OnegWR OnegWR}
 * @copyright   Copyright ? 2006, OnegWR
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since       Wikka 1.1.6.0
 *
 * @input       string  $title  optional: alternative for "RandomPage"
 *                              if set to "" the real pagename is used
 * @input       string  $pos    optional: comma separated list of pages
 *                              only pages out of this list will be chosen
 *                              default: all pages
 * @input       string  $neg    optional: comma separated list of pages
 *                              pages in this list will not be used
 *                              exeption: if the errorpage is in the list
 *                              default: defined by $neg_list_default array
 * If no match could be found, HomePage/config["root_page"] is returned.
 * @ToDo        Get list of pages from the tagCache of the ExistsPage function by IanAndolina
 */

$errorpage = $this->GetConfigValue('root_page');
$neg_list_default = array("HomePage","UserSettings","TextSearch","TextSearchExpanded","PageIndex");
$title = isset($vars['title']) ? $this->htmlspecialchars_ent($vars['title']) : "RandomPage"; //i18n

foreach( $this->LoadAll("select distinct tag from ".$this->GetConfigValue('table_prefix')."pages") as $key => $val ){
        $all[]=$val['tag'];
}
$pos_list = isset($vars['pos']) ? preg_split("/[|,]/", preg_replace( "/[\ ]/", '', $vars['pos'] ) ) : $all ;
$neg_list = isset($vars['neg']) ? preg_split("/[|,]/", preg_replace( "/[\ ]/", '', $vars['neg'] ) ) : $neg_list_default ;

$try = 0;
while ( $try < 5 ) {
        $try++;
        $page = $pos_list[array_rand($pos_list)];
        if( !in_array($page, $all) ) continue;
        if( in_array($page, $neg_list) ) continue;
        break;
}
if( $try > 4 ) $page = $errorpage;
if( $title=='' ) $title = $page;
print $this->Link( $page, '', $title, FALSE, TRUE, "$page, a random page on this site" ); //i18n
?>
