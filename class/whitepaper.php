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
 * Make sure object handler is included
 */
if (!class_exists("CordPersistableObjectHandler")) {
    include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/object.php");
}
/**
 * @package Cordemanon
 * @subpackage Objects
 */
class CordemanonWhitepaper extends CordemanonObject {
    function CordemanonWhitepaper() {
        //descriptive properties
        $this->initVar('whitepaperid', XOBJ_DTYPE_INT);
        $this->initVar('whitepaper_title', XOBJ_DTYPE_TXTBOX);
        $this->initVar('whitepaper_shortdesc', XOBJ_DTYPE_TXTAREA);
        $this->initVar('whitepaper_desc', XOBJ_DTYPE_TXTAREA);
        $this->initVar('whitepaper_pages', XOBJ_DTYPE_INT);
        $this->initVar('whitepaper_language', XOBJ_DTYPE_TXTBOX);
        //functional properties
        $this->initVar('whitepaper_active', XOBJ_DTYPE_INT,0);
        $this->initVar('whitepaper_date', XOBJ_DTYPE_INT, time());
        $this->initVar('whitepaper_publishdate', XOBJ_DTYPE_INT, time());
        $this->initVar('whitepaper_expiredate', XOBJ_DTYPE_INT, time());
        $this->initVar('whitepaper_level', XOBJ_DTYPE_INT); // 1 = need login only, 2 = contact info required
        //foreign key references
        $this->initVar('customerid', XOBJ_DTYPE_INT);
        //statistics summarising
        $this->initVar('whitepaper_reads', XOBJ_DTYPE_INT);
        $this->initVar('whitepaper_hits', XOBJ_DTYPE_INT);
    }

    /**
	 * Get categories for a whitepaper
	 *
	 * @param bool $only_ids Whether to only return the ids of the categories
	 * @return array
	 */
    function getCategories($only_ids = true) {
        $ret = array();
        if ($this->isNew()) {
            return $ret;
        }
        $papercat_handler = xoops_getmodulehandler('papercat', 'cordemanon');
        $papercats = $papercat_handler->getObjects(new Criteria('whitepaperid', $this->getVar('whitepaperid')));
        if (count($papercats) == 0) {
            return $ret;
        }
        foreach (array_keys($papercats) as $i) {
            $ret[] = $papercats[$i]->getVar('categoryid');
        }
        if ($only_ids) {
            return $ret;
        }
        $category_handler = xoops_getmodulehandler('category', 'cordemanon');
        $criteria = new Criteria('categoryid', "(".implode(',', $ret).")", "IN");
        $criteria->setSort("category_name");
        return $category_handler->getObjects($criteria);
    }

    /**
	 * Register a download
	 * 
	 * @return bool
	 */
    function registerHit() {
        if (!$this->checkDownloadData()) {
            return false;
        }
        
        global $xoopsUser;
        $uid = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
        $hit_handler =& xoops_getmodulehandler('hit', 'cordemanon');
        $hit =& $hit_handler->create();
        $hit->setVar('hit_ip', ip2long($_SERVER['REMOTE_ADDR']));
        $hit->setVar('hit_time', time());
        $hit->setVar('userid', $uid);
        $hit->setVar('whitepaperid', $this->getVar('whitepaperid'));

        if ($this->getVar('whitepaper_level') == 2) {
            // extra information
            $hit->setVar('hit_name', $_REQUEST['name']);
            $hit->setVar('hit_address', $_REQUEST['address']);
            $hit->setVar('hit_postal', $_REQUEST['postal']);
            $hit->setVar('hit_city', $_REQUEST['city']);
            $hit->setVar('hit_phone', $_REQUEST['phone']);
            $hit->setVar('hit_email', $_REQUEST['email']);
        }
        if ($hit_handler->insert($hit) ) {
            $paper_handler =& xoops_getmodulehandler('whitepaper', 'cordemanon');
            return $paper_handler->addHit($this);
        }
        return false;
    }

    /**
	 * Get form for download info/disclaimer
	 *
	 * @return XoopsForm
	 */
    function getDownloadForm() {
        $action = XOOPS_URL."/whitepapers/".$this->getVar('whitepaperid')."/download";
        $title = _CORD_MA_DOWNLOADFORM;
        switch ($this->getVar('whitepaper_level')) {
            case 1:
                include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/forms/downloadlogin.php");
                $formclass = "DownloadFormLogin";
                break;

            case 2:
                include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/forms/downloadcontact.php");
                $formclass = "DownloadFormContact";
                break;
        }
        $form = new $formclass($title, 'downloadform', $action);
        $form->target =& $this;
        $form->createElements();
        return $form;
    }

    /**
	 * Is the whitepaper accessible to visitors
	 *
	 * @return bool
	 */
    function isActive() {
        $now = time();
        return ($this->getVar('whitepaper_active') == 1 && $now >= $this->getVar('whitepaper_publishdate') && $now <= $this->getVar('whitepaper_expiredate'));
    }

