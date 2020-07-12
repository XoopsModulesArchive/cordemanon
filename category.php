<?php
// $Id: category.php $
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

$myts = MyTextSanitizer::getInstance();
$name = isset($_REQUEST['name']) ? $myts->addSlashes($_REQUEST['name']) : redirect_header(XOOPS_URL."/whitepapers", 3, _CORD_MA_NOCATSELECTED);

$category_handler = xoops_getmodulehandler('category', 'cordemanon');
$category = $category_handler->getByName($name);
if (!$category) {
    // Category not found, see if there is a customer with the same shortname
    $customer_handler =& xoops_getmodulehandler('customer');
    $customer = $customer_handler->getByName($name);
    if (!$customer) {
        // Neither customer nor category found, redirect to whitepaper front page
        redirect_header(XOOPS_URL."/whitepapers", 3, _CORD_MA_ITEMNOTFOUND);
    }
    // Show customer details instead of category
    include "customer.php";
    exit;
}

$_GET['categoryid'] = $category->getVar('categoryid');

$xoopsOption['template_main'] = "cord_category.html";
include XOOPS_ROOT_PATH."/header.php";

// Get top categories
$allcats = $category_handler->getObjects(null, true, false);

// Create tree relationship
include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/arraytree.php");
$tree = new ArrayTree($allcats, 'categoryid', 'category_parent');

// Get whitepaper count per category
$papercounts = $category_handler->getCountByCategory();

// Add counts to tree elements
$category_handler->assignCounts($tree, $papercounts);

// Filter out empty categories and build subcat array from the selected category
$categories = array();
$topcats = $tree->getFirstChild($category->getVar('categoryid'));
foreach (array_keys($topcats) as $i) {
	if ($topcats[$i]['count'] > 0) {
		$categories[$i] = $topcats[$i];
		$subcats = $tree->getFirstChild($i);
		foreach (array_keys($subcats) as $k) {
			// Only add subcats with whitepapers
			if ($subcats[$k]['count'] > 0) {
				$categories[$i]['subcats'][$k] = $subcats[$k];
			}
		}
	}
}

// Get whitepapers from this category and subcategories
$allsubcats = $tree->getAllChild($category->getVar('categoryid'));

// Meta keywords and page title
$xoopsTpl->assign('xoops_pagetitle', $category->getVar('category_name', 'n')); // pagetitle
$xoopsTpl->assign('xoops_meta_description', $category->getVar('category_description', 'n'));
$keywords[] = $category->getVar('category_name', 'n');
if (count($allsubcats) > 0) {
    foreach ($allsubcats as $subcat) {
        $keywords[] = $subcat['category_name'];
    }
}
$xoopsTpl->assign('xoops_meta_keywords', implode(',', $keywords));

/**
 * Preferably, we will use a subquery to 
 * select x whitepapers from the whitepaper-table 
 * where whitepaperid in (SELECT whitepaperid FROM papercat-table WHERE categoryid IN ($catids) )
 * 
 * But until we know whether subqueries are available, we will do it this way
 * 
 * Alternatively, a select x whitepapers from whitepaper-table w, papercat-table pc where w.whitepaperid=pc.whitepaperid
 * AND categoryid IN ($catids) group by w.whitepaperid
 */

// Find category ids to look in
$catids = array_keys($allsubcats);
$catids[] = $category->getVar('categoryid');

// Get whitepaper ids to choose from
$papercat_handler = xoops_getmodulehandler('papercat');
$whitepapercatlinks = $papercat_handler->getObjects(new Criteria('categoryid', "(".implode(',', $catids).")", "IN"));
foreach (array_keys($whitepapercatlinks) as $i) {
	$whitepaperids[] = $whitepapercatlinks[$i]->getVar('whitepaperid');
}

$limit = $xoopsModuleConfig['category_limit'];
$start = isset($_REQUEST['p']) ? (intval($_REQUEST['p'])-1)*$limit : 0;
$sort = $xoopsModuleConfig['sortby'] == 1 ? 'whitepaper_date' : 'whitepaper_publishdate';

// Get x whitepapers
$paper_handler = xoops_getmodulehandler('whitepaper');
$criteria = new CriteriaCompo(new Criteria('whitepaperid', "(".implode(',', $whitepaperids).")", "IN"));
$criteria->add(new Criteria('whitepaper_active', 1));
$criteria->add(new Criteria('whitepaper_publishdate', time(), '<='));
$criteria->add(new Criteria('whitepaper_expiredate', time(), '>='));
$criteria->setLimit($limit);
$criteria->setStart($start);
$criteria->setSort($sort);
$criteria->setOrder('DESC');
$whitepapers = $paper_handler->getObjects($criteria, true, false);

// Page navigation
/**
 * @todo Remember to change count($whitepapers) to a counting query call when optimising the above
 */
$count = count($whitepapers);
$total_pages = ceil($count/$limit);
if ($total_pages > 1) {
    $current_page = $start/$limit;
	include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/pagenav.php");
	$pagenav = new CordemanonPageNav($total_pages, 1, $current_page, 'p');
	$xoopsTpl->assign('pagenav', $pagenav->renderNav(5));
}
$last = $start+$limit > $count ? $count : $start+$limit;
$xoopsTpl->assign('showing', sprintf(_CORD_MA_SHOWING, $start+1, $last, $count));

// Get customers
if (count($whitepapers) > 0) {
	foreach (array_keys($whitepapers) as $i) {
		$customerids[] = $whitepapers[$i]['customerid'];
	}
	$customer_handler = xoops_getmodulehandler('customer');
	$customers = $customer_handler->getObjects(new Criteria('customerid', "(".implode(',', $customerids).")", "IN"), true, false);
	$xoopsTpl->assign('customers', $customers);
}

// Assign categories to template - one sublevel to each top category
$xoopsTpl->assign('subcategories', $categories);
$xoopsTpl->assign('category', $category->toArray());
$xoopsTpl->assign('whitepapers', $whitepapers);
include XOOPS_ROOT_PATH."/footer.php";
?>