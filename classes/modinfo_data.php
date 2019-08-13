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
 * General data object for creating a course module.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studiosity;

defined('MOODLE_INTERNAL') || die();

/**
 * General data object for creating a course module.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modinfo_data {

    /** @var int $section Section activity will be added to .*/
    public $section;

    /** @var bool $visible Can students see it in course. */
    public $visible;

    /** @var bool $visibleold Deprecated visible. */
    public $visibleold;

    /** @var int $course Id of course to be added to. */
    public $course;

    /** @var int $module Id of module type. */
    public $module;

    /** @var string $modulename Name of module type. */
    public $modulename;

    /** @var string $groupmode Inherited from course. */
    public $groupmode;

    /** @var int $groupingid Inherited from course. */
    public $groupingid;

    /** @var string $id Placeholder for id. */
    public $id;

    /** @var string $instance Placeholder for activity instance id. */
    public $instance;

    /** @var string $coursemodule Placeholder for course module id. */
    public $coursemodule;

    /**
     * modinfo_data constructor.
     *
     * @param $course
     * @param string $modulename Name of module type, e.g. 'lti'.
     * @param int $section Section module to be added to.
     * @throws \dml_exception
     */
    public function __construct($course, $modulename, $section) {
        $module = $this->get_module_by_name($modulename);

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

    /**
     * Gets the module object from DB based on name.
     *
     * @param $modulename Name of module type, e.g. 'lti'.
     * @return mixed
     * @throws \dml_exception
     */
    private function get_module_by_name($modulename) {
        global $DB;
        return $DB->get_record('modules', array('name' => $modulename), '*', MUST_EXIST);
    }
}
