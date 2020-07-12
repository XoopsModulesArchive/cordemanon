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
include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/processors/processor.php");
/**
 * @package Cordemanon
 * @subpackage Processor
 */
class WhitepaperProcessor extends CordemanonProcessor {
    /**
     * Process form submissal
     * 
     * @return void
     *
     */
    function process() {
        $this->target->setVar('whitepaper_title', $_REQUEST['whitepaper_title']);
        $this->target->setVar('whitepaper_active', $_REQUEST['whitepaper_active']);
        $this->target->setVar('whitepaper_pages', $_REQUEST['whitepaper_pages']);
        $this->target->setVar('whitepaper_language', $_REQUEST['whitepaper_language']);
        $this->target->setVar('whitepaper_level', $_REQUEST['whitepaper_level']);
        if (!customerOnly()) {
            $this->target->setVar('customerid', $_REQUEST['customerid']);
        }
        else {
            $customer_handler =& xoops_getmodulehandler('customer');
            $customer =& $customer_handler->getByUid($GLOBALS['xoopsUser']->getVar('uid'));
            $this->target->setVar('customerid', $customer->getVar('customerid'));
        }
        $this->target->setVar('whitepaper_shortdesc', $_REQUEST['whitepaper_shortdesc']);
        $this->target->setVar('whitepaper_desc', $_REQUEST['whitepaper_desc']);
        
        $date = strtotime($_REQUEST['whitepaper_date']['date']) + ($_REQUEST['whitepaper_date']['hours'] * 3600) + ($_REQUEST['whitepaper_date']['minutes'] * 60);
        $publishdate = strtotime($_REQUEST['whitepaper_publishdate']['date']) + ($_REQUEST['whitepaper_publishdate']['hours'] * 3600) + ($_REQUEST['whitepaper_publishdate']['minutes'] * 60);
        $expiredate = strtotime($_REQUEST['whitepaper_expiredate']['date']) + ($_REQUEST['whitepaper_expiredate']['hours'] * 3600) + ($_REQUEST['whitepaper_expiredate']['minutes'] * 60);
        $this->target->setVar('whitepaper_date', $date);
        $this->target->setVar('whitepaper_publishdate', $publishdate);
        $this->target->setVar('whitepaper_expiredate', $expiredate);
    }
    
    /**
     * Following a save operation after a form submissal, this method is called to perform postsave operations
     * In this case it is to remove all categories from the whitepaper and add the ones selected in the form
     *
     * Then upload a new whitepaper file, if a new one was selected
     * 
     * @return bool
     */
    function postSave() {
        $papercat_handler = xoops_getmodulehandler('papercat', 'cordemanon');
        $papercat_handler->deleteAll(new Criteria('whitepaperid', $this->target->getVar('whitepaperid')));
        foreach ($_REQUEST['categories'] as $catid) {
            $papercat =& $papercat_handler->create();
            $papercat->setVar('whitepaperid', $this->target->getVar('whitepaperid'));
            $papercat->setVar('categoryid', $catid);
            $papercat_handler->insert($papercat);
            unset($papercat);
        }
        // Upload whitepaper if any
        if (is_uploaded_file($_FILES['whitepaper_file']['tmp_name'])) {
    		$filename = $this->uploadFile('whitepaper_file');
    		if ($filename === false) {
    			$this->target->setErrors("No file uploaded");
    			return false;
    		}
    	}
    	elseif (isset($_FILES['whitepaper_file']['name']) && $_FILES['whitepaper_file']['name'] != "") {
    	    switch ($_FILES['whitepaper_file']['error'])
            {
                case 0: // no error;
                    $this->target->setErrors('There was a problem with your upload. Error: 0');
                    break;
                case 1: // uploaded file exceeds the upload_max_filesize directive in php.ini
                    $this->target->setErrors('The file you are trying to upload is too big. Error: 1 (max '.ini_get('upload_max_filesize').'B)');

                    break;
                case 2: // uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                    $this->target->setErrors('The file you are trying to upload is too big. Error: 2');
                    break;
                case 3: // uploaded file was only partially uploaded
                    $this->target->setErrors('The file you are trying to upload was only partially uploaded. Error: 3');
                    break;
                case 4: // no file was uploaded
                    $this->target->setErrors('No file selected for upload. Error: 4');
                    break;
                default: // a default error, just in case!  :)
                    $this->target->setErrors('No file selected for upload. Error: 5');
                    break;
            }
            return false;
    	}
        return true;
    }
    
    /**
    * Finishes the upload following a XoopsFormFile element
    *
    * @param string $varname name of the variable used in the form
    * @param array $allowed_mimetypes
    */
    function uploadFile($varname = 'file', $allowed_mimetypes = array()) {
        global $xoopsModuleConfig, $xoopsModule;
	    include_once XOOPS_ROOT_PATH.'/class/uploader.php';

	    $maxfilesize = $xoopsModuleConfig['max_file_size']*1024;

	    if ($allowed_mimetypes == array()) {
	    	$allowed_mimetypes = array("image/bmp",
	                                   "image/gif",
	                                   "image/jpeg",
	                                   "image/png",
	                                   "image/tiff",
	                                   "image/tif",
	                                   "application/pdf");
	    }

	    $target_dir = $this->target->getUploadPath();
	    $uploader = new XoopsMediaUploader($target_dir, $allowed_mimetypes, $maxfilesize);

	    if ($uploader->fetchMedia($varname)) {
	        $ext= '.pdf';
	        // Name file "filex.ext"
	        $filename = "file".$this->target->getVar('whitepaperid').$ext;
	        $uploader->setTargetFileName($filename);
	        if (is_file($target_dir.$filename) && !is_dir($target_dir.$filename)) {
	            // file exists - remove it so it can be overwritten with new one
	            unlink($target_dir.$filename);
	        }
	        if (!$uploader->upload()) {
	            $this->target->setErrors("Upload Error<br />".$uploader->getErrors());
	            return false;
	        } else {
	            return $uploader->getSavedFileName();
	        }
	    } else {
	        $this->target->setErrors("FetchMedia Error: <br />".$uploader->getErrors());
	        return false;
	    }
	}
}
?>