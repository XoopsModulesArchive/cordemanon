<?php
// $Id: whitepaper.php $
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

$handler = xoops_getmodulehandler('whitepaper');
$typetitle = _CORD_AM_WHITEPAPER;
$xoopsTpl->assign('typetitle', $typetitle);
$typetemplate = "cord_admin_whitepaperlist.html";

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : ($id > 0 ? "edit" : "list");

switch ($op) {
    default:
    case "list":

		xoops_cp_header();

        $xoopsTpl->assign('typetitle', $typetitle);
        $criteria = new CriteriaCompo();
        
        include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/forms/filter.php");
        $filterform = new FilterForm(_CORD_AM_FILTERFORM, 'filterform', 'whitepaper.php');

        if (customerOnly() ) {
            // Get customerid from userid
            $customer_handler =& xoops_getmodulehandler('customer');
            $customer =& $customer_handler->getByUid($xoopsUser->getVar('uid'));
            if (!$customer) {
                echo "ingen adgang";
                break;
            }
            $criteria->add(new Criteria('customerid', $customer->getVar('uid')));
            $filterform->customerid = $customer->getVar('uid');
        }
        elseif (isset($_REQUEST['customerid']) && $_REQUEST['customerid'] > 0) {
            $criteria->add(new Criteria('customerid', intval($_REQUEST['customerid'])));
            $filterform->customerid = intval($_REQUEST['customerid']);
        }

        $sortby = "whitepaper_title";
        $orderby = "ASC";
        if (isset($_REQUEST['sortby'])) {
            $options = $filterform->getSortOptions();
            if (isset($options[$_REQUEST['sortby']])) {
                $sortby = $options[$_REQUEST['sortby']]['fieldname'];
                $orderby = $options[$_REQUEST['sortby']]['orderby'];
                $filterform->sortby = $_REQUEST['sortby'];
            }
        }
        $criteria->setSort($sortby);
        $criteria->setOrder($orderby);

        
        if (isset($_REQUEST['categoryid']) && $_REQUEST['categoryid'] > 0) {
            $filterform->categoryid = intval($_REQUEST['categoryid']);
            
            // Find all subcategories
            $category_handler =& xoops_getmodulehandler('category');
            $categories = $category_handler->getObjects();
            
            include_once(XOOPS_ROOT_PATH."/class/tree.php");
            $tree = new XoopsObjectTree($categories, 'categoryid', 'category_parent');
            $subcats = $tree->getAllChild($filterform->categoryid);
            $categoryids = array_keys($subcats);
            $categoryids[] = $filterform->categoryid;
            
            // Find whitepaper IDs in the selected subcats
            $papercat_handler =& xoops_getmodulehandler('papercat');
            $papercats =& $papercat_handler->getObjects(new Criteria('categoryid', "(".implode(',', $categoryids).")", "IN"));
            foreach (array_keys($papercats) as $i) {
                $whitepaperids[] = $papercats[$i]->getVar('whitepaperid');
            }
            // add whitepaper criteria
            $criteria->add(new Criteria('whitepaperid', "(".implode(',', array_unique($whitepaperids)).")", "IN"));
        }
        
        $filterform->createElements();
        $filterform->assign($xoopsTpl);
        
        $count = $handler->getCount($criteria);
        $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $limit = 20;
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        
        $objects =& $handler->getObjects($criteria, true, false);
        unset($criteria);
        if (count($objects) > 0) {
            $xoopsTpl->assign('objects', $objects);
            $whitepaperids = array_keys($objects);
            
            // Get customers for whitepapers
            foreach ($whitepaperids as $i) {
                $customerids[] = $objects[$i]['customerid'];
            }
            $customer_handler = xoops_getmodulehandler('customer');
            $customer_criteria = new Criteria("customerid", "(".implode(',', array_unique($customerids)).")", "IN");
            $xoopsTpl->assign('customers', $customer_handler->getObjects($customer_criteria, true, false));
            unset($customer_criteria);
            
            // Get download counts
            $criteria = new Criteria('whitepaperid', "(".implode(',', $whitepaperids).")", "IN");
            $criteria->setGroupby("whitepaperid");
            $hit_handler = xoops_getmodulehandler('hit');
            $xoopsTpl->assign('download_counts', $hit_handler->getCount($criteria));
            unset($criteria);
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

		xoops_cp_header();

        $obj =& $handler->create();
        $form =& $obj->getForm(_ADD." ".$typetitle);
        $form->display();
        break;

    case "edit":
        if (!isset($_REQUEST['id'])) {
            redirect_header('index.php', 2, _CORD_AM_NOWHITEPAPERSELECTED);
        }

		xoops_cp_header();

        $obj =& $handler->get($_REQUEST['id']);
        if (customerOnly() ) {
            $customer_handler =& xoops_getmodulehandler('customer');
            $customer =& $customer_handler->get($obj->getVar('customerid'));
            if ($customer->getVar('uid') != $xoopsUser->getVar('uid')) {
                header('Location: whitepaper.php');
            }
        }
        $form =& $obj->getForm(_EDIT." ".$typetitle);
        $form->display();
        break;

    case "save":
        if (isset($_REQUEST['id'])) {
            $obj =& $handler->get($_REQUEST['id']);
            if (customerOnly() ) {
                $customer_handler =& xoops_getmodulehandler('customer');
                $customer =& $customer_handler->get($obj->getVar('customerid'));
                if ($customer->getVar('uid') != $xoopsUser->getVar('uid')) {
                    header('Location: whitepaper.php');
                }
            }
        }
        else {
            $obj =& $handler->create();
        }

        $obj->processFormSubmit();

        if ($handler->insert($obj) && $obj->postSave()) {
            redirect_header('whitepaper.php?op=list', 3, sprintf(_CORD_AM_SAVEDSUCCESS, getIdentifier($obj, $handler)));
        }
        else {
            xoops_cp_header();
            echo "<div class='errorMsg'>".implode('<br />', $obj->getErrors())."</div>";
            $form =& $obj->getForm(_EDIT." ".$typetitle);
            $form->display();
        }
        break;

    case "delete":
        $obj =& $handler->get($_REQUEST['id']);
        if ( customerOnly() ) {
            $customer_handler =& xoops_getmodulehandler('customer');
            $customer =& $customer_handler->get($obj->getVar('customerid'));
            if ($customer->getVar('uid') != $xoopsUser->getVar('uid')) {
                header('Location: whitepaper.php');
            }
        }
        if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
            if ($handler->delete($obj)) {
                redirect_header('whitepaper.php?op=list', 3, sprintf(_CORD_AM_DELETEDSUCCESS, $typetitle));
            }
            else {
                xoops_cp_header();
                echo implode('<br />', $obj->getErrors());
            }
        }
        else {

			xoops_cp_header();
            xoops_confirm(array('ok' => 1, 'id' => $_REQUEST['id'], 'op' => 'delete'), 'whitepaper.php', sprintf(_CORD_AM_RUSUREDEL, getIdentifier($obj, $handler)));
        }
        break;

}
if (isset($smartOption['template_main'])) {
	$xoopsTpl->display("db:".$smartOption['template_main']);
}
xoops_cp_footer();
?>