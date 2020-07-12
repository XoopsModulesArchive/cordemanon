<?php
// $Id: xoops_version.php $
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

$modversion['name'] = _CORD_MI_NAME;
$modversion['version'] = 1.01;
$modversion['description'] = _CORD_MI_DESC;
$modversion['author'] = "Jan Pedersen (Mithrandir)";
$modversion['credits'] = "<a href='http://www.cusix.dk/'>Cusix Software</a>, <a href='http://fyens.dk'>Fyens Stiftstidende</a> for the excellent util module snippet for category administration";
$modversion['help'] = "";
$modversion['license'] = "<a href='http://www.gnu.org/copyleft/gpl.html' target='_blank'>Full Legal Code</a>";
$modversion['official'] = 0;
$modversion['image'] = "images/cordemanon.jpg";
$modversion['dirname'] = "cordemanon";

// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][] = "cord_category";
$modversion['tables'][] = "cord_customer";
$modversion['tables'][] = "cord_hit";
$modversion['tables'][] = "cord_keywordcategory";
$modversion['tables'][] = "cord_keywordpaper";
$modversion['tables'][] = "cord_papercat";
$modversion['tables'][] = "cord_whitepaper";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/whitepaper.php";
$modversion['adminmenu'] = "admin/menu.php";

// Menu -- content in main menu block
$modversion['hasMain'] = 1;

$modversion['css'] = "module.css";

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "include/search.inc.php";
$modversion['search']['func'] = "cordemanon_search";

// Templates
$modversion['templates'][] = array('file' => 'cord_index.html',
								 'description' => "Front page");
$modversion['templates'][] = array('file' => 'cord_category.html',
								 'description' => "Category page");
$modversion['templates'][] = array('file' => 'cord_whitepaper.html',
								 'description' => "Whitepaper details page");
$modversion['templates'][] = array('file' => 'cord_customer.html',
								 'description' => "Customer whitepaper list");
$modversion['templates'][] = array('file' => 'cord_whitepaperlist.html',
								 'description' => "Whitepaper list");
$modversion['templates'][] = array('file' => 'cord_whitepaper_download.html',
								 'description' => "Whitepaper download form");
$modversion['templates'][] = array('file' => 'cord_rss.html',
								 'description' => "RSS Feed template");

$modversion['templates'][] = array('file' => 'cord_admin_whitepaperlist.html',
                                   'description' => "Admin whitepaper list");
$modversion['templates'][] = array('file' => 'cord_admin_customerlist.html',
                                   'description' => "Admin customer list");
$modversion['templates'][] = array('file' => 'cord_admin_categorylist.html',
                                   'description' => "Admin category list");
$modversion['templates'][] = array('file' => 'cord_admin_statistics.html',
                                   'description' => "Statistics for a whitepaper");
// Blocks
$modversion['blocks'][1] = array('file' => "categories.php",
							  'name' => _CORD_MI_B_CATEGORIES,
							  'description' => "Category list",
							  'show_func' => "b_cord_categories",
							  'template' => "cord_b_categories.html");
$modversion['blocks'][2] = array('file' => "whitepapers.php",
							  'name' => _CORD_MI_B_LATEST,
							  'description' => "Latest whitepapers",
							  'show_func' => "b_cord_whitepapers",
							  'edit_func' => "b_cord_whitepapers_edit",
							  'options' => "10|0|0",
							  'template' => "cord_b_whitepapers.html");
$modversion['blocks'][3] = array('file' => "whitepapers.php",
							  'name' => _CORD_MI_B_POPULAR,
							  'description' => "Popular whitepapers",
							  'show_func' => "b_cord_whitepapers",
							  'edit_func' => "b_cord_whitepapers_edit",
							  'options' => "10|1|14",
							  'template' => "cord_b_whitepapers.html");
$modversion['blocks'][4] = array('file' => "customers.php",
							  'name' => _CORD_MI_B_CUSTOMERS,
							  'description' => "List customers",
							  'show_func' => "b_cord_customers",
							  'template' => "cord_b_customers.html");
