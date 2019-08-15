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
 * Take a csv file of course ids/shortnames and add block to each corresponding course.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_studiosity\simple_course;

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/externallib.php');

// Get parameters.
$shortopts = "";
//$shortopts = "u:p:h:d:";

$longopts = [
    "file:",
    "help",
];

$helpmessage =
"Usage: add_block_to_courses.php --file <courses.csv>

--file <filename>   Absolute path for csv file containing course shortnames.
";

$params = getopt($shortopts, $longopts);

if (array_key_exists("help", $params) || !array_key_exists("file", $params)) {
    echo $helpmessage;
    exit;
}

// Convert csv file to array. - Get Nicks code from Kmart for this?
$file = $params["file"];
$coursedata = utility::csv_to_array($file, ',', true);

// Get each course from the shortnames.
$courses = [];
$failed = [];
foreach ($coursedata as $row) {
    $course = new simple_course($row['shortname']);
    if ($course->course_exists()) {
        $courses[] = $course;
    } else {
        $failed[] = $row['shortname'];
    }
}

// Extract id's from courses.
$courseids = array_column($courses, 'id');
$courseids = array_unique($courseids);

// Log in as admin user.
external_api::validate_context($PAGE->context);

// Get list of pages from the course id's.
$pages = mod_page_external::get_pages_by_courses($courseids);

// Add block using page.
foreach ($pages as $page) {
    $blockmanager = new block_manager($page);
//    $missingblocks = $page->blocks->get_addable_blocks();
    // TODO: Check Studiosity block can be added to page.

    $blockmanager->add_block('studiosity', $blockmanager->get_default_region(), 0, true);
}

// If successful add to a list of courses successfully added to.

// Either create list of unsuccessful additions or throw error?

// If file not found throw error.

// Use --file param to get file.

// Can we use --type=id or --type=shortname to give them an option?

class utility {

    public static function csv_to_array($filename='', $delimiter='|', $includeheader=true) {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {

                if ($includeheader) {
                    if (!$header) {
                        $header = $row;
                    } else {
                        if (count($header) == count($row)) {
                            $data[] = array_combine($header, $row);
                        }
                    }
                } else {
                    $data[] = $row;
                }
            }
            fclose($handle);
        }
        return $data;
    }
}
