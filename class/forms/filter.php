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
class FilterForm extends XoopsThemeForm {
    
    var $customerid;
    var $categoryid;
    var $sortby;
    
    /**
     * Create elements for the form from the target
     * 
     * @return void
     */
    function createElements() {
        global $xoopsModule, $xoopsModuleConfig;
        include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/formselectdatetime.php");
		
		if (customerOnly()) {
		    $customer = new XoopsFormHidden('customerid', $GLOBALS['xoopsUser']->getVar('uid'));
		}
		else {
		    $customer = new XoopsFormSelect(_CORD_AM_WHITEPAPERCUSTOMER, 'customerid', $this->customerid);
		    $customer_handler = xoops_getmodulehandler('customer', 'cordemanon');
		    $customer->addOption(0, " -- ");
		    $customer->addOptionArray($customer_handler->getList());
		}
		$this->addElement($customer);
//		$this->addElement(new XoopsFormRadioYN(_CORD_AM_WHITEPAPERACTIVE, 'active', $this->active));
//		
//		$this->addElement(new CordemanonSelectDateTime(_CORD_AM_PUBLISHED_AFTER, 'pub_after', 15, $this->pub_after));
//		$this->addElement(new CordemanonSelectDateTime(_CORD_AM_PUBLISHED_BEFORE, 'pub_before', 15, $this->pub_before));
//		
//		$language = new XoopsFormSelect(_CORD_AM_WHITEPAPERLANGUAGE, 'whitepaper_language', $this->target->getVar('whitepaper_language', 'e'));
//		$language_options = array_map('trim', explode(',', $GLOBALS['xoopsModuleConfig']['languages']));
//		asort($language_options);
//		foreach ($language_options as $lang) { 
//		    $language->addOption($lang);
//		}
//		$this->addElement($language);
		
		// Category select
		$element =& $this->getCategorySelect();
        $this->addElement($element);
        
        $sortby = new XoopsFormSelect(_CORD_AM_SORTBY, 'sortby', $this->sortby);
        $options = $this->getSortOptions();
        foreach ($options as $key => $value) {
            $sortby->addOption($key, $value['name']);
        }
        $this->addElement($sortby);
        
		$this->addElement(new XoopsFormButton('', '', _CORD_AM_FILTER, 'submit'));
    }
    
    /**
     * Get category selector for this whitepaper
     *
     * @return XoopsFormSelect
     */
    function &getCategorySelect() {
        $element = new XoopsFormSelect(_CORD_AM_WHITEPAPERCATEGORY, 'categoryid', $this->categoryid);
        
        $category_handler =& xoops_getmodulehandler('category', 'cordemanon');
        $criteria = new CriteriaCompo();
        $criteria->setSort('category_name');
        $categories =& $category_handler->getObjects($criteria);
        
        include_once(XOOPS_ROOT_PATH."/class/tree.php");
        $tree = new XoopsObjectTree($categories, 'categoryid', 'category_parent');
        
        $element->addOption(0, " -- ");
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
    
    /**
     * Get options to sort by
     * 
     * @return array
     */
    function getSortOptions() {
        $options = array(1 => array('name' => _CORD_AM_WHITEPAPERTITLE, 'fieldname' => 'whitepaper_title', 'orderby' => 'ASC'),
                         2 => array('name' => _CORD_AM_WHITEPAPERHITS, 'fieldname' => 'whitepaper_hits', 'orderby' => 'DESC'),
                         3 => array('name' => _CORD_AM_WHITEPAPERPUBDATE." ("._ASCENDING.")", 'fieldname' => 'whitepaper_publishdate', 'orderby' => 'ASC'),
                         4 => array('name' => _CORD_AM_WHITEPAPERPUBDATE." ("._DESCENDING.")", 'fieldname' => 'whitepaper_publishdate', 'orderby' => 'DESC')
                         );
        return $options;
    }
}
?>