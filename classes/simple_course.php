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
 *
 * Currently only fetches the id and shortname. Can be expanded to fetch more data about course.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class simple_course {

    /** @var int $id Id of course. */
    public $id;

    /** @var string category */
    public $category;

    /** @var string sortorder */
    public $sortorder;

    /** @var string fullname */
    public $fullname;

    /** @var string shortname */
    public $shortname;

    /** @var string idnumber */
    public $idnumber;

    /** @var string summary */
    public $summary;

    /** @var string summaryformat */
    public $summaryformat;

    /** @var string format */
    public $format;

    /** @var string showgrades */
    public $showgrades;

    /** @var string newsitems */
    public $newsitems;

    /** @var string startdate */
    public $startdate;

    /** @var string enddate */
    public $enddate;

    /** @var string marker */
    public $marker;

    /** @var string maxbytes */
    public $maxbytes;

    /** @var string legacyfiles */
    public $legacyfiles;

    /** @var string showreports */
    public $showreports;

    /** @var string visible */
    public $visible;

    /** @var string visibleold */
    public $visibleold;

    /** @var string groupmode */
    public $groupmode;

    /** @var string groupmodeforce */
    public $groupmodeforce;

    /** @var string defaultgroupingid */
    public $defaultgroupingid;

    /** @var string lang */
    public $lang;

    /** @var string calendartype */
    public $calendartype;

    /** @var string theme */
    public $theme;

    /** @var string timecreated */
    public $timecreated;

    /** @var string timemodified */
    public $timemodified;

    /** @var string requested */
    public $requested;

    /** @var string enablecompletion */
    public $enablecompletion;

    /** @var string completionnotify */
    public $completionnotify;

    /** @var string cacherev */
    public $cacherev;

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

            // Populate object.
            $this->id = $courserecord->id;
            $this->category = $courserecord->category;
            $this->sortorder = $courserecord->sortorder;
            $this->fullname = $courserecord->fullname;
            $this->shortname = $courserecord->shortname;
            $this->idnumber = $courserecord->idnumber;
            $this->summary = $courserecord->summary;
            $this->summaryformat = $courserecord->summaryformat;
            $this->format = $courserecord->format;
            $this->showgrades = $courserecord->showgrades;
            $this->newsitems = $courserecord->newsitems;
            $this->startdate = $courserecord->startdate;
            $this->enddate = $courserecord->enddate;
            $this->marker = $courserecord->marker;
            $this->maxbytes = $courserecord->maxbytes;
            $this->legacyfiles = $courserecord->legacyfiles;
            $this->showreports = $courserecord->showreports;
            $this->visible = $courserecord->visible;
            $this->visibleold = $courserecord->visibleold;
            $this->groupmode = $courserecord->groupmode;
            $this->groupmodeforce = $courserecord->groupmodeforce;
            $this->defaultgroupingid = $courserecord->defaultgroupingid;
            $this->lang = $courserecord->lang;
            $this->calendartype = $courserecord->calendartype;
            $this->theme = $courserecord->theme;
            $this->timecreated = $courserecord->timecreated;
            $this->timemodified = $courserecord->timemodified;
            $this->requested = $courserecord->requested;
            $this->enablecompletion = $courserecord->enablecompletion;
            $this->completionnotify = $courserecord->completionnotify;
            $this->cacherev = $courserecord->cacherev;
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
