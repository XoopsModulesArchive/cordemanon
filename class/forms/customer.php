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
 * Include parent class
 */
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
/**
 * @package Cordemanon
 * @subpackage Form
 */
class CustomerForm extends XoopsThemeForm {
    /**
     * Target object
     *
     * @var CordemanonCustomer
     */
    var $target;
    
    /**
     * Create elements for the form from the target
     * 
     * @return void
     */
    function createElements() {
		if (!$this->target->isNew()) {
		    $this->addElement(new XoopsFormHidden('customerid', $this->target->getVar('customerid')));
		}
		$this->addElement(new XoopsFormText(_CORD_AM_CUSTOMERNAME, 'customer_name', 25, 255, $this->target->getVar('customer_name', 'e')));
		$shortname = new XoopsFormText(_CORD_AM_CUSTOMERSHORTNAME, 'customer_shortname', 25, 255, $this->target->getVar('customer_shortname', 'e'));
		$shortname->setDescription(_CORD_AM_SHORTNAMEDESC);
		$this->addElement($shortname, true);
		$this->addElement(new XoopsFormText(_CORD_AM_CUSTOMEREMAIL, 'customer_email', 25, 255, $this->target->getVar('customer_email', 'e')));
		
		if (!customerOnly()) {
		    include_once XOOPS_ROOT_PATH."/modules/cordemanon/class/formselectuserfromgroup.php";
		    // User select
		    $this->addElement(new XoopsFormSelectUserFromGroup(_CORD_AM_USER, 'uid', $GLOBALS['xoopsModuleConfig']['customer_group'], $this->target->getVar('uid'), 1, false, true));
		    
            $img_popup = "<img onmouseover='style.cursor=\"hand\"' onclick='javascript:openWithSelfMain(\"".XOOPS_URL."/imagemanager.php?target=customer_image\",\"imgmanager\",400,430);' src='".XOOPS_URL."/images/image.gif' alt='image' />";
            $image_tray = new XoopsFormElementTray(_CORD_AM_CUSTOMERIMAGE);
            $image_tray->addElement(new XoopsFormText('', 'customer_image', 25, 255, $this->target->getVar('customer_image', 'e')));
            $image_tray->addElement(new XoopsFormLabel('', $img_popup));
            $this->addElement($image_tray);
            
        }
		$this->addElement(new XoopsFormTextArea(_CORD_AM_CUSTOMERDESC, 'customer_desc', $this->target->getVar('customer_desc', 'e'), 15));
		
		$this->addElement(new XoopsFormHidden('op', 'save'));
		$this->addElement(new XoopsFormButton('', 'submit', _CORD_AM_SAVECUSTOMER, 'submit'));
    }
}
?>