<?php
// $Id: download.php $
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

include "../../mainfile.php";

if (!isset($_REQUEST['whitepaperid'])) {
    redirect_header(XOOPS_URL."/whitepapers", 3, _CORD_MA_NOWHITEPAPERSELECTED);
}

$paper_handler =& xoops_getmodulehandler('whitepaper');
$whitepaper =& $paper_handler->get($_REQUEST['whitepaperid']);

if (!$whitepaper->isActive()) {
    redirect_header(XOOPS_URL."/whitepapers/".$whitepaper->getVar('whitepaperid'), 3, _CORD_MA_WHITEPAPERINACTIVE);
}

if (!$xoopsUser) {
    redirect_header(XOOPS_URL."/user.php", 3, _CORD_MA_NEEDLOGINTODOWNLOAD);
}

if (isset($_REQUEST['submit']) && $whitepaper->registerHit()) {
    $filepath = $whitepaper->getFilePath();

    $mime = "application/pdf";
    $content_disposition = "attachment";

    header("Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime($filepath))." GMT");
    header("Content-Type: ".$mime);
    header('Content-Disposition: '.$content_disposition.'; filename="'.str_replace(" ","_",$whitepaper->getVar('whitepaper_title', 'n')).'"');
    echo file_get_contents($filepath);
    exit;
} else {
    $xoopsOption['template_main'] = "cord_whitepaper_download.html";
    include_once XOOPS_ROOT_PATH."/header.php";
    
    // Meta and page title
    $xoopsTpl->assign('pagetitle', $whitepaper->getVar('whitepaper_title', 'n'));
    $xoopsTpl->assign('xoops_page_title', $whitepaper->getVar('whitepaper_title', 'n'));

    
    // show download form/disclaimer
    $form = $whitepaper->getDownloadForm();
    $form->assign($xoopsTpl);
    
    $xoopsTpl->assign('whitepaper', $whitepaper->toArray());

    $xoopsTpl->assign('errors', $whitepaper->getErrors());
}
include XOOPS_ROOT_PATH."/footer.php";
?>