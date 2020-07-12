<?php
// $Id: hit.php $
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
class CordemanonHit extends CordemanonObject {
	function CordemanonHit() {
		$this->initVar('hitid', XOBJ_DTYPE_INT);
		$this->initVar('hit_ip', XOBJ_DTYPE_INT);
		$this->initVar('hit_time', XOBJ_DTYPE_INT);
		$this->initVar('userid', XOBJ_DTYPE_INT);
		$this->initVar('whitepaperid', XOBJ_DTYPE_INT);
		
		// extra information
		$this->initVar('hit_name', XOBJ_DTYPE_TXTBOX, '');
		$this->initVar('hit_address', XOBJ_DTYPE_TXTBOX, '');
		$this->initVar('hit_postal', XOBJ_DTYPE_TXTBOX, '');
		$this->initVar('hit_city', XOBJ_DTYPE_TXTBOX, '');
		$this->initVar('hit_phone', XOBJ_DTYPE_TXTBOX, '');
		$this->initVar('hit_email', XOBJ_DTYPE_EMAIL, '');
	}
	
	/**
    * Returns an array representation of the object
    *
    * @return array
    */
	function toArray() {
	    $ret = parent::toArray();
	    $ret['hit_ip'] = long2ip($ret['hit_ip']);
	    return $ret;
	}
}

/**
 * @package Cordemanon
 * @subpackage Objects
 */
class CordemanonHitHandler extends CordPersistableObjectHandler {
	function CordemanonHitHandler($db) {
		$this->CordPersistableObjectHandler($db, 'cord_hit', 'CordemanonHit', 'hitid');
	}
	
	/**
	 * Get whitepapers that are downloaded by the same users who have 
	 * downloaded one particular whitepaper
	 *
	 * @param CordemanonWhitepaper $whitepaper
	 * @param bool $as_object whether to return as objects or arrays
	 * @param int $limit how many whitepapers to return
	 * 
	 * @return array
	 */
	function getOthersByWhitepaper($whitepaper, $as_object = true, $limit = 5) {
	    // Get last 100 users who have downloaded this whitepaper
	    $criteria = new Criteria('whitepaperid', $whitepaper->getVar('whitepaperid'));
	    $criteria->setSort('hit_time');
	    $criteria->setOrder('DESC');
	    $criteria->setLimit(100);
	    $hits = $this->getObjects($criteria);
	    unset($criteria);
	    if (count($hits) == 0) {
	        return array();
	    }
	    foreach (array_keys($hits) as $i) {
	        $uids[] = $hits[$i]->getVar('userid');
	    }
	    
	    // Get IDs of $limit whitepapers that these users have also downloaded
	    $timestamp = time() - (3600*24*60); //60 days back
	    $sql = "SELECT whitepaperid, count(*) AS hits FROM ".$this->table." WHERE 
	               whitepaperid != ".$whitepaper->getVar('whitepaperid')."
	               AND hit_time > ".$timestamp."
	               AND userid IN (".implode(',', $uids).")
	               GROUP BY whitepaperid ORDER BY hits DESC";
	    $result = $this->db->query($sql, $limit);
	    if (!$result || $this->db->getRowsNum($result) == 0) {
	        return array();
	    }
	    while (list($whitepaperid, $count) = $this->db->fetchRow($result) ) {
	        $whitepaperids[] = $whitepaperid;
	    }
	    // Get whitepaper objects from IDs
	    $paper_handler =& xoops_getmodulehandler('whitepaper', 'cordemanon');
	    $criteria = new Criteria('whitepaperid', "(".implode($whitepaperids).")", "IN");
	    $criteria->setSort('whitepaper_date');
	    $criteria->setOrder('DESC');
	    return $paper_handler->getObjects($criteria, true, $as_object);
	}
	
	/**
	 * Get hit statistics for a given whitepaper
	 *
	 * @param int $whitepaperid
	 * @return array
	 */
	function getStats($whitepaperid) {
	    $ret = array();
	    $sql = "SELECT MONTH(FROM_UNIXTIME(hit_time)) AS `month`, YEAR(FROM_UNIXTIME(hit_time)) as `year`, COUNT(*) as `count` FROM ".$this->table." 
	            WHERE whitepaperid=".intval($whitepaperid)." 
	            GROUP BY MONTH(FROM_UNIXTIME(hit_time)), YEAR(FROM_UNIXTIME(hit_time))";
	    $result = $this->db->query($sql);
	    if (!$result) {
	        echo $this->db->error();
	        return $ret;
	    }
	    while ($row = $this->db->fetchArray($result)) {
	        $ret[] = $row;
	    }
	    return $ret;
	}
}
?>