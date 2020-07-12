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
class CordemanonCustomer extends CordemanonObject {
	function CordemanonCustomer() {
		$this->initVar('customerid', XOBJ_DTYPE_INT);
		$this->initVar('customer_name', XOBJ_DTYPE_TXTBOX);
		$this->initVar('customer_shortname', XOBJ_DTYPE_TXTBOX);
		$this->initVar('customer_email', XOBJ_DTYPE_EMAIL);
		$this->initVar('customer_image', XOBJ_DTYPE_TXTBOX);
		$this->initVar('customer_desc', XOBJ_DTYPE_TXTAREA);
		$this->initVar('uid', XOBJ_DTYPE_INT);
	}
}
/**
 * @package Cordemanon
 * @subpackage Objects
 */
class CordemanonCustomerHandler extends CordPersistableObjectHandler {
	function CordemanonCustomerHandler($db) {
		$this->CordPersistableObjectHandler($db, 'cord_customer', 'CordemanonCustomer', 'customerid', 'customer_name');
	}
	
	/**
	 * Get a customer from its shortname
	 *
	 * @param string $name
	 * @return CordemanonCustomer|false
	 */
	function &getByName($name) {
	    $criteria = new Criteria('customer_shortname', $name);
	    $criteria->setLimit(1);
	    $obj_arr =& $this->getObjects($criteria, false, true);
	    if (!isset($obj_arr[0])) {
	        $obj_arr[0] = false;
	    }
	    return $obj_arr[0];
	}
	
	/**
	 * Get a customer from a user's id
	 *
	 * @param int $id
	 * @return CordemanonCustomer|false
	 */
	function &getByUid($id) {
	    $criteria = new Criteria('uid', intval($id));
	    $criteria->setLimit(1);
	    $obj_arr =& $this->getObjects($criteria, false, true);
	    if (!isset($obj_arr[0])) {
	        $obj_arr[0] = false;
	    }
	    return $obj_arr[0];
	}
}
?>