<?php
/**
 * Shows the contributors of this page, most active user first.
 *
 * syntax:      {{contributors}}
 *
 * @package     Actions
 * @subpackage  Menulets
 * @name        Contributors
 * @author      {@link http://wikkawiki.org/OnegWR OnegWR}
 * @copyright   Copyright © 2006, OnegWR
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since       Wikka 1.1.6.0
 */

$q = 'SELECT Count(*) AS cnt, `user` FROM '.$this->config["table_prefix"].'pages '.
		'WHERE `tag`="'.$this->tag.'" GROUP BY user ORDER BY cnt DESC;';
$all = $this->LoadAll( $q );

foreach($all as $key=>$val)
{
		print $this->Link($val['user'],'',$val['user'], FALSE, TRUE, '('.$val['cnt'].')') ." \n";
}
?>
