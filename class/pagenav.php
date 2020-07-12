<?php
// $Id: pagenav.php $
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
 * Make sure object handler is included
 */
if (!class_exists("XoopsPageNav")) {
	include_once(XOOPS_ROOT_PATH."/class/pagenav.php");
}
/**
 * @package Cordemanon
 */

class CordemanonPageNav extends XoopsPageNav {
    
    function CordemanonPageNav($total_items, $items_perpage, $current_start, $start_name="start", $extra_arg="") {
        parent::XoopsPageNav($total_items, $items_perpage, $current_start, $start_name, $extra_arg);
        if ( $extra_arg != '' && ( substr($extra_arg, -5) != '&amp;' || substr($extra_arg, -1) != '&' ) ) {
			$extra_arg .= '&amp;';
		}
		$this->url = $_SERVER['SCRIPT_URL'].'?'.$extra_arg.trim($start_name).'=';
    }
    /**
     * Return HTML for a pagenav to be used in Cordemanon category listings
     *
     * @param int $offset
     *
     * @return string
     */
    function renderNav($offset = 4) {
        $ret = '';
        if ( $this->total <= $this->perpage ) {
            return $ret;
        }
        $total_pages = ceil($this->total / $this->perpage);
        if ( $total_pages > 1 ) {
            $ret .= _CORD_MA_PAGE." ";
            $prev = $this->current - $this->perpage;
            if ( $prev >= 0 ) {
                //$ret .= '<a href="'.$this->url.$prev.'">'. _CORD_MA_PREVIOUS .'</a> ';
            }
            $counter = 1;
            $current_page = intval(floor(($this->current + $this->perpage) / $this->perpage));
            while ( $counter <= $total_pages ) {
                if ( $counter == $current_page ) {
                    $ret .= '<b>'.$counter.'</b> ';
                } elseif ( ($counter > $current_page-$offset && $counter < $current_page + $offset ) || $counter == 1 || $counter == $total_pages ) {
                    if ( $counter == $total_pages && $current_page < $total_pages - $offset ) {
                        $ret .= '... ';
                    }
                    $ret .= '<a href="'.$this->url.(($counter) * $this->perpage).'">'.$counter.'</a> ';
                    if ( $counter == 1 && $current_page > 1 + $offset ) {
                        $ret .= '... ';
                    }
                }
                $counter++;
            }
            $next = $this->current + $this->perpage;
            if ( $this->total > $next ) {
                $next++;
                $ret .= '<a href="'.$this->url.$next.'">'. _CORD_MA_NEXT .'</a> ';
            }
        }
        return $ret;
    }
}
?>