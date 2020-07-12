<?php
// $Id: index.php $
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

$xoopsOption['template_main'] = "cord_index.html";
include XOOPS_ROOT_PATH."/header.php";

// Get top categories
$category_handler = xoops_getmodulehandler('category');
$allcats = $category_handler->getObjects(null, true, false);

// Create tree relationship
include_once(XOOPS_ROOT_PATH."/modules/cordemanon/class/arraytree.php");
$tree = new ArrayTree($allcats, 'categoryid', 'category_parent');

// Get whitepaper count per category
$papercounts = $category_handler->getCountByCategory();
// Add counts to tree elements
$category_handler->assignCounts($tree, $papercounts);

// Filter out empty categories
$topcats = $tree->getFirstChild(0);
foreach (array_keys($topcats) as $i) {
	if ($topcats[$i]['count'] > 0) {
		$categories[$i] = $topcats[$i];
		$keywords[] = $topcats[$i]['category_name'];
		$subcats = $tree->getFirstChild($i);
		foreach (array_keys($subcats) as $k) {
			// Only add subcats with whitepapers
			if ($subcats[$k]['count'] > 0) {
				$categories[$i]['subcats'][$k] = $subcats[$k];
				$keywords[] = $subcats[$k]['category_name'];
			}
		}
	}
}

// Meta keywords and page title
$xoopsTpl->assign('xoops_meta_description', $xoopsModuleConfig['frontpage_text']);
$xoopsTpl->assign('xoops_meta_keywords', implode(',', $keywords));

$myts =& MyTextSanitizer::getInstance();
// Assign categories to template - one sublevel to each top category
$xoopsTpl->assign('categories', $categories);
$xoopsTpl->assign('description_text', $myts->displayTarea($xoopsModuleConfig['frontpage_text']));
include XOOPS_ROOT_PATH."/footer.php";
?>