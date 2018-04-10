<?php
#
# Checks hyperlinks appearing on wiki pages
# (intra-wiki, intra-wiki, actions and external links to web pages or shared files)
#
# @package      Actions
# @name         checklinks
#
# @authors      DomBonj
# @authors      Credit to steven@haryan.to, carinridge@hotmail.com (part of external link check code)
#
# @version      0.94
#
# @input        Parameters =  [scope=('page'|'user'|'all')] [sort=('tag'|'type'|'cnt'|)] [i][t][w]
#               default values: scope='user', sort='tag'
#               options: i=check internal links only ; t=terse report ; w=check that page tags are valid WikiWords
#
# @uses         Wakka::Format()
# @uses         Wakka::LoadAll()
# @uses         Wakka::LoadPage()
# @uses         Wakka::GetUserName()
# @uses         Wakka::GetPageTag()
# @uses         Wakka::Href()
# @uses         Wakka::SetPage()
# @uses         Wakka::GetInterWikiUrl()
#

// i18n strings
if (!defined('CL_SERVER_OK')) define('CL_SERVER_OK', 'Server OK');
if (!defined('CL_HOST_NOT_FOUND')) define('CL_HOST_NOT_FOUND', 'Server not found');
if (!defined('CL_HOST_UNREACH')) define('CL_HOST_UNREACH', 'Server unreachable');
if (!defined('CL_HOST_TIMEOUT')) define('CL_HOST_TIMEOUT', 'Server timeout');
if (!defined('CL_HOST_REJECT')) define('CL_HOST_REJECT', 'Server reject');
if (!defined('CL_FILE_NOHTTP')) define('CL_FILE_NOHTTP', 'No HTTP reply');
if (!defined('CL_NOSUCH_FILE')) define('CL_NOSUCH_FILE', 'File not found');
if (!defined('CL_MISSING_PAGE')) define('CL_MISSING_PAGE', 'Page non-existent');
if (!defined('CL_MISSING_INTERIWIKI')) define('CL_MISSING_INTERIWIKI', 'Wiki not defined');
if (!defined('CL_NON_WIKINAME')) define('CL_NON_WIKINAME', 'Page tag not a WikiName');
if (!defined('CL_COLNAME_SUMMARY')) define('CL_COLNAME_SUMMARY', 'Link type,Valid links #,Broken links #,Total');
if (!defined('CL_COLNAME_DETAILED')) define('CL_COLNAME_DETAILED', 'Link type,Page name,Link value,Error message,Occurences');
if (!defined('CL_NAME_AC')) define('CL_NAME_AC', 'Action');
if (!defined('CL_NAME_EX')) define('CL_NAME_EX', 'External');
if (!defined('CL_NAME_FI')) define('CL_NAME_FI', 'File');
if (!defined('CL_NAME_IW')) define('CL_NAME_IW', 'Inter-wiki');
if (!defined('CL_NAME_WN')) define('CL_NAME_WN', 'Wiki page');
if (!defined('CL_SUMMARY')) define('CL_SUMMARY', 'Link check summary');
if (!defined('CL_DETAILED')) define('CL_DETAILED', 'Detailed report');
// internal constants
if (!defined('CL_MAX_TRY')) define('CL_MAX_TRY', 3); // page fetch attempts
if (!defined('CL_MAX_REDIRECTS')) define('CL_MAX_REDIRECTS', 3); // allowed redirections
if (!defined('CL_CX_TIMEOUT ')) define('CL_CX_TIMEOUT', 8); // connection time allowance
if (!defined('CL_CACHE_LIFETIME')) define('CL_CACHE_LIFETIME', 120); // caching for 2 minutes
if (!defined('CL_MAX_LINK_LENGTH')) define('CL_MAX_LINK_LENGTH', 60); // max displayed length for an hyperlink

