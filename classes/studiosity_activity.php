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
 * This is a Moodle file.
 *
 * This is a longer description of the file.
 *
 * @package    mod_mymodule
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studiosity;

defined('MOODLE_INTERNAL') || die();

class studiosity_activity {
    public $name;
    public $typeid;
    public $course;
    public $coursemodule;

    public function __construct($courseid, $typeid) {
        $this->name = get_string('activitytitle', 'block_studiosity');
        $this->typeid = $typeid;
        $this->course = $courseid;
        $this->coursemodule = null; // Placeholder until added to course.
    }
}
