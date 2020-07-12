<?php
// $Id: rss.php $
###############################################################################
##                    XOOPS - PHP Content Management System                  ##
##                       Copyright (c) 2000 XOOPS.org                        ##
##                          <http://www.xoops.org/>                          ##
###############################################################################
##  This program is free software; you can redistribute it and/or modify     ##
##  it under the terms of the GNU General Public License as published by     ##
##  the Free Software Foundation; either version 2 of the License, or        ##
##  (at your option) any later version.                                      ##
##                                                                           ##
##  You may not change or alter any portion of this comment or credits       ##
##  of supporting developers from this source code or any supporting         ##
##  source code which is considered copyrighted (c) material of the          ##
##  original comment or credit authors.                                      ##
##                                                                           ##
##  This program is distributed in the hope that it will be useful,          ##
##  but WITHOUT ANY WARRANTY; without even the implied warranty of           ##
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            ##
##  GNU General Public License for more details.                             ##
##                                                                           ##
##  You should have received a copy of the GNU General Public License        ##
##  along with this program; if not, write to the Free Software              ##
##  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA ##
###############################################################################

include "../../mainfile.php";

if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}

if(isset($_GET['type']) AND $_GET['type'] == 'atom'){
    $feed_type = 'atom';
} else {
    $feed_type = 'rss';
}
$contents = ob_get_clean();
header ('Content-Type:text/xml; charset=utf-8');
$xoopsOption['template_main'] = 'cord_' . $feed_type . '.html';
$xoopsOption['pagetype'] = $feed_type;
//error_reporting(0);


include_once(XOOPS_ROOT_PATH."/class/template.php");
$xoopsTpl = new XoopsTpl();

$myts = MyTextSanitizer::getInstance();
$name = isset($_REQUEST['name']) ? $myts->addSlashes($_REQUEST['name']) : "";

// Find case and 
$case = "all";
$category_handler = xoops_getmodulehandler('category');
$category = $category_handler->getByName($name);
if (!$category) {
    // Category not found, see if there is a customer with the same shortname
    $customer_handler =& xoops_getmodulehandler('customer');
    $customer = $customer_handler->getByName($name);
    if ($customer) {
        $case = "customer";
    }
}
else {
    $case = "category";
}

switch ($case) {
    // Set cache_prefix
    default:
    case "all":
        $cache_prefix = 'cord|feed|' . $feed_type;
        break;

    case "category":
        $cache_prefix = 'cord|catfeed|' . $feed_type. '|'.$category->getVar('categoryid');
        break;

    case "customer":
        $cache_prefix = 'cord|custfeed|' . $feed_type. '|'.$customer->getVar('customerid');
        break;
}