if (!function_exists('CLerror'))
{
    function CLerror ($msg)
    {
        return ('<em class="error">'. $msg .'</em><br />');
    }

    function CLsummary ($goodlinks, $badlinks)
    {
        $linknames = array('ac'=>CL_NAME_AC, 'ex'=>CL_NAME_EX, 'iw'=>CL_NAME_IW, 'wn'=>CL_NAME_WN, 'fi'=>CL_NAME_FI);
        $table_css = "class='data' cellpadding='2' cellspacing='1' border='2'";
        $colheads = preg_split('/,/', CL_COLNAME_SUMMARY);
        $sumgood = 0; $sumbad = 0;
        $output = sprintf ("<table $table_css><tr class='comment'><th>%s</th><th>%s</th><th>%s</th></tr>", $colheads[0], $colheads[1], $colheads[2]);
        foreach ($goodlinks as $linktype => $cnt)
        {
            $output .= "<tr><td>{$linknames[$linktype]}</td><td align='right'>{$cnt}</td><td align='right'>".(empty($badlinks[$linktype])?0:$badlinks[$linktype])."</td></tr>";
            $sumgood += $cnt;
            $sumbad += (empty($badlinks[$linktype])? 0 : $badlinks[$linktype]);
        }
        $output .= "<tr class='comment'><td>{$colheads[3]}</td><td align='right'>$sumgood</td><td align='right'>$sumbad</td></tr></table>";
        return $output;
    }

    function CLreport ($thisone, $badlinks, $keyorder)
    {
        $linknames = array('ac'=>CL_NAME_AC, 'ex'=>CL_NAME_EX, 'iw'=>CL_NAME_IW, 'wn'=>CL_NAME_WN, 'fi'=>CL_NAME_FI);
        $table_css = "class='wikka' cellpadding='2' cellspacing='1' border='2'";
        $colheads = preg_split('/,/', CL_COLNAME_DETAILED);
        $output = sprintf ("<table $table_css><tr class='comment'><th>%s</th><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr>", $colheads[0], $colheads[1], $colheads[2], $colheads[3], $colheads[4]);
        foreach ($keyorder as $tag => $val)
        {
            $cnt = $badlinks[$tag];
            preg_match('/^(.+) (\w\w)\/(.+?)\*(.+)$/', $tag, $matches);
            $pagelink = "<a href='". $thisone->Href('', $matches[1]) ."'>{$matches[1]}</a>";
            $link = ($matches[2] == 'ex') ? ("<a href='{$matches[3]}'>".substr($matches[3], 0, CL_MAX_LINK_LENGTH).((strlen($matches[3])>CL_MAX_LINK_LENGTH)?'...':'').'</a>')
                : (($matches[2] == 'fi') ? ("<a href='file://{$matches[3]}'>".substr($matches[3], 0, CL_MAX_LINK_LENGTH).((strlen($matches[3])>CL_MAX_LINK_LENGTH)?'...':'').'</a>') : $matches[3]);
            $output .= "<tr><td>{$linknames[$matches[2]]}</td><td>$pagelink</td><td>$link</td><td>{$matches[4]}</td><td align='right'>$cnt</td></tr>";
        }
        $output .= '</table>';
        return $output;
    }

    function CLcheck_page($fp, $page, $hostname, $firstcall)
    {
        $filestatus = CL_FILE_NOHTTP;
        $tmp = fputs ($fp, sprintf( "HEAD %s HTTP/1.0\r\nHost: %s\r\nUser-Agent: WikkaCheckLinks/1.0\r\n\r\n", $page, $hostname));
        for ($try = 1; ($try <= CL_MAX_TRY) && ($filestatus == CL_FILE_NOHTTP); $try++)
        {
            if (($http_reply = fgets($fp, 256)) == NULL)
            {
                break;
            }
            if (preg_match('/^HTTP\/(\d)\.(\d)\s+(\d+)\s+(.*)$/', $http_reply, $matches ))
            {
                $filestatus = ($matches[4]) ? trim($matches[4]) : trim($matches[3]);
                if ($firstcall && ($matches[3] == '100'))
                { // in HTTP/1.1, '100' means Continue
                    $filestatus = CLcheck_page($fp, $page, $hostname, false);
                }
                else if (substr($matches[3], 0, 1) == '3')
                { // redirection: let's find the new location
                    while (!feof($fp))
                    {
                        $reply .= fgets($fp, 256);
                    }
                    if (preg_match('/^Location:\s+(\S+)\s*$/m', $reply, $matches1))
                    {
                        $filestatus = 'MOV '. $matches1[1];
                    }
                }
            }
        }
        return $filestatus;
    }

    function CLcheck_link($url, $p='')
    {
        static $statuses = array();
        static $hostnames = array();
        $now = time();
        $purl = parse_url($url);
        $proto = isset($purl['scheme']) ? $purl['scheme'] : 'http';
        $port = isset($purl['port']) ? $purl['port'] : '';
        $path = isset($purl['path']) ? $purl['path'] : '/';
        $suffix = isset($purl['query']) ? $purl['query'] : '';
        if ((empty($purl['host'])) || ($proto!='http')&&($proto!='https')&&($proto!='ftp'))
        {
            $serverstatus = CL_HOST_NOT_FOUND;
        }
        else
        {
            $hostname = strtolower($purl['host']);
            if (preg_match('/^d+.d+.d+.d+/', $hostname))
            { // the host is an IP address
                $ip = $hostname;
            }
            else
            { // the host is a domain name, so we have to resolve it first
                $from_cache = false;
                if (isset($hostnames[$hostname]))
                { // have we tried to resolve it not so long ago?
                    if (($now - $hostnames[$hostname][1]) <= CL_CACHE_LIFETIME)
                    {
                        $ip = $hostnames[$hostname][0];
                        $from_cache = true;
                    }
                }
                if (!$from_cache)
                {
                    $ip = gethostbyname($hostname);
                    // if hostname not resolvable, gethostbyname returns its argument unchanged
                    if ($ip === $hostname)
                    {
                        $ip = '';
                    }   
                    // cache this resolve
                    else
                    {
                        $hostnames[$hostname] = array($ip, $now);
                    }
                }
            }
            if (!$ip)
            { // was the hostname unresolvable?
                $serverstatus = CL_HOST_NOT_FOUND;
            }
            else
            {
                if (!$port)
                {
                    if ($proto == 'http')
                    {
                        $port = 80;
                    }
                    elseif ($proto == 'https')
                    {
                        $port = 443;
                    }
                    elseif ($proto == 'ftp')
                    {
                        $port = 21;
                   
                    }
                }
                $key = "$ip:$port";
                // have we checked the server not so long ago?
                $from_cache = false;
                if (isset($statuses[$key]))
                {
                    if (($now - $statuses[$key][2]) <= CL_CACHE_LIFETIME)
                    {
                        $serverstatus = $statuses[$key][0];
                        if ($serverstatus == CL_SERVER_OK)
                        {
                            $from_cache = true;
                        }
                    }
                }
                if (!$from_cache || ($from_cache && ($serverstatus == CL_SERVER_OK)))
                { // we have to check the server, or the host is ok so check the file
                    $errno = 0;
                    $errstr = '';
                    if ($fp = fsockopen($ip, $port, $errno, $errstr, CL_CX_TIMEOUT))
                    {
                        $serverstatus = CL_SERVER_OK;
                        $filestatus = CL_FILE_NOHTTP;
                        $page = ($suffix) ? $path .'?'. $suffix : $path;
                        $filestatus = CLcheck_page($fp, $page, $hostname, false);
                    }
                    else
                    { // could not connect to server
                        if (preg_match('/timed?[- ]?out/i', $errstr))
                        {
                            $serverstatus = CL_HOST_TIMEOUT;
                        }
                        elseif (preg_match('/refused/i', $errstr))
                        {
                            $serverstatus = CL_HOST_REJECT;
                        }
                        else
                        {
                            $serverstatus = CL_HOST_UNREACH;
                        }
                    }
                    // cache this (server, file) pair
                    $statuses[$key] = array($serverstatus, $filestatus, $now);
                }
            }
        }
        if ($filestatus == '200')
        {
            $filestatus = 'OK';
        }
        else if ($filestatus == '302')
        {
            $filestatus = 'OK';
        }
        $output = ($serverstatus != CL_SERVER_OK) ? $serverstatus : $filestatus;

        return ($output);
    }
}

