<?php
// $Id: customer.php $
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

// If customer object is not instantiated already (see category.php)
if (!isset($customer) || !is_object($customer)) {
    $name = isset($_REQUEST['name']) ? $myts->addSlashes($_REQUEST['name']) : redirect_header(XOOPS_URL."/whitepapers", 3, _CORD_MA_NOCATSELECTED);
    $customer_handler =& xoops_getmodulehandler('customer');
    $customer = $customer_handler->getByName($name);
    if (!$customer) {
        // customer not found, redirect to whitepaper front page
        redirect_header(XOOPS_URL."/whitepapers", 3, _CORD_MA_ITEMNOTFOUND);
    }
}

$GLOBALS['cord']['customerid'] = $customer->getVar('customerid');
$_GET['categoryid'] = $customer->getVar('customerid') + 5000;

$xoopsOption['template_main'] = "cord_customer.html";
include XOOPS_ROOT_PATH."/header.php";

// Meta and page title
$xoopsTpl->assign('xoops_page_title', $customer->getVar('customer_name', 'n'));

// Get whitepapers for customer
$paper_handler =& xoops_getmodulehandler('whitepaper');
$criteria = new CriteriaCompo(new Criteria('customerid', $customer->getVar('customerid')));
$criteria->add(new Criteria('whitepaper_active', 1));
$criteria->add(new Criteria('whitepaper_publishdate', time(), '<='));
$criteria->add(new Criteria('whitepaper_expiredate', time(), '>='));
$criteria->setSort('whitepaper_date');
$criteria->setOrder('DESC');
$whitepapers =& $paper_handler->getObjects($criteria, false, false);

$xoopsTpl->assign('customer', $customer->toArray());
$xoopsTpl->assign('whitepapers', $whitepapers);
$xoopsTpl->assign('customers', array($customer->getVar('customerid') => $customer->toArray()));

include XOOPS_ROOT_PATH."/footer.php";
?>