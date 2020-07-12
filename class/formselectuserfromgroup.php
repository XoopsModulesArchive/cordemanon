<?php
// $Id: formselectuserfromgroup.php,v 1.2 2005/04/25 12:13:15 jkp Exp $
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
if (!defined("XOOPS_ROOT_PATH")) {
    die("Cannot access file directly");
}
/**
 * @package     Cordemanon
 * 
 * @author	    Jan Pedersen	<jkp@cusix.dk>
 */

/**
 * Parent
 */
include_once XOOPS_ROOT_PATH."/class/xoopsform/formselect.php";
/**
 * A select field with a choice of available users
 * 
 * @package     Cordemanon
 * 
 * @author	    Jan Keller Pedersen	<jkp@cusix.dk>
 * @copyright	copyright (c) 2006 Cusix.dk
 */
class XoopsFormSelectUserFromGroup extends XoopsFormSelect
{
	/**
	 * Constructor
	 * 
	 * @param	string	$caption	
	 * @param	string	$name
	 * @param	int  	$groupid	    Group to select users from
	 * @param	mixed	$value	    	Pre-selected value (or array of them).
	 * @param	int		$size	        Number or rows. "1" makes a drop-down-list.
     * @param	bool    $multiple       Allow multiple selections?
     * @param   bool    $add_empty      Add an empty option?
	 */
	function XoopsFormSelectUserFromGroup($caption, $name, $groupid, $value=null, $size=1, $multiple=false, $add_empty = false)
	{
	    $this->XoopsFormSelect($caption, $name, $value, $size, $multiple);
		$user_handler =& xoops_gethandler('member');
		$uids = $user_handler->getUsersByGroup($groupid, false);
		
		$criteria = new Criteria('uid', "(".implode(',', $uids).")", 'IN');
		$criteria->setSort('uname');
		$users =& $user_handler->getUserList($criteria);
		if ($add_empty) {
		    $this->addOption(0, " -- ");
		}
		$this->addOptionArray($users);
	}
}
?>