$output = '';
if ( (isset($vars['opts']) && (!preg_match("/^[tiw]{1,3}$/i", $vars['opts'])))
    || (isset($vars['scope']) && (!preg_match("/^(page|user|all)$/i", $vars['scope'])))
    || (isset($vars['sort']) && (!preg_match("/^(tag|type|cnt)$/i", $vars['sort']))) )
{
    $output .= CLerror("Usage: checklinks [scope=\"user|page|all\"] [sort=\"tag|type|cnt\"] [opts=\"[i][t][w]\"]");
}
else
{
    $save_page = $this->page;
    $save_tag = $this->tag;
    $goodcnts = array('ac'=>0, 'ex'=>0, 'iw'=>0, 'wn'=>0, 'fi'=>0);
    $show_external = (!isset($vars['opts']) || !preg_match('/i/', $vars['opts']));
    $show_badwnames = (isset($vars['opts']) && preg_match('/w/', $vars['opts']));
    $base_url = preg_quote($this->config['base_url'], '/');
    $badlinks = array();

    // first pass to load all page tags
    $query = "SELECT tag, time FROM ".$this->config['table_prefix']."pages WHERE ((latest = 'Y'))";
    $rows = $this->LoadAll($query);
    foreach ($rows as $row)
    {
        $exist[$row['tag']] = $row['time'];
    }
    // start checking requested pages
    $query = "SELECT * FROM ".$this->config['table_prefix']."pages WHERE ((latest = 'Y') AND (user <> 'WikkaInstaller')";
    if ('all' == strtolower($vars['scope']))
    {
        $query .= '';
    }
    else if ('page' == strtolower($vars['scope']))
    {
        $query .= " AND (tag = '". $this->GetPageTag() ."')";
    }
    else
    { // default value
        $query .= " AND (owner = '". $this->GetUserName() ."')";
    }
    $query .= ')';
    $rows = $this->LoadAll($query);
    foreach ($rows as $row)
    {
        if ($this->HasAccess('read', $row['tag']))
        { // this page is to be scanned: pretend your are this page
            $this->SetPage($this->LoadPage($row['tag']));
            $tmppage = $this->page['body'];
            // get rid of raw HTML and code blocks
            $tmppage = preg_replace("/\"\"(.*?)\"\"/s", '', $tmppage);
            $tmppage = preg_replace("/\%\%(.*?)\%\%/s", '', $tmppage);

            // 1. is page tag formatted as a valid WikiName ?
            if (!preg_match("/^([A-ZÄÖÜ]+[a-zßäöü]+[A-Z0-9ÄÖÜ][A-Za-z0-9ÄÖÜßäöü]*)$/", $row['tag'], $matches) && $show_badwnames)
            {
                $badlinks[$row['tag']." wn/&nbsp;*".CL_NON_WIKINAME] = (empty($badlinks[$row['tag']." wn/&nbsp;*".CL_NON_WIKINAME])) ? 1 : $badlinks[$row['tag']." wn/&nbsp;*".CL_NON_WIKINAME]+1;
                $badcnts['wn'] += 1;
            }
            // 2. check actions
            preg_match_all("/\{\{(.*?)\}\}/", $tmppage, $matches);
            foreach ($matches[1] as $actionname)
            {
                if (preg_match("/^([A-Za-z0-9]+)/", trim($actionname), $matches1))
                {
                    if (!file_exists($this->config['action_path']."/".$matches1[1].".php"))
                    {
                        $badlinks[$row['tag']." ac/{$matches1[1]}*". CL_NOSUCH_FILE] = (empty($badlinks[$row['tag']." ac/{$matches1[1]}*". CL_NOSUCH_FILE])) ? 1 : $badlinks[$row['tag']." ac/{$matches1[1]}*". CL_NOSUCH_FILE]+1;
                        $badcnts['ac'] += 1;
                    }
                    else
                    {
                        $goodcnts['ac'] +=1;
                    }
                }
            }
            // now get rid of actions to avoid confusion
            $tmppage = preg_replace("/\{\{(.*?)\}\}/", '', $tmppage);
            // 3. check interwiki links
            preg_match_all("/([A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+)[:](\S*)\b/", $tmppage, $matches);
            foreach ($matches[1] as $interwikiname)
            {
                if (!$this->GetInterWikiUrl(trim($interwikiname), ''))
                {
                    $badlinks[$row['tag']." iw/{$interwikiname}*". CL_MISSING_INTERIWIKI] = (empty($badlinks[$row['tag']." iw/{$interwikiname}*". CL_MISSING_INTERIWIKI])) ? 1 : $badlinks[$row['tag']." iw/{$interwikiname}*". CL_MISSING_INTERIWIKI]+1;
                    $badcnts['iw'] += 1;
                }
                else
                {
                    $goodcnts['iw'] +=1;
                }
            }
            // now get rid of interwiki links to avoid confusion
            $tmppage = preg_replace("/([A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+[:]\S*)\b/", '', $tmppage);

            // now check hyperlinks; first, prevent recursive calling
            $page = preg_replace('/\{\{\s*checklinks\b.*?\}\}/i', '', $this->page['body']);
            // do not count twice non-existent links
            $page = preg_replace('/\{\{\s*wantedpages\s*\}\}/i', '', $page);
            // render the page
            $html = $this->Format($page, 'wakka');
            if (preg_match_all("/href\=[\"|\']((http|https|ftp):\/\/[^\\s\"\'<>]+)/", $html, $matches))
            {
                foreach ($matches[1] as $url)
                { // 4. check intra-wiki links
                    if (preg_match('/'.$base_url.'([A-Za-zÄÖÜßäöü][A-Za-z0-9ÄÖÜßäöü]*)/', $url, $matches1))
                    {
                        $wikiname = $matches1[1];
                        if (!$exist[trim($wikiname)])
                        {
                            $badlinks[$row['tag']." wn/{$wikiname}*". CL_MISSING_PAGE] = (empty($badlinks[$row['tag']." wn/{$wikiname}*". CL_MISSING_PAGE])) ? 1 : $badlinks[$row['tag']." wn/{$wikiname}*". CL_MISSING_PAGE]+1;
                            $badcnts['wn'] += 1;
                        }
                        else
                        {
                            $goodcnts['wn'] += 1;
                        }
                    }
                    else if ($show_external)
                    { // 5. check external hyperlinks
                        $OK = (strtoupper($tmp = CLcheck_link($url)) == 'OK');
                        // allow at most 3 successive redirections
                        for ($i=1; !$OK && preg_match("/^MOV (.+)$/", $tmp, $matches1) && ($i<=CL_MAX_REDIRECTS); $i++)
                        {
                            $OK = (strtoupper($tmp = CLcheck_link(trim($matches1[1]))) == "OK");
                        }
                        if (!$OK)
                        {
                            $badlinks[$row['tag']." ex/{$url}*$tmp"] = (empty($badlinks[$row['tag']." ex/{$url}*$tmp"])) ? 1 : $badlinks[$row['tag']." ex/{$url}*$tmp"]+1;
                            $badcnts['ex'] += 1;
                        }
                        else
                        {
                            $goodcnts['ex'] += 1;
                        }
                    }
                } // foreach $matches
            } // if preg_match_all http
            // 5. check href-ed files
            if (preg_match_all("/href\=[\"|\']file:\/{2,}([^\\\"\'<>]+)[\'\"]/", $html, $matches))
            {
                foreach ($matches[1] as $rawfname)
                {
                    if (!file_exists(rawurldecode($rawfname)))
                    {
                        $fname = str_replace(' ', '&nbsp;', $rawfname);
                        $badlinks[$row['tag']." fi/$fname*".CL_NOSUCH_FILE] = (empty($badlinks[$row['tag']." fi/$fname*".CL_NOSUCH_FILE])) ? 1 : $badlinks[$row['tag']." fi/$fname*".CL_NOSUCH_FILE]+1;
                        $badcnts['fi'] += 1;
                    }
                    else
                    {
                        $goodcnts['fi'] += 1;
                    }
                } // foreach $matches
            } // if preg_match_all file
        } // if $this->HasAcess
    } // foreach $rows

    // restore original values
    $this->tag = $save_tag;
    $this->page = $save_page;

    // now, sort the associative array's keys
    if (isset($vars['sort']))
    {
        if ($vars['sort'] == 'type')
        {
            $field = 2;
            $fn = 'asort';
        }
        else if ($vars['sort'] == 'cnt')
        {
            $field = 'cnt';
            $fn = 'arsort';
        }
    }
    // default case: sort on tag
    if (!$field)
    {
        $field = 1;
        $fn = 'asort';
    }
    $keyorder = array();
    foreach ($badlinks as $index => $cnt)
    {
        preg_match("/^(.+) (\w\w)\/(.+)\*(.+)$/", $index, $matches);
        $keyorder[$index] = ($field == 'cnt') ? $cnt : strtolower($matches[$field]);
    }
    // do the actual sorting
    $fn($keyorder);

    $output .= '<h3>'. CL_SUMMARY .'</h3>'. CLsummary($goodcnts, $badcnts).'<br />';
    if (!isset($vars['opts']) || !preg_match('/t/', $vars['opts'], $tmp))
    {
        $output .= '<h3>'. CL_DETAILED .'</h3>'. CLreport($this, $badlinks, $keyorder);
    }
}
echo $output;
// avoid side-effect if there were footnotes on checked pages
if (function_exists('FNprint'))
{
    FNprint($this, 'purge', '', $this->Href());
}
?>
