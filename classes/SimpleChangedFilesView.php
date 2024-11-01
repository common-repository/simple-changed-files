<?php
/**
 * SimpleChangedFilesView
 * the view class for SimpleChangedFiles
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


    class SimpleChangedFilesView {
/**
 * display the form to get a time range
 */        
        function showForm($start, $end, $errors) {
            // set up some variables used in the form
            $now = strtotime('now');
            $wp_ts = $this->formatTS($now);
            if ($errors) {
                $message = 'The form has errors';
            } else if($start['ts']){
                $st = $start['ts'];
                if($end['ts'])$et = $end['ts']; else  $et = $now;
                $message = 'Time range from '.$this->formatTS($st). ' to ' . $this->formatTS($et);
            } else {
                $message = 'Time range not set';
            }
        ?>

        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Display Changed Files</h2>

            <p>Current Time= <?php echo $wp_ts;?> </p>
            <p>Enter start and end time or date using any format that php recognizes, eg. "May 4" or "2011-05-04" or "2pm" 
               Click "Review Settings" to validate your time range or click "Run" to run a scan of your file.</p>

            <form action="" method="post">
            <?php settings_fields('scf_options_group');?>
                <table>
                    <tr>
                        <th>Start Time (required)</th>
                        <td><input id="scf_start_time" name="scf_start_time" value= "<?php echo $start['input']; ?>" /> </td>
                        <td style="color:red"><?php echo $start['error'] ?></td>
                    </tr>
                    <tr>
                        <th>End Time (optional)</th>
                        <td><input id="scf_end_time" name="scf_end_time" value= "<?php echo $end['input']; ?>" /></td>
                        <td style="color:red"><?php echo $end['error']; ?></td>
                    </tr>
                </table>

                <p class="submit"> 
                    <input name="submit" class="button-primary" type="submit" value="Review Settings"/>
                    <input name="submit" class="button" type="submit" value="Run"/>
                </p>
            </form>
        </div>

        <div>
            <p><?php echo $message; ?></p> 
        </div>
       <?php
        }

/**
 * display the list of files
 */
        function showFiles($files) {
        ?>
                <div>
                    <h2>Changed Files</h2> 
                    <table class='widefat'>
                        <tr>
                            <th>Directory</th>
                            <th>File</th>
                            <th>Changed on</th>
                        </tr>
                    <?php foreach($files as $file) : ?>
                        <tr>
                            <td><?php echo $file['dir'];?></td>
                            <td><?php echo $file['name'];?></td>
                            <td><?php echo $this->formatTS($file['time'], 'Y-m-d H:i:s');?></td>
                        </tr>
                    <?php endforeach; ?>   
                    </table>
                </div>   
        <?php
        }  // end show files
        
/**
 * format a timestame according to WordPress format specs and offset
 * @param type $ts
 * @return type 
 */
        function formatTS($ts, $fmt = null) {
            if(!$ts)return '';
            if(!$fmt) {
                $fmt = get_option( 'date_format' ) . ' ' . get_option( 'time_format' ); 
            }
            return date($fmt, $ts);
        }
    } // end class SimpleChangedFilesView
