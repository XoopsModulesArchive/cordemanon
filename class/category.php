<?php
// $Id: xoops_version.php $
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
 * Make sure object handler is included
 */
if (!class_exists("CordPersistableObjectHandler")) {
	include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/object.php");
}
/**
 * @package Cordemanon
 * @subpackage Objects
 */
class CordemanonCategory extends CordemanonObject {
	function CordemanonCategory() {
		$this->initVar('categoryid', XOBJ_DTYPE_INT);
		$this->initVar('category_name', XOBJ_DTYPE_TXTBOX, '', true);
		$this->initVar('category_shortname', XOBJ_DTYPE_TXTBOX, '');
		$this->initVar('category_description', XOBJ_DTYPE_TXTAREA, '');
		$this->initVar('category_parent', XOBJ_DTYPE_INT, 0);
	}
}
/**
 * @package Cordemanon
 * @subpackage Objects
 */
class CordemanonCategoryHandler extends CordPersistableObjectHandler {
	function CordemanonCategoryHandler($db) {
		$this->CordPersistableObjectHandler($db, 'cord_category', 'CordemanonCategory', 'categoryid', 'category_name');
	}
	
	/**
	 * Get a category from its shortname
	 *
	 * @param string $name
	 * @return CordemanonCategory|false
	 */
	function &getByName($name) {
	    $criteria = new Criteria('category_shortname', $name);
	    $criteria->setLimit(1);
	    $obj_arr =& $this->getObjects($criteria, false, true);
	    if (!isset($obj_arr[0])) {
	        $ret = false;
	        return $ret;
	    }
	    return $obj_arr[0];
	}
	
	/**
	 * Get number of active whitepapers in each category
	 *
	 * @return array
	 */
	function getCountByCategory() {
		$papercat_handler = xoops_getmodulehandler('papercat', 'cordemanon');
		return $papercat_handler->getCountByCategory();
	}
	
	/**
	 * Assign category counts to categories recursively
	 *
	 * @param ArrayTree $tree
	 * @param array $papercounts
	 * @param int $root [unassigned when called from the outside]
	 * 
	 * @return void
	 */
	function assignCounts(&$tree, $papercounts, $root = 0) {
		$childcats =& $tree->getAllChild($root);
		foreach (array_keys($childcats) as $i ) {
			// assign count
			$count = isset($papercounts[$i]) ? $papercounts[$i] : 0;
			$childcats[$i]['count'] = isset($childcats[$i]['count']) ? $childcats[$i]['count'] + $count : $count;

			// add this category's counts to parents
			$parents =& $tree->getAllParent($i);
			if (count($parents) > 0) {
				foreach (array_keys($parents) as $k) {
					$parents[$k]['count'] = isset($parents[$k]['count']) ? $parents[$k]['count'] + $count : $count;
				}
			}
			// recurse
			$this->assignCounts($tree, $papercounts, $i);
		}
	}
}
?>