    /**
	 * Get path to downloadable file
	 *
	 * @return string
	 */
    function getFilePath() {
        $upload_path = $this->getUploadPath();
        return $upload_path."file".$this->getVar('whitepaperid').".pdf";
    }
    
    /**
     * Get path to upload directory
     *
     * @return string
     */
    function getUploadPath() {
        global $xoopsModuleConfig;
        if (strrpos($xoopsModuleConfig['upload_path'],"/") !== strlen($xoopsModuleConfig['upload_path'])-1) {
            $xoopsModuleConfig['upload_path'] .= "/";
        }
        return $xoopsModuleConfig['upload_path'];
    }

    /**
	 * Get "People Who Downloaded This Also Downloaded These" list
	 *
	 * @param bool $as_object whether to return objects or arrays
	 * 
	 * @return array
	 */
    function getPWDTADT($as_object = true) {
        $hit_handler =& xoops_getmodulehandler('hit', 'cordemanon');
        return $hit_handler->getOthersByWhitepaper($this, $as_object);
    }
    
    /**
     * Check whether submitted data are correctly filled
     * @todo Move to form classes
     *
     * @return bool
     */
    function checkDownloadData() {
        $check = true;
        if (!isset($_REQUEST['disclaimer']) || $_REQUEST['disclaimer'] != 1) {
            $this->setErrors(_CORD_MA_ERR_DISCLAIMER);
            $check = false;
        }
        if ($this->getVar('whitepaper_level') == 1) {
            return $check;
        }
        if (!isset($_REQUEST['name']) || $_REQUEST['name'] == "") {
            $this->setErrors(_CORD_MA_ERR_NAMENOTSET);
            $check = false;
        }
        if (!isset($_REQUEST['address']) || $_REQUEST['address'] == "") {
            $this->setErrors(_CORD_MA_ERR_ADDRESSNOTSET);
            $check = false;
        }
        if (!isset($_REQUEST['postal']) || $_REQUEST['postal'] == "") {
            $this->setErrors(_CORD_MA_ERR_POSTALNOTSET);
            $check = false;
        }
        if (!isset($_REQUEST['city']) || $_REQUEST['city'] == "") {
            $this->setErrors(_CORD_MA_ERR_CITYNOTSET);
            $check = false;
        }
        if (!isset($_REQUEST['email']) || $_REQUEST['email'] == "" || !checkEmail($_REQUEST['email'])) {
            $this->setErrors(_CORD_MA_ERR_EMAILINVALID);
            $check = false;
        }
        return $check;
    }
}
/**
 * @package Cordemanon
 * @subpackage Objects
 */
class CordemanonWhitepaperHandler extends CordPersistableObjectHandler {
    function CordemanonWhitepaperHandler($db) {
        $this->CordPersistableObjectHandler($db, 'cord_whitepaper', 'CordemanonWhitepaper', 'whitepaperid', 'whitepaper_title');
    }

