<?php
// $Id: statistics.php $
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
include "header.php";

xoops_cp_header();

$paper_handler =& xoops_getmodulehandler('whitepaper');
$hit_handler =& xoops_getmodulehandler('hit');

$whitepaper = $paper_handler->get($_REQUEST['whitepaperid']);

if (customerOnly() ) {
    // Get customerid from userid
    $customer_handler =& xoops_getmodulehandler('customer');
    $customer =& $customer_handler->getByUid($xoopsUser->getVar('uid'));
    if (!$customer || $whitepaper->getVar('customerid') != $customer->getVar('customerid')) {
        redirect_header('index.php', 3, _CORD_AM_NOACCESS);
        break;
    }
}

$limit = 50;
$start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;

$criteria = new CriteriaCompo(new Criteria('whitepaperid', $whitepaper->getVar('whitepaperid')));
if (isset($_REQUEST['m']) && isset($_REQUEST['y'])) {
    $start_time = mktime(0,0,0,intval($_REQUEST['m']), 1, intval($_REQUEST['y']));
    $end_time = mktime(0,0,0,intval($_REQUEST['m'])+1, 1, intval($_REQUEST['y']));
    $criteria->add(new Criteria('hit_time', $start_time, ">="));
    $criteria->add(new Criteria('hit_time', $end_time, "<"));
}
$hit_count = $hit_handler->getCount($criteria);

$criteria->setLimit($limit);
$criteria->setStart($start);
$criteria->setOrder('DESC');
$criteria->setSort('hit_time');

$hits = $hit_handler->getObjects($criteria, false, true);

foreach (array_keys($hits) as $i) {
    $uids[] = $hits[$i]['userid'];
}
$member_handler =& xoops_gethandler('member');
$users = $member_handler->getUserList(new Criteria('uid', "(".implode(',', $uids).")", "IN"));

$smartOption['template_main'] = "cord_admin_statistics.html";

$xoopsTpl->assign('whitepaper', $whitepaper->toArray());
$xoopsTpl->assign('users', $users);
$xoopsTpl->assign('stats', $hit_handler->getStats($whitepaper->getVar('whitepaperid')));
$xoopsTpl->assign('hits', $hits);

if ($hit_count > $limit) {
    include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/pagenav.php");
    $pagenav = new CordemanonPageNav($hit_count, $limit, $start, '', 'whitepaperid='.$whitepaper->getVar('whitepaperid'));
    $xoopsTpl->assign('pagenav', $pagenav->renderNav(5));
}

$xoopsTpl->display("db:".$smartOption['template_main']);

xoops_cp_footer();

?>