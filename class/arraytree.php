<?php
// $Id: arraytree.php,v 1.3 2005/06/27 08:29:27 jkp Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
/**
 * @package Cordemanon
 */


/**
 * A tree structures with arrays as nodes
 *
 * @package		Cordemanon
 *
 * @author		Jan Pedersen	<jkp@cusix.dk>
 */
 class ArrayTree {
     /**#@+
     * @access	private
     */
     var $_parentId;
     var $_myId;
     var $_rootId = null;
     var $_tree = array();
     var $_objects;
     /**#@-*/
     /**
	 * Constructor
	 * 
	 * @param   array   $objectArr  Array of arrays 
	 * @param   string     $myId       field name of object ID
	 * @param   string     $parentId   field name of parent object ID
	 * @param   string     $rootId     field name of root object ID
	 **/
	 function ArrayTree(&$objectArr, $myId, $parentId, $rootId = null)
	 {
	     $this->_objects =& $objectArr;
	     $this->_myId = $myId;
	     $this->_parentId = $parentId;
	     if (isset($rootId)) {
	         $this->_rootId = $rootId;
	     }
	     $this->_initialize();
	 }

	 /**
	 * Initialize the object
	 * 
	 * @access	private
	 **/
	 function _initialize()
	 {
	     foreach (array_keys($this->_objects) as $i) {
	         $key1 = $this->_objects[$i][$this->_myId];
	         $this->_tree[$key1]['obj'] =& $this->_objects[$i];
	         $key2 = $this->_objects[$i][$this->_parentId];
	         $this->_tree[$key1]['parent'] = $key2;
	         $this->_tree[$key2]['child'][] = $key1;
	         if (isset($this->_rootId)) {
	             $this->_tree[$key1]['root'] = $this->_objects[$i][$this->_rootId];
	         }
	     }
	 }

	 /**
	 * Get the tree
	 * 
	 * @return  array   Associative array comprising the tree 
	 **/
	 function &getTree()
	 {
	     return $this->_tree;
	 }

	 /**
	 * returns an object from the tree specified by its id
	 * 
	 * @param   string  $key    ID of the object to retrieve
     * @return  object  Object within the tree
	 **/
	 function &getByKey($key)
	 {
	     return $this->_tree[$key]['obj'];
	 }

	 /**
	 * returns an array of all the first child object of an object specified by its id
	 * 
	 * @param   string  $key    ID of the parent object
	 * @return  array   Array of children of the parent
	 **/
	 function &getFirstChild($key)
	 {
	     $ret = array();
	     if (isset($this->_tree[$key]['child'])) {
	         foreach ($this->_tree[$key]['child'] as $childkey) {
	             $ret[$childkey] =& $this->_tree[$childkey]['obj'];
	         }
	     }
	     return $ret;
	 }

	 /**
	 * returns an array of all child objects of an object specified by its id
	 * 
	 * @param   string     $key    ID of the parent
	 * @param   array   $ret    (Empty when called from client) Array of children from previous recursions.
	 * @return  array   Array of child nodes.
	 **/
	 function &getAllChild($key, $ret = array())
	 {
	     if (isset($this->_tree[$key]['child'])) {
	         foreach ($this->_tree[$key]['child'] as $childkey) {
	             $ret[$childkey] =& $this->_tree[$childkey]['obj'];
	             $children =& $this->getAllChild($childkey, $ret);
	             foreach (array_keys($children) as $newkey) {
	                 $ret[$newkey] =& $children[$newkey];
	             }
	         }
	     }
	     return $ret;
	 }

	 /**
     * returns an array of all parent objects.
     * the key of returned array represents how many levels up from the specified object
	 * 
	 * @param   string     $key    ID of the child object
	 * @param   array   $ret    (empty when called from outside) Result from previous recursions
	 * @param   int $uplevel (empty when called from outside) level of recursion
	 * @return  array   Array of parent nodes. 
	 **/
	 function &getAllParent($key, $ret = array(), $uplevel = 1)
	 {
	     if (isset($this->_tree[$key]['parent']) && isset($this->_tree[$this->_tree[$key]['parent']]['obj'])) {
	         $ret[$uplevel] =& $this->_tree[$this->_tree[$key]['parent']]['obj'];
	         $parents =& $this->getAllParent($this->_tree[$key]['parent'], $ret, $uplevel+1);
	         foreach (array_keys($parents) as $newkey) {
	             $ret[$newkey] =& $parents[$newkey];
	         }
	     }
	     return $ret;
	 }

	 /**
	 * Make options for a select box from
	 * 
	 * @param   string  $fieldName   Name of the member variable from the
     *  node objects that should be used as the title for the options.
	 * @param   string  $selected    Value to display as selected
	 * @param   int $key         ID of the object to display as the root of select options
     * @param   string  $ret         (reference to a string when called from outside) Result from previous recursions
	 * @param   string  $prefix_orig  String to indent items at deeper levels
	 * @param   string  $prefix_curr  String to indent the current item
	 * @return void
     * 
     * @access	private 
	 **/
	 function _makeSelBoxOptions($fieldName, $selected, $key, &$ret, $prefix_orig, $prefix_curr = '')
	 {
	     if ($key > 0) {
	         $value = $this->_tree[$key]['obj'][$this->_myId];
	         $ret .= '<option value="'.$value.'"';
	         if ($value == $selected) {
	             $ret .= ' selected="selected"';
	         }
	         $ret .= '>'.$prefix_curr.$this->_tree[$key]['obj'][$fieldName].'</option>';
	         $prefix_curr .= $prefix_orig;
	     }
	     if (isset($this->_tree[$key]['child']) && !empty($this->_tree[$key]['child'])) {
	         foreach ($this->_tree[$key]['child'] as $childkey) {
	             $this->_makeSelBoxOptions($fieldName, $selected, $childkey, $ret, $prefix_orig, $prefix_curr);
	         }
	     }
	 }

	 /**
	 * Make a select box with options from the tree
	 * 
	 * @param   string  $name            Name of the select box
	 * @param   string  $fieldName       Name of the member variable from the
     *  node objects that should be used as the title for the options.  
	 * @param   string  $prefix          String to indent deeper levels
	 * @param   string  $selected        Value to display as selected
	 * @param   bool    $addEmptyOption  Set TRUE to add an empty option with value "0" at the top of the hierarchy
	 * @param   integer $key             ID of the object to display as the root of select options
	 * @return  string  HTML select box
	 **/
	 function &makeSelBox($name, $fieldName, $prefix='-', $selected='', $addEmptyOption = false, $key=0)
	 {
	     $ret = '<select name='.$name.'>';
	     if (false != $addEmptyOption) {
	         $ret .= '<option value="0"></option>';
	     }
	     $this->_makeSelBoxOptions($fieldName, $selected, $key, $ret, $prefix);
	     return $ret.'</select>';
	 }

	/**
    * Indent titles according to parent-child status
    *
    */
    function formatIndent($fieldName, $prefix_orig, $prefix_curr = '', $key = 0) {
        if ($key > 0) {
            $this->_tree[$key]['obj'][$fieldName] = $prefix_curr.$this->_tree[$key]['obj'][$fieldName];
            $prefix_curr .= $prefix_orig;
        }

        if (isset($this->_tree[$key]['child']) && !empty($this->_tree[$key]['child'])) {
            foreach ($this->_tree[$key]['child'] as $childkey) {
                $this->formatIndent($fieldName, $prefix_orig, $prefix_curr, $childkey);
            }
        }
    }
    
    /**
	 * returns an array of all child objects of an array as subarrays to the array
	 * 
	 * @param   string     $key    ID of the parent
	 * @param   array   $ret    (Empty when called from client) Array of children from previous recursions.
	 * @return  array   Array of child nodes.
	 **/
	 function &getAllChildArray($key, $ret = array())
	 {
	     $ret[$key] = $this->_tree[$key]['obj'];
	     if (isset($this->_tree[$key]['child'])) {
	         foreach ($this->_tree[$key]['child'] as $childkey) {
	             $ret[$key]['children'] = $this->getAllChildArray($childkey, $ret);
	         }
	     }
	     return $ret;
	 }
	 
	 /**
	 * returns a nicely formatted string of links to the parents of the selected element
	 *
	 * @param int $sel_id ID of selected element
	 * @param string $funcURL URL to point to
	 * @param string $path recursive element
	 *
	 * @return string
	 */
	 function getNicePathFromId($sel_id, $funcURL, $path="")
	{
	    $parents = $this->getAllParent($sel_id);
	    
	    foreach ($parents as $key => $element) {
	        $path = "<a href='".$funcURL."&amp;".$idName."=".$key."'>".$element[$this->_myId]."</a> &nbsp;:&nbsp;".$path;
	    }
	    return $path;		
	}
 }
?>