    /**
	 * @todo Implement this
	 *
	 * @param int $limit       number of items to fetch
	 * @param int $sortby      what to sort by (0=latest, 1=most downloaded)
	 * @param int $daysback    number of days to go back either for downloads (if sortby = most downloaded)
	 * 
	 * @return array
	 */
    function getWhitepapers($limit=10, $sortby=0, $daysback=0, $as_object = false) {
        switch ($sortby) {
            default:
            case 0:
            case 2:
                $sort = $sortby == 0 ? "whitepaper_date" : "whitepaper_publishdate";
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('whitepaper_active', 1));
                $criteria->add(new Criteria('whitepaper_publishdate', time(), '<='));
                $criteria->add(new Criteria('whitepaper_expiredate', time(), '>='));
                $criteria->setSort($sort);
                $criteria->setOrder('DESC');
                $criteria->setLimit($limit);
                $whitepapers =& $this->getObjects($criteria, false, $as_object);
                break;

            case 1:
                $hit_handler =& xoops_getmodulehandler('hit', 'cordemanon');
                $sql = "SELECT w.*, count(*) AS hits FROM ".$this->table." w, ".$hit_handler->table." h
	                     WHERE w.whitepaperid=h.whitepaperid 
	                       AND whitepaper_active = 1
	                       AND whitepaper_publishdate <= ".time()."
	                       AND whitepaper_expiredate >= ".time()."
	                       AND hit_time >= ".(time()-($daysback*3600*24))."
	                       GROUP BY w.whitepaperid
	                       ORDER BY hits DESC";
                $result = $this->db->query($sql, $limit);
                if (!$result) {
                    $whitepapers = array();
                }
                else {
                    $whitepapers =& $this->convertResultSet($result, false, $as_object);
                }
                break;
        }
        return $whitepapers;
    }

    /**
     * Get whitepapers in a category
     *
     * @param CordemanonCategory $category
     * @param int $limit
     * 
     * @return array
     */
    function getByCategory($category, $limit) {
        // Get top categories
        $category_handler = xoops_getmodulehandler('category', 'cordemanon');
        $allcats = $category_handler->getObjects(null, true, false);

        // Create tree relationship
        include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/arraytree.php");
        $tree = new ArrayTree($allcats, 'categoryid', 'category_parent');

        // Get whitepapers from this category and subcategories
        $allsubcats = $tree->getAllChild($category->getVar('categoryid'));

        // Find category ids to look in
        $catids = array_keys($allsubcats);
        $catids[] = $category->getVar('categoryid');

        // Get whitepaper ids to choose from
        $papercat_handler = xoops_getmodulehandler('papercat');
        $whitepapercatlinks = $papercat_handler->getObjects(new Criteria('categoryid', "(".implode(',', $catids).")", "IN"));
        foreach (array_keys($whitepapercatlinks) as $i) {
            $whitepaperids[] = $whitepapercatlinks[$i]->getVar('whitepaperid');
        }

        // Get whitepapers
        $criteria = new CriteriaCompo(new Criteria('whitepaperid', "(".implode(',', $whitepaperids).")", "IN"));
        $criteria->add(new Criteria('whitepaper_active', 1));
        $criteria->add(new Criteria('whitepaper_publishdate', time(), '<='));
        $criteria->add(new Criteria('whitepaper_expiredate', time(), '>='));
        $criteria->setLimit($limit);
        $criteria->setSort('whitepaper_publishdate');
        $criteria->setOrder('DESC');
        return $this->getObjects($criteria, true, true);
    }
    
    /**
     * Get whitepapers for a customer
     *
     * @param CordemanonCustomer $customer
     * @param int $limit
     * 
     * @return array
     */
    function getByCustomer($customer, $limit) {
        // Get whitepapers
        $criteria = new CriteriaCompo(new Criteria('customerid', $customer->getVar('customerid')));
        $criteria->add(new Criteria('whitepaper_active', 1));
        $criteria->add(new Criteria('whitepaper_publishdate', time(), '<='));
        $criteria->add(new Criteria('whitepaper_expiredate', time(), '>='));
        $criteria->setLimit($limit);
        $criteria->setSort('whitepaper_publishdate');
        $criteria->setOrder('DESC');
        return $this->getObjects($criteria, true, true);
    }

    /**
     * delete an object from the database
     *
     * @param object $obj reference to the object to delete
     * @param bool $force
     * 
     * @return bool FALSE if failed.
     */
    function delete(&$obj, $force = false) {
        if (parent::delete($obj, $force)) {
            @unlink($obj->getFilePath());
            $papercat_handler = xoops_getmodulehandler('papercat', 'cordemanon');
            return $papercat_handler->deleteAll(new Criteria('whitepaperid', $this->target->getVar('whitepaperid')));
        }
        return false;
    }

    /**
	 * Add a hit to the hit counter for a whitepaper
	 *
	 * @param CordemanonWhitepaper $whitepaper
	 * @param bool $force
	 * @return bool
	 */
    function addHit($whitepaper, $force = false) {
        $sql = "UPDATE ".$this->table." SET whitepaper_hits = whitepaper_hits + 1 WHERE whitepaperid = ".$whitepaper->getVar('whitepaperid');
        if ($force) {
            return $this->db->queryF($sql);
        }
        return $this->db->query($sql);
    }

    /**
	 * Add a read to the read counter for a whitepaper
	 *
	 * @param CordemanonWhitepaper $whitepaper
	 * @param bool $force
	 * @return bool
	 */
    function addRead($whitepaper, $force = false) {
        $sql = "UPDATE ".$this->table." SET whitepaper_reads = whitepaper_reads + 1 WHERE whitepaperid = ".$whitepaper->getVar('whitepaperid');
        if ($force) {
            return $this->db->queryF($sql);
        }
        return $this->db->query($sql);
    }
    
    /**
     * Search whitepaper titles, short descriptions and descriptions for one or more terms
     *
     * @param array $query
     * @param string $andor
     * @param int $offset
     * @param int $limit
     * @param int $uid
     * 
     * @return array
     */
    function search($query, $andor = "AND", $offset = 0, $limit = 0, $uid = 0) {
        $ret = array();
        $sql = "SELECT * FROM ".$this->table." 
                    WHERE whitepaper_active = 1 
                    AND whitepaper_publishdate < ".time()." 
                    AND whitepaper_expiredate > ".time();
        if ($uid > 0) {
            $customer_handler =& xoops_getmodulehandler('customer', 'cordemanon');
            $customer = $customer_handler->getByUid($uid);
            if (is_object($customer)) {
                $sql .= " AND customerid=".$customer->getVar('customerid');
            }
        }
        if ( is_array($query) && $count = count($query) ) {
            $sql .= " AND (CONCAT(whitepaper_title, whitepaper_shortdesc, whitepaper_desc) LIKE '%". $query[0]."%'";
            for ($i = 1; $i < $count; $i++) {
                $sql .= " ".$andor." CONCAT(whitepaper_title, whitepaper_shortdesc, whitepaper_desc) LIKE '%". $query[$i]."%'";
            }
            $sql.= ")";
        }
        $result = $this->db->query($sql);
        $ret = $this->convertResultSet($result);
        return $ret;
    }
}
?>