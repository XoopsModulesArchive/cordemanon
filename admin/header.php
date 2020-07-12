<?php
// $Id:
###############################################################################
##                    Developed 2006 Jan Pedersen (aka Mithrandir)           ##
##                       <http://www.web-udvikling.dk>                       ##
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
if (!defined("XOOPS_ROOT_PATH")) {
    include_once '../../../include/cp_header.php';
}

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
	include_once(XOOPS_ROOT_PATH."/class/template.php");
	$xoopsTpl = new XoopsTpl();
}

function customerOnly() {
    global $xoopsUser, $xoopsModule;
    if (!is_object($xoopsModule) || $xoopsModule->getVar('dirname') != "cordemanon") {
        $mod_handler =& xoops_gethandler('module');
        $cordModule =& $mod_handler->getByDirname("cordemanon");
        $config_handler =& xoops_gethandler('config');
        $modConfig =& $config_handler->getConfigsByCat(0, $cordModule->getVar('mid'));
    }
    else {
        $modConfig =& $GLOBALS['xoopsModuleConfig'];
    }
    $groups = $xoopsUser->getGroups();
    $modConfig['admin_groups'][] = XOOPS_GROUP_ADMIN;
    return $groups == array() || array_intersect($groups, $modConfig['admin_groups']) == array();
}
?>