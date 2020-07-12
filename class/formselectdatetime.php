<?php
// $Id: formselectdatetime.php,v 1.1 2005/11/08 11:17:22 jkp Exp $
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
 * @package Cordemanon
 */

/**
 * Parent
 */
include_once XOOPS_ROOT_PATH."/class/xoopsform/formelementtray.php";
/**
 * A select field with a choice of available users
 *
 * @package     Cordemanon
 * @subpackage  Form
 *
 * @author	    Jan Keller Pedersen	<mithrandir@xoops.org>
 * @copyright	copyright (c) 2006 cusix.dk
 */
class CordemanonSelectDateTime extends XoopsFormElementTray 
{
	/**
	 * Constructor
	 *
	 * @param	string	$caption
	 * @param	string	$name
	 * @param	int		$size	        Size of text field
	 * @param	int  	$value	    	Pre-selected value as UNIX TIMESTAMP

	 */
	function CordemanonSelectDateTime($caption, $name, $size = 15, $value=0)
	{
	    $this->XoopsFormElementTray($caption, '&nbsp;', $name);
	    //Date
		$value = intval($value);
		$this->addElement(new XoopsFormTextDateSelect('', $name.'[date]', $size, $value));
		
		//Hours
		$datetime = $value > 0 ? getdate($value) : getdate(time());
		$hourselect = new XoopsFormSelect('', $name.'[hours]', $datetime['hours']);
		for ($i = 0; $i < 24; $i++) {
			$hourselect->addOption($i);
		}
		$this->addElement($hourselect);
		//Minutes
		$minuteselect = new XoopsFormSelect('', $name.'[minutes]', $datetime['minutes']);
		for ($j = 0; $j < 60; $j++) {
			$minuteselect->addOption(sprintf("%02d",$j));
		}
		$this->addElement($minuteselect);
	}
}
?>