$xoopsTpl->caching = true;
$xoopsTpl->cache_lifetime = $xoopsConfig['module_cache'][$xoopsModule->getVar('mid')];
if( ! $xoopsTpl->is_cached('db:'.$xoopsOption['template_main'], $cache_prefix) ) {
    // Get content
    $paper_handler = xoops_getmodulehandler('whitepaper');
    $limit = 30;
    switch ($case) {
        default:
        case "all":
            $shorthand = "all";
            $title = $xoopsConfig['sitename'] . ' - ' . $xoopsModule->getVar('name');
            $desc = $xoopsConfig['slogan'] ;
            $channel_url = XOOPS_URL . '/' . $feed_type . '/whitepapers';
            $whitepapers = $paper_handler->getWhitepapers($limit, 2, 0, true);
            $id = 0;
            break;

        case "category":
            $shorthand = "cat";
            $title = $xoopsConfig['sitename'] . ' - ' . $category->getVar('category_name');
            $desc = $xoopsConfig['slogan'] . ' - ' . $category->getVar('category_name');
            $channel_url = XOOPS_URL . '/' . $feed_type . '/whitepapers/' . $category->getVar('category_shortname');
            $whitepapers = $paper_handler->getByCategory($category, $limit, 0, true);
            $id = $category->getVar('categoryid');
            break;

        case "customer":
            $shorthand = "cust";
            $title = $xoopsConfig['sitename'] . ' - ' . $customer->getVar('customer_name');
            $desc = $xoopsConfig['slogan'] . ' - ' . $customer->getVar('customer_name');
            $channel_url = XOOPS_URL . '/' . $feed_type . '/whitepapers/' . $customer->getVar('customer_shortname');
            $whitepapers = $paper_handler->getByCustomer($customer, $limit, 0, true);
            $id = $customer->getVar('customerid');
            break;
    }

    /*
    * Assign feed-specific vars
    */

    $xoopsTpl->assign('channel_title', xoops_utf8_encode($title, 'n'));
    $xoopsTpl->assign('channel_desc', xoops_utf8_encode($desc, 'n'));
    $xoopsTpl->assign('channel_link', $channel_url);
    $xoopsTpl->assign('channel_lastbuild', formatTimestamp(time(), $feed_type));
    $xoopsTpl->assign('channel_webmaster', $xoopsConfig['adminmail']);
    $xoopsTpl->assign('channel_editor', $xoopsConfig['adminmail']);
    $xoopsTpl->assign('channel_editor_name', $xoopsConfig['sitename']);
    $xoopsTpl->assign('channel_category', 'Whitepapers');
    $xoopsTpl->assign('channel_generator', 'PHP');
    $xoopsTpl->assign('channel_language', _LANGCODE);

    /**
     * Assign whitepapers to template style array
     */

    $url = XOOPS_URL.'/whitepapers';
    if(count($whitepapers) > 0){
        // Get customers for whitepapers
        $customerids = array();
        foreach(array_keys($whitepapers) AS $i){
            $customerids[] = $whitepapers[$i]->getVar('customerid');
        }
        if (count($customerids) > 0) {
            $customer_handler =& xoops_getmodulehandler('customer');
            $customers = $customer_handler->getList(new Criteria('customerid', "(".implode(',', array_unique($customerids)).")", "IN"));
        }

        //Assign whitepapers to template
        foreach(array_keys($whitepapers) AS $i){
            $whitepaper = $whitepapers[$i];
            $link = $url.'/'.$whitepaper->getVar('whitepaperid') . '?a='.$shorthand.'_' . $feed_type . '&amp;i=' . $id;
            $title = $whitepaper->getVar('whitepaper_title', 'n');
            $teaser = $whitepaper->getVar('whitepaper_desc', 'n');
            $author = isset($customers[$whitepaper->getVar('customerid')]) ? $customers[$whitepaper->getVar('customerid')] : "";

            $xoopsTpl->append('items', array(
                                'title' => xoops_utf8_encode($title),
                                'author' => xoops_utf8_encode($author),
                                'link' => $link,
                                'guid' => $url.'/'.$whitepaper->getVar('whitepaperid'),
                                'is_permalink'=>false,
                                'pubdate' => formatTimestamp($whitepaper->getVar('whitepaper_publishdate'), $feed_type),
                                'dc_date' => formatTimestamp($whitepaper->getVar('whitepaper_publishdate'), 'd/m H:i'), 
                                'description' => xoops_utf8_encode($teaser)
                                ));
        }
    } else {
        $excuse_title = 'No whitepapers!';
        $excuse = 'There are no whitepapers for this feed!';
        $art_title = htmlspecialchars($excuse_title, ENT_QUOTES);
        $art_teaser = htmlspecialchars($excuse, ENT_QUOTES);
        $xoopsTpl->append('items', array('title' => xoops_utf8_encode($art_title), 
                                         'link' => $url, 
                                         'guid' => $url, 
                                         'pubdate' => formatTimestamp(time(), $feed_type), 
                                         'dc_date' => formatTimestamp(time(), 'd/m H:i'), 
                                         'description' => xoops_utf8_encode($art_teaser)));
    }
}

$xoopsTpl->display('db:'.$xoopsOption['template_main'], $cache_prefix);
?>