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
if (!defined("XOOPS_ROOT_PATH")) {
    die("Cannot access file directly");
}
/**
 * @package Cordemanon
 */

/**
 * Include parent class
 */
include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/processors/processor.php");
/**
 * @package Cordemanon
 * @subpackage Processor
 */
class CategoryProcessor extends CordemanonProcessor {
    /**
     * Process form submissal
     * 
     * @return void
     *
     */
    function process() {
        $this->target->setVar('category_name', $_REQUEST['category_name']);
        $this->target->setVar('category_shortname', $_REQUEST['category_shortname']);
        $this->target->setVar('category_description', $_REQUEST['category_description']);
        $this->target->setVar('category_parent', $_REQUEST['category_parent']);
    }
}
?>