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
 * Class for the course object for a course.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studiosity;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for a simple course.
 * Currently only fetches the id and shortname. Can be expanded to fetch more data about course.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class simple_course {

    /** @var int $id Id of course. */
    private $id;

    /** @var string $shortname Shortname of course. */
    private $shortname;

    /**
     * Builds a class object based on a shortname.
     *
     * simple_course constructor.
     * @param string $shortname Shortname of course.
     */
    public function __construct(string $shortname) {
        $this->load_course($shortname);
    }

    /**
     * Loads a new course into the object.
     *
     * @param string $shortname Shortname for a course.
     */
    public function load_course(string $shortname) {
        global $DB;
        try {
            $this->shortname = $shortname;
            $courserecord = $DB->get_record('course', ['shortname' => $shortname], '*', MUST_EXIST);
            $this->id = $courserecord->id;
            // Populate object.
        } catch (\dml_exception $e) {
            $this->id = null; // No course exists with given shortname.
        }
    }

    /**
     * Checks if a course with the provided shortname exists.
     *
     * @return bool
     */
    public function course_exists() {
        return ($this->id !== null);
    }

    /**
     * Getter for course id.
     *
     * @return int|null
     */
    public function get_id() {
        return $this->id;
    }
}
