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
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
/**
 * @package Cordemanon
 * @subpackage Form
 */
class CategoryForm extends XoopsThemeForm {
    /**
     * Target object
     *
     * @var CordemanonCategory
     */
    var $target;
    
    /**
     * Create elements for the form from the target
     * 
     * @return void
     */
    function createElements() {
		if (!$this->target->isNew()) {
		    $this->addElement(new XoopsFormHidden('categoryid', $this->target->getVar('categoryid')));
		}
		$this->addElement(new XoopsFormText(_CORD_AM_CATEGORYNAME, 'category_name', 25, 255, $this->target->getVar('category_name', 'e')), true);
		$shortname = new XoopsFormText(_CORD_AM_CATEGORYSHORTNAME, 'category_shortname', 25, 255, $this->target->getVar('category_shortname', 'e'));
		$shortname->setDescription(_CORD_AM_SHORTNAMEDESC);
		$this->addElement($shortname, true);
		$this->addElement(new XoopsFormTextArea(_CORD_AM_CATEGORYDESC, 'category_description', $this->target->getVar('category_description', 'e'), 10));
		$this->addElement($this->getParentSelect());
		
		$this->addElement(new XoopsFormHidden('op', 'save'));
		$this->addElement(new XoopsFormButton('', 'submit', _CORD_AM_SAVECATEGORY, 'submit'));
    }
    
    /**
     * Get select form for parent selection
     *
     * @return XoopsFormSelect
     */
    function getParentSelect() {
        $element = new XoopsFormSelect(_CORD_AM_CATEGORYPARENT, 'category_parent', $this->target->getVar('category_parent', 'e'));
        $element->addOption(0, "--");
        
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
     * 
     * @return void
     */
    function addSelectElements(&$tree, &$element, $key = 0, $level = 0) {
        $categories = $tree->getFirstChild($key);
        foreach (array_keys($categories) as $i) {
            if ($categories[$i] == $this->target->getVar('categoryid')) {
                // Don't include this category or its subcats
                continue;
            }
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