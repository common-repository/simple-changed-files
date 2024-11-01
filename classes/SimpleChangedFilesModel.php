<?php
/**
 * SimpleChangedFilesModel
 * the model class for SimpleChangedFiles
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
 * the model class gets the data for the display
 */
    class SimpleChangedFilesModel {
        private $filea;
        private $after;
        private $before;
        private $rootlen;
        
        /**
         * run the scan, returns an array of file data
         * @param type $st the start time array
         * @param type $et the end time array
         * @return type an array of file information (dir,name,ts)
         */
        public function doScan($st, $et) {
            $this->after = $st['ts'];
            $this->before = $et['ts'];
            $this->files = array();
            $dir = ABSPATH;
            if(substr($dir, strlen($dir) -1, 1) == DIRECTORY_SEPARATOR){
                $dir = substr($dir,0, strlen($dir) -1);
            }
            $this->rootlen = 1+strlen($dir);
            $this->scan_dirs($dir, $st, $et, 1+strlen($dir));
            return $this->files;
        } // end do_scan

        /**
         * recursively scan files to find those that were modified 
         * within the time range
         * - gather the file and directory names
         * - check the file modification times and add intersting one to the result
         * - recursively call for each directory
         * @param type $dir 
         */
        function scan_dirs($dir) {
            // echo "dir=$dir<br/>";
            $dh = opendir($dir);
            $files = array();
            $dirs = array();
            while (false != ($file = readdir($dh))) {
                if($file != '.' && $file != '..'){
                    $file = $dir.DIRECTORY_SEPARATOR.$file;
                    if(is_dir($file)) $dirs[] = $file; else $files[] = $file; 
                }
            }
            closedir($dh);

            foreach($files as $f){
                $mt = filemtime($f); 
                if($mt >= $this->after && $mt <= $this->before) {
                    $d = substr($dir, $this->rootlen);
                    $f = substr($f, strlen($dir)+1);
                    $this->files[] = array('dir' => $d, 'name' => $f, 'time' => $mt);
                }
            }
            $files = '';
            foreach($dirs as $d) {
                $this->scan_dirs($d);
            }
        }  // end scan_dirs
    }  // end class SimpleChangedFilesModel
