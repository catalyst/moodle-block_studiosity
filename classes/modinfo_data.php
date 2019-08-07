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

class modinfo_data {

    public $section;
    public $visible;
    public $visibleold;
    public $course;
    public $module;
    public $modulename;
    public $groupmode;
    public $groupingid;
    public $id;
    public $instance;
    public $coursemodule;

    public function __construct($course, $module, $section) {
        $this->section = $section;
        $this->visible = false;
        $this->visibleold = false;
        $this->course = $course->id;
        $this->module = $module->id;
        $this->modulename = $module->name;
        $this->groupmode = $course->groupmode;
        $this->groupingid = $course->defaultgroupingid;
        $this->id = '';
        $this->instance = '';
        $this->coursemodule = '';
    }
}
