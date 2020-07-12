<?php
// $Id: whitepapers.php $
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

function b_cord_whitepapers($options) {
	$limit = intval($options[0]);
	$sortby = intval($options[1]); //0 = latest, 1 = most downloaded
	$daysback = intval($options[2]);
	$handler = xoops_getmodulehandler('whitepaper', 'cordemanon');
	$whitepapers = $handler->getWhitepapers($limit, $sortby, $daysback);
	return array("whitepapers" => $whitepapers);
}

function b_cord_whitepapers_edit($options) {
	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
	$form = new XoopsFormElementTray('', '<br />');
	
	$sortby = new XoopsFormRadio(_CORD_B_SORTBY, 'options[1]', intval($options[1]));
	$sortby->addOption(0, _CORD_B_LATEST);
	$sortby->addOption(1, _CORD_B_POPULAR);
	
	$form->addElement(new XoopsFormText(_CORD_B_ITEMS, 'options[0]', 10, 8, intval($options[0])));
	$form->addElement($sortby);
	$form->addElement(new XoopsFormText(_CORD_B_DAYSBACK, 'options[2]', 10, 8, intval($options[2])));
	
	return $form->render();
}
?>