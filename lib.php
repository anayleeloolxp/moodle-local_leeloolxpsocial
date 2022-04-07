<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local Library file for additional Functions
 *
 * @package    local_leeloolxpsocial
 * @copyright  2020 Leeloo LXP (https://leeloolxp.com)
 * @author     Leeloo LXP <info@leeloolxp.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Fetch and Update Configration From L
 */
function local_leeloolxpsocial_updateset() {

    global $CFG;

    $addsocialpage = get_config('local_leeloolxpsocial')->addsocialpage;

    if ($addsocialpage == 1) {
        $fs = get_file_storage();
        $files = $fs->get_area_files(1, 'local_staticpage', 'documents');

        $filenames = array();

        foreach ($files as $f) {
            $filenames[] = $f->get_filename();
        }

        if (!in_array('leeloolxp-social-network.html', $filenames)) {
            $filename = $CFG->dirroot . '/local/leeloolxpsocial/html/leeloolxp-social-network.html';
            $filerecord = array(
                'contextid' => 1,
                'component' => 'local_staticpage',
                'filearea' => 'documents',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => 'leeloolxp-social-network.html',
            );
            $file = $fs->create_file_from_pathname($filerecord, $filename);
        }

        $addmenusocial = PHP_EOL . 'Leeloo LXP Social|/local/staticpage/view.php?page=leeloolxp-social-network||||||fa-users|leeloossourlj|';

        $existinglinks = get_config('local_boostnavigation')->insertcustomnodesusers;

        if (strpos($existinglinks, 'leeloolxp-social-network') === false) {
            set_config('insertcustomnodesusers', $existinglinks . $addmenusocial, 'local_boostnavigation');
        }
    } else {
        $fs = get_file_storage();

        $fileinfo = array(
            'component' => 'local_staticpage',
            'filearea' => 'documents', // usually = table name
            'itemid' => 0, // usually = ID of row in table
            'contextid' => 1, // ID of context
            'filepath' => '/', // any path beginning and ending in /
            'filename' => 'leeloolxp-social-network.html'); // any filename

        // Get file
        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

        // Delete it if it exists
        if ($file) {
            $file->delete();
        }

        $existinglinks = get_config('local_boostnavigation')->insertcustomnodesusers;
        $listext = explode(PHP_EOL, $existinglinks);
        $newlist = array();

        foreach ($listext as $existmen) {
            if (strpos($existmen, 'leeloolxp-social-network') === false) {
                $newlist[] = $existmen;
            }
        }

        $newliststr = implode(PHP_EOL, $newlist);

        set_config('insertcustomnodesusers', $newliststr, 'local_boostnavigation');
    }

    return;
}

/**
 * Function to add social frame in arena page.
 */
function local_leeloolxpsocial_before_footer() {
    global $PAGE;
    $PAGE->requires->js_init_code("window.addEventListener('message', function(e) {
            if (e.data == 'loadarenasocial') {
                function getCookie(cname) {
                    var name = cname + '=';
                    var decodedCookie = decodeURIComponent(document.cookie);
                    var ca = decodedCookie.split(';');
                    for(var i = 0; i <ca.length; i++) {
                            var c = ca[i];
                            while (c.charAt(0) == ' ') {
                                    c = c.substring(1);
                            }
                            if (c.indexOf(name) == 0) {
                                    return c.substring(name.length, c.length);
                            }
                    }
                    return '';
                }
                var jsession_id = getCookie('jsession_id');
                var license_key = getCookie('license_key');
                var moodleurl = getCookie('moodleurl');

                if( document.getElementsByClassName('leeloosocial').length != 0){
                    
                    var framesrc = 'https://leeloolxp.com/es-frame?session_id='+jsession_id+'&leeloolxplicense='+license_key;

                    document.getElementsByClassName('leeloosocial')[0].contentWindow.postMessage('loadarenasocial-'+framesrc, '*');
                }
                
            }
        });");
}
