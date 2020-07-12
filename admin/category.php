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

if (customerOnly() ) {
    header("Location: index.php");
}

function getIdentifier($obj, $handler) {
    if ($handler->identifierName != "") {
        return $obj->getVar($handler->identifierName);
    }
    global $typetitle;
    return $typetitle;
}

$handler = xoops_getmodulehandler('category');
$typetitle = _CORD_AM_CATEGORY;
$xoopsTpl->assign('typetitle', $typetitle);
$typetemplate = "cord_admin_categorylist.html";
$sortby = "category_name";
$order = "ASC";

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : ($id > 0 ? "edit" : "list");

switch ($op) {
    default:
    case "list":

		xoops_cp_header();

        $xoopsTpl->assign('typetitle', $typetitle);
        $criteria = new CriteriaCompo();
        
        $criteria->setSort('category_name');

        $categories =& $handler->getObjects($criteria, true, false);
        unset($criteria);
        
        $xoopsTpl->assign('categories', $categories);
        
        $smartOption['template_main'] = $typetemplate;

      
        break;

    case "new":

		xoops_cp_header();

        $obj =& $handler->create();
        $form =& $obj->getForm(_ADD." ".$typetitle);
        $form->display();
        break;

    case "edit":
        if (!isset($_REQUEST['id'])) {
            redirect_header('category.php', 2, _CORD_AM_NOCATEGORYSELECTED);
        }

		xoops_cp_header();

        $obj =& $handler->get($_REQUEST['id']);
        $form =& $obj->getForm(_EDIT." ".$typetitle);
        $form->display();
        break;

    case "save":
        if (isset($_REQUEST['id'])) {
            $obj =& $handler->get($_REQUEST['id']);
        }
        else {
            $obj =& $handler->create();
        }

        $obj->processFormSubmit();

        if ($handler->insert($obj) && $obj->postSave()) {
            redirect_header('category.php?op=list', 3, sprintf(_CORD_AM_SAVEDSUCCESS, getIdentifier($obj, $handler)));
        }
        else {
            xoops_cp_header();
            echo "<div class='errorMsg'>".implode('<br />', $obj->getErrors())."</div>";
            $form =& $obj->getForm();
            $form->display();
        }
        break;

    case "delete":
        $obj =& $handler->get($_REQUEST['id']);
        if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
            if ($handler->delete($obj)) {
                redirect_header('category.php?op=list', 3, sprintf(_CORD_AM_DELETEDSUCCESS, $typetitle));
            }
            else {
                xoops_cp_header();
                echo implode('<br />', $obj->getErrors());
            }
        }
        else {

			xoops_cp_header();
            xoops_confirm(array('ok' => 1, 'id' => $_REQUEST['id'], 'op' => 'delete'), 'category.php', sprintf(_CORD_AM_RUSUREDEL, getIdentifier($obj, $handler)));
        }
        break;

}
if (isset($smartOption['template_main'])) {
	$xoopsTpl->display("db:".$smartOption['template_main']);
}
xoops_cp_footer();
?>