$modversion['blocks'][5] = array('file' => "customerwhitepapers.php",
							  'name' => _CORD_MI_B_CUSTOMERPAPERS,
							  'description' => "Customer's whitepapers",
							  'show_func' => "b_cord_customerwhitepapers",
							  'edit_func' => "b_cord_customerwhitepapers_edit",
							  'options' => "10",
							  'template' => "cord_b_whitepapers.html");

//	Module Configs
// languages
$modversion['config'][] = array('name' => 'languages',
                                 'title' => '_CORD_MI_LANGUAGES',
                                 'description' => '_CORD_MI_LANGUAGES_DESC',
                                 'formtype' => 'textarea',
                                 'valuetype' => 'text',
                                 'default' => 'dansk,engelsk');
// sort-by in categories
$sortby_options = array('_CORD_MI_SORTBY_DATE' => 1, '_CORD_MI_SORTBY_PUBLISH' => 2);
$modversion['config'][] = array('name' => 'sortby',
                                 'title' => '_CORD_MI_SORTBY',
                                 'description' => '_CORD_MI_SORTBY_DESC',
                                 'formtype' => 'select',
                                 'valuetype' => 'int',
                                 'options' => $sortby_options,
                                 'default' => '1');
                                 
// category_limit
$modversion['config'][] = array('name' => 'category_limit',
                                'title' => '_CORD_MI_CATEGORY_LIMIT',
                                'description' => '_CORD_MI_CATEGORY_LIMIT_DESC',
                                'formtype' => 'select',
                                'valuetype' => 'int',
                                'options' => array('3' => 3, '5' => 5, '10' => 10, '15' => 15, '20' => 20, '50' => 50),
                                'default' => 15);
// admin_groups
$modversion['config'][] = array('name' => 'admin_groups',
                                 'title' => '_CORD_MI_ADMGROUPS',
                                 'description' => '_CORD_MI_ADMGROUPS_DESC',
                                 'formtype' => 'group_multi',
                                 'valuetype' => 'array',
                                 'default' => array(XOOPS_GROUP_ADMIN));

$modversion['config'][] = array('name' => 'customer_group',
                                 'title' => '_CORD_MI_CUSTGROUP',
                                 'description' => '_CORD_MI_CUSTGROUP_DESC',
                                 'formtype' => 'group',
                                 'valuetype' => 'int',
                                 'default' => '');
// frontpage_text
$modversion['config'][] = array('name' => 'frontpage_text',
                                 'title' => '_CORD_MI_FRONT_TEXT',
                                 'description' => '_CORD_MI_FRONT_TEXT_DESC',
                                 'formtype' => 'textarea',
                                 'valuetype' => 'text',
                                 'default' => '');
                                 
$modversion['config'][] = array('name' => 'disclaimer',
                                'title' => '_CORD_MI_DISCLAIMER',
                                'description' => '_CORD_MI_DISCLAIMER_DESC',
                                'formtype' => 'textarea',
                                'valuetype' => 'text',
                                'default' => _CORD_MI_DISCLAIMER_TEXT);
// upload_path
$upload_arr = explode("/", XOOPS_ROOT_PATH);
$last_dir = $upload_arr[count($upload_arr)-1]; //find last element
array_pop($upload_arr); //pop last element
if ($last_dir == "") {
    array_pop($upload_arr); //trailing slash, pop another one
}
$upload_path = implode("/", $upload_arr);
$upload_path .= "/".$modversion['dirname']."/";
// $upload_path should now have the value of one step up from XOOPS_ROOT_PATH plus cordemanon directory
$modversion['config'][] = array('name' => 'upload_path',
                                 'title' => '_CORD_MI_UPLOADPATH',
                                 'description' => '_CORD_MI_UPLOADPATH_DESC',
                                 'formtype' => 'textbox',
                                 'valuetype' => 'text',
                                 'default' => $upload_path);
// max_file_size
$modversion['config'][] = array('name' => 'max_file_size',
                                 'title' => '_CORD_MI_MAXFILE',
                                 'description' => '_CORD_MI_MAXFILE_DESC',
                                 'formtype' => 'textbox',
                                 'valuetype' => 'int',
                                 'default' => (ini_get('upload_max_filesize')/1024) );
?>