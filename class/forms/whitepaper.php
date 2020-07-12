<?php
// $Id: whitepaper.php $
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
class WhitepaperForm extends XoopsThemeForm {
    /**
     * Target object
     *
     * @var CordemanonWhitepaper
     */
    var $target;
    
    /**
     * Create elements for the form from the target
     * 
     * @return void
     */
    function createElements() {
        global $xoopsModule, $xoopsModuleConfig;
        include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/formselectdatetime.php");
        if (!$this->target->isNew()) {
		    $this->addElement(new XoopsFormHidden('whitepaperid', $this->target->getVar('whitepaperid')));
		}
		else {
		    $this->target->assignVar('customerid', $GLOBALS['xoopsUser']->getVar('uid'));
		}
		
		if (!customerOnly()) {
		    $customer = new XoopsFormSelect(_CORD_AM_WHITEPAPERCUSTOMER, 'customerid', $this->target->getVar('customerid', 'e'));
		    $customer_handler = xoops_getmodulehandler('customer', 'cordemanon');
		    $customer->addOptionArray($customer_handler->getList());
		}
		$this->addElement($customer);
		$this->addElement(new XoopsFormText(_CORD_AM_WHITEPAPERTITLE, 'whitepaper_title', 35, 255, $this->target->getVar('whitepaper_title', 'e')));
		
		$this->addElement(new XoopsFormRadioYN(_CORD_AM_WHITEPAPERACTIVE, 'whitepaper_active', $this->target->getVar('whitepaper_active', 'e')));
		$this->addElement(new CordemanonSelectDateTime(_CORD_AM_WHITEPAPERDATE, 'whitepaper_date', 15, $this->target->getVar('whitepaper_date', 'e')));
		$this->addElement(new CordemanonSelectDateTime(_CORD_AM_WHITEPAPERPUBLISHDATE, 'whitepaper_publishdate', 15, $this->target->getVar('whitepaper_publishdate', 'e')));
		$this->addElement(new CordemanonSelectDateTime(_CORD_AM_WHITEPAPEREXPIREDATE, 'whitepaper_expiredate', 15, $this->target->getVar('whitepaper_expiredate', 'e')));
		$this->addElement(new XoopsFormText(_CORD_AM_WHITEPAPERPAGES, 'whitepaper_pages', 10, 25, $this->target->getVar('whitepaper_pages', 'e')));
		
		$language = new XoopsFormSelect(_CORD_AM_WHITEPAPERLANGUAGE, 'whitepaper_language', $this->target->getVar('whitepaper_language', 'e'));
		$language_options = array_map('trim', explode(',', $GLOBALS['xoopsModuleConfig']['languages']));
		asort($language_options);
		foreach ($language_options as $lang) { 
		    $language->addOption($lang);
		}
		$this->addElement($language);
		
		$level = new XoopsFormRadio(_CORD_AM_WHITEPAPERLEVEL, 'whitepaper_level', $this->target->getVar('whitepaper_level', 'e'));
		$level->addOption(1, _CORD_AM_WHITEPAPERLOGIN);
		$level->addOption(2, _CORD_AM_WHITEPAPERCONTACT);
		$this->addElement($level);
		
		$this->addElement(new XoopsFormTextArea(_CORD_AM_WHITEPAPERSHORTDESCRIPTION, 'whitepaper_shortdesc', $this->target->getVar('whitepaper_shortdesc', 'e'), 5));
		$this->addElement(new XoopsFormTextArea(_CORD_AM_WHITEPAPERDESCRIPTION, 'whitepaper_desc', $this->target->getVar('whitepaper_desc', 'e'), 15));
		
		// PDF upload
		if (is_dir($this->target->getUploadPath()) && is_writable($this->target->getUploadPath())) {
		    $maxfilesize = $xoopsModuleConfig['max_file_size']*1024;
		    $this->addElement(new XoopsFormFile(_CORD_AM_WHITEPAPERFILE, 'whitepaper_file', $maxfilesize));
		}
		else {
		    $this->addElement(new XoopsFormLabel('', "<div class='errorMsg'>".sprintf(_CORD_AM_UNWRITEABLE, $this->target->getUploadPath())."</div>"));
		}
		if (file_exists($this->target->getFilePath())) {
		    $this->addElement(new XoopsFormLabel('', "<a href='".XOOPS_URL."/modules/cordemanon/admin/download.php?id=".$this->target->getVar('whitepaperid')."'>Download</a>"));
		}

		// Category select
		$element =& $this->getCategorySelect();
        $this->addElement($element);
        
		$this->addElement(new XoopsFormHidden('op', 'save'));
		$this->addElement(new XoopsFormButton('', 'submit', _CORD_AM_SAVEWHITEPAPER, 'submit'));
		$this->setExtra("enctype=\"multipart/form-data\"");
    }
    
    /**
     * Get category selector for this whitepaper
     *
     * @return XoopsFormSelect
     */
    function &getCategorySelect() {
        $element = new XoopsFormSelect(_CORD_AM_WHITEPAPERCATEGORY, 'categories', $this->target->getCategories(), 25, true);
        $category_handler =& xoops_getmodulehandler('category', 'cordemanon');
        $criteria = new CriteriaCompo();
        $criteria->setSort('category_name');
        $categoryies =& $category_handler->getObjects($criteria);
        include_once(XOOPS_ROOT_PATH."/class/tree.php");
        $tree = new XoopsObjectTree($categoryies, 'categoryid', 'category_parent');
        
        $this->addSelectElements($tree, $element);
        return $element;
    }
    
    /**
     * Add options to select from a tree
     *
     * @param XoopsObjectTree $tree
     * @param XoopsFormSelect $element
     * @param int $key Parent to start from
     * @param int $level Which recursion level we are at
     * 
     * @return void
     */
    function addSelectElements(&$tree, &$element, $key = 0, $level = 0) {
        $categories = $tree->getFirstChild($key);
        foreach (array_keys($categories) as $i) {
            // add option
            $length = strlen($categories[$i]->getVar('category_name')) + ($level*2);
            $name = str_pad($categories[$i]->getVar('category_name'), $length, "-", STR_PAD_LEFT);
            $element->addOption($categories[$i]->getVar('categoryid'), $name);
            // recurse
            $this->addSelectElements($tree, $element, $categories[$i]->getVar('categoryid'), $level+1);
        }
    }
}
?>