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
 * Renderable for block
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_studiosity\output;

defined('MOODLE_INTERNAL') || die;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use block_studiosity\simple_course;

/**
 * Studiosity block class.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block implements renderable, templatable {

    /** @var $coursemoduleid string The id of the Studiosity external tool activity. */
    private $coursemoduleid;

    /** @var $imagepath string URL of image to use for main brand. */
    private $imagepath;

    /** @var $courseid string The id of the Learning Hub course. */
    private $learninghubcourseid;

    /**
     * Construct the contents of the block
     * @param string $coursemoduleid The id of the Studiosity external tool activity.
     * @param string $imagepath URL of image to use for main brand.
     * @param string $learninghubcourseid The id of Learning Hub course.
     */
    public function __construct($coursemoduleid, $imagepath = '', $learninghubcourseid = '') {
        $fallbackimageurl = get_config('block_studiosity', 'defaultimageurl');
        $this->coursemoduleid = $coursemoduleid;
        $this->imagepath = empty($imagepath) ? $fallbackimageurl : $imagepath;
        if (!empty($learninghubcourseid)) {
            $this->learninghubcourseid = $learninghubcourseid;
        } else {
            $learninghubshortname = get_config('block_studiosity', 'learninghubshortname');
            $learninghubcourse = new simple_course($learninghubshortname);
            $this->learninghubcourseid = $learninghubcourse->get_id() ?? '';
        }
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass Data to be used for the template
     */
    public function export_for_template(renderer_base $output) {

        $data = new stdClass();
        $data->courseid = $this->learninghubcourseid;
        $data->coursemoduleid = $this->coursemoduleid;
        $data->imagepath = $this->imagepath;

        return $data;
    }
}
