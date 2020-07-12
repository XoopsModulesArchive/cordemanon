<?php
// $Id: customer.php $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
include "header.php";

function getIdentifier($obj, $handler) {
    if ($handler->identifierName != "") {
        return $obj->getVar($handler->identifierName);
    }
    global $typetitle;
    return $typetitle;
}

$handler = xoops_getmodulehandler('customer');
$typetitle = _CORD_AM_CUSTOMER;
$xoopsTpl->assign('typetitle', $typetitle);
$typetemplate = "cord_admin_customerlist.html";
$sortby = "customer_name";
$order = "ASC";

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : ($id > 0 ? "edit" : "list");

switch ($op) {
    default:
    case "list":
        
        // Permissions check for only customer's own account in which case it should go to edit that one
        if (customerOnly()) {
            // Get customerid from userid
            $customer =& $handler->getByUid($xoopsUser->getVar('uid'));
            header('Location: customer.php?id='.$customer->getVar('customerid'));
        }

		xoops_cp_header();

        $xoopsTpl->assign('typetitle', $typetitle);
        $criteria = new CriteriaCompo();
        
        $count = $handler->getCount($criteria);
        $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $limit = 20;
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        if (isset($sortby) && $sortby != "") {
            $criteria->setSort($sortby);
            $criteria->setOrder($order);
        }
        
        $objects =& $handler->getObjects($criteria, true, false);
        unset($criteria);
        if (count($objects) > 0) {
            $xoopsTpl->assign('objects', $objects);
            $customerids = array_keys($objects);
            
            // Get whitepaper counts
            $criteria = new Criteria('customerid', "(".implode(',', $customerids).")", "IN");
            $criteria->setGroupby("customerid");
            $paper_handler = xoops_getmodulehandler('whitepaper');
            $xoopsTpl->assign('paper_counts', $paper_handler->getCount($criteria));
        }
        $smartOption['template_main'] = $typetemplate;

        // Page navigation
        if ($count > $limit) {
            include_once XOOPS_ROOT_PATH."/class/pagenav.php";
            $nav = new XoopsPageNav($count, $limit, $start, "start");
            $xoopsTpl->assign("pagenav", $nav->renderNav(20));
        }
        break;

    case "new":

        if (customerOnly()) {
            // edit self
            $customer =& $handler->getByUid($xoopsUser->getVar('uid'));
            header('Location: customer.php?id='.$customer->getVar('customerid'));
        }
		xoops_cp_header();

        $obj =& $handler->create();
        $form =& $obj->getForm(_ADD." ".$typetitle);
        $form->display();
        break;

    case "edit":
        if (customerOnly() ) {
            // Get customer from userid
            $customer =& $handler->getByUid($xoopsUser->getVar('uid'));
            $_REQUEST['id'] = $customer->getVar('customerid');
        }

        if (!isset($_REQUEST['id'])) {
            redirect_header('customer.php', 2, _CORD_AM_NOCUSTOMERSELECTED);
        }

		xoops_cp_header();

        $obj =& $handler->get($_REQUEST['id']);
        $form =& $obj->getForm(_EDIT." ".$typetitle);
        $form->display();
        break;

    case "save":
        if (customerOnly() ) {
            $customer =& $handler->getByUid($xoopsUser->getVar('uid'));
            $_REQUEST['id'] = $customer->getVar('customerid');
        }
        if (isset($_REQUEST['id'])) {
            $obj =& $handler->get($_REQUEST['id']);
        }
        else {
            $obj =& $handler->create();
        }

        $obj->processFormSubmit();

        if ($handler->insert($obj) && $obj->postSave()) {
            redirect_header('customer.php?op=list', 3, sprintf(_CORD_AM_SAVEDSUCCESS, getIdentifier($obj, $handler)));
        }
        else {
            xoops_cp_header();
            echo "<div class='errorMsg'>".implode('<br />', $obj->getErrors())."</div>";
            $form =& $obj->getForm();
            $form->display();
        }
        break;

    case "delete":
        if (customerOnly() ) {
            redirect_header('customer.php', 3, _CORD_AM_CANNOTDELETESELF);
        }

        $obj =& $handler->get($_REQUEST['id']);
        if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
            if ($handler->delete($obj)) {
                redirect_header('customer.php?op=list', 3, sprintf(_CORD_AM_DELETEDSUCCESS, $typetitle));
            }
            else {
                xoops_cp_header();
                echo implode('<br />', $obj->getErrors());
            }
        }
        else {

			xoops_cp_header();
            xoops_confirm(array('ok' => 1, 'id' => $_REQUEST['id'], 'op' => 'delete'), 'customer.php', sprintf(_CORD_AM_RUSUREDEL, getIdentifier($obj, $handler)));
        }
        break;

}
if (isset($smartOption['template_main'])) {
	$xoopsTpl->display("db:".$smartOption['template_main']);
}
xoops_cp_footer();
?>