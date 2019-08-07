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
 * General class for creating a Studiosity external tool activity.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studiosity;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for Studiosity external tools activity
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class studiosity_activity {

    /** @var string $name Name of activity. */
    public $name;

    /** @var int $typeid Id of external tool type setup in Site Administration. */
    public $typeid;

    /** @var int $course Id of course activity will be added to. */
    public $course;

    /** @var null $coursemodule Placeholder until added to course as module. */
    public $coursemodule;

    /**
     * studiosity_activity constructor.
     *
     * @param $courseid int Course the activity will be added to.
     * @param $typeid int Tool type id of the site level configuration for external tool.
     * @throws \coding_exception
     */
    public function __construct($courseid, $typeid) {
        $this->name = get_string('activitytitle', 'block_studiosity');
        $this->typeid = $typeid;
        $this->course = $courseid;
        $this->coursemodule = null; // Placeholder until added to course.
    }
}
