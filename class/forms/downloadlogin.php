<?php
// $Id: downloadlogin.php $
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
include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
/**
 * @package Cordemanon
 * @subpackage Form
 */
class DownloadFormLogin extends XoopsForm {
    /**
     * Target object
     *
     * @var CordemanonWhitepaper
     */
    var $target;
    
    /**
     * Create elements for this form
     *
     * @return void
     */
    function createElements() {
        $this->addElement(new XoopsFormHidden('whitepaperid', $this->target->getVar('whitepaperid')));
        
        $myts =& MyTextSanitizer::getInstance();
		global $xoopsModuleConfig;
		$this->addElement(new XoopsFormLabel('', $myts->displayTarea($xoopsModuleConfig['disclaimer'])));
		        
        $disclaimer = new XoopsFormCheckBox('', 'disclaimer', 0);
        $disclaimer->addOption(1, _CORD_MA_ACCEPTDISCLAIMER);
        $this->addElement($disclaimer);
        $this->addElement(new XoopsFormButton('', 'submit', _CORD_MA_SUBMITDOWNLOAD, 'submit'));
    }
}
?>