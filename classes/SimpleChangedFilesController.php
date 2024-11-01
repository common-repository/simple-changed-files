<?php

/**
 * SimpleChangedFilesController
 * the controller class for SimpleChangedFiles
 *
 * @author peter wooster 
 */

/*  Copyright (C) 2011 Devondev Inc.  (http://devondev.com)

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
 * pull in the code for the model and view
 */
require_once 'SimpleChangedFilesView.php';
require_once 'SimpleChangedFilesModel.php';

/**
 * The controller class.
 * This class integrates with the WordPress admin system, takes input,
 * and dispatches functions to get data and display the page. 
 * It does NOT contain any file system code or HTML.
 * The files ystem code is in the model
 * The HTML is in the view 
 */

    class SimpleChangedFilesController {
/** ========================= instance variables ============================== */
        private $errors;
        private $lastError;
        private $et;
        private $st;
        private $et_error;
        private $st_error;

/** ========================== WordPress Integration ========================== */        
/* static functions that link into the wordpress admin system
 * set up actions to link into the admin tools menu 
 * The callback functions must be static and require the class name and method name
 */

/**
 * register the plugin by adding an action to the admin system
 */        
        public static function register() {
            add_action('admin_menu', array(__CLASS__, 'add_menu'));
        }
        
/**
 * add a menu item to the tools (management) menu
 */        
        static function add_menu() {
            add_management_page( 'Simple Changed Files', 'Simple Changed Files', 'manage_options', 'scf_tool_page', array(__CLASS__, 'tool_page'));
        }

/**
 * the function that WordPress calls to display the page
 * we get am instance of this class and call the control method
 */        
        static function tool_page() {
            $scf = new SimpleChangedFilesController;
            $scf->control();
        }

/** ========================== The Controller ================================= */        
        
/**
 * The main method of this class,
 * - set the PHP timezone to match the WordPress timezone 
 * - get the range from the Request,
 * - call the view to display the form
 * - call the model to get the list
 * - call the view to display the list
 */
        function control() {
            if (!current_user_can('manage_options'))
            {
              wp_die( __('The current user does not have sufficient permissions to access this page.') );
            }
            
            $this->now = strtotime('now');
            // we set the PHP timezone based on the WordPress gmt_offset
            // this makes all the date calculations much easier
            $gofs = get_option( 'gmt_offset' );
            $tz = date_default_timezone_get();
            date_default_timezone_set('Etc/GMT'.(($gofs < 0)?'+':'').-$gofs);
            
            // get the time range
            $this->getRange();
            
            // display the form
            $view = new SimpleChangedFilesView;
            $view->showForm($this->start, $this->end, $this->errors);
            
            // display the result table if requested and no errors
            if(!$this->errors && $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submit'] == 'Run') {
                $model = new SimplechangedFilesModel;
                $files = $model->doScan($this->start, $this->end);
                if($files) {
                    $view->showFiles($files);
                }
            }
            // put the timezone back
            date_default_timezone_set($tz);
        } // end control
        
/**
 * get the time range from the form parameters
 */        
        function getRange() {
            // validate the user input
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->start = $this->validate_ts('scf_start_time', true);
                $this->end = $this->validate_ts('scf_end_time');
            } else {
                $this->start = array('ts' =>false, 'error' => '', 'input' => '');
                $this->end = $this->start;
            }
            
            // ensure that time moves forward
            if(0 == $this->errors) {
                $e = $this->end['ts'];
                if(!$e)$e = $this->now;
                if($this->start['ts'] > $e){
                    $this->start['error'] = 'must be less than end time';
                    $this->errors++;
                }
            }
        } // end getRange
        
/**
 * validate the provided parameter as a timestamp (date and optional time)
 * @param type $param the request parameter name
 * @param type $default the default to use if not provided, if null a value is required
 * @return type an array containing the input, timestamp and error 
 */
        function validate_ts($param, $required = false) {
            $input = trim($_REQUEST[$param]);
            $error = '';
            $ts = false;
            
            if ($input){
                $ts = strtotime($input);
                if(false === $ts || $ts < 0){
                    $error = 'invalid';
                    $this->errors++;
                    $ts = false;
                }
            } else {    
                if($required) {
                    $error = 'required';
                    $this->errors++;
                } else {
                    $ts = $this->now;
                }
            }
            return array('input' => $input, 'ts' => $ts, 'error' => $error);
        } // end validate_ts
    } // end class
