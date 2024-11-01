<?php
/*
Plugin Name: Simple Changed Files
Plugin URI: http://devondev.com/simple-changed-files/
Description: Allows administrators to view a list of files changed after a given time or within a time range. 
Version: 1.1
Author: Peter Wooster
Author URI: http://www.devondev.com/
*/

/*  Copyright (C) 2011-2013 Devondev Inc.  (http://devondev.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * pull in the controller class
 * 
 * This plugin uses the Model-View-Controller pattern to illustrate how to write 
 * an object oriented plugin.  It's overkill for something this small but it does 
 * provide a nice simple example.
 * 
 * Model View Controller splits the code into three parts
 * - Model - the data and access to it, files
 * - View - the display, forms, tables, html
 * - Controller - control logic and business logic, actions and decisions
 */

require_once 'classes/SimpleChangedFilesController.php';

/**
 * register the plugin with the WordPress admin system
 * by calling a static function on the controller class
 */

SimpleChangedFilesController::register();


/* =========================================================================
 * end of program, php close tag intentionally omitted
 * ========================================================================= */
