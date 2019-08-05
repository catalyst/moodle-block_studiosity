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
 * Studiosity block for displaying link and sending context
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_studiosity extends block_base {

    private $incourse = false;

    public function init() {
        $this->title = get_string('pluginname', 'block_studiosity');
    }

    public function specialization() {
        parent::specialization();

        // Check if page is in course
        if ($this->page->course && $this->page->course->id != SITEID) {
            $this->incourse = true;
        }

        // Check if there is already a studiosity activity installed.
        $modinfo = get_fast_modinfo($this->page->course->id);
        $studiosityid = $this->getstudiosityid($modinfo);

        // If not installed and in course, install the activity in the course.
        if ($this->incourse && $studiosityid === null) {
            $this->addstudiosityactivitytocourse();
        }
    }

    public function get_content() {
        global $CFG;

        $this->content = new stdClass;
        $renderer = $this->page->get_renderer('block_studiosity');

        $courseid = $this->page->course->id;
        $modinfo = get_fast_modinfo($courseid);
        $studiosityid = $this->getstudiosityid($modinfo);

        // If not in a course, do not render content.
        if ($this->incourse) {
            $this->content->text = $renderer->render_block(new \block_studiosity\output\block($courseid, $studiosityid));
            $this->content->footer = '';
        }
        return $this->content;
    }

    private function getstudiosityid(course_modinfo $modinfo) : string {
        // Check if the studiosity lti is present in the course and get LTI id if so.
        if (isset($modinfo->instances['lti'])) {
            foreach ($modinfo->instances['lti'] as $lti) {
                if (stripos($lti->name, get_string('activitytitle', 'block_studiosity')) !== false) {
                    return $lti->id;
                }
            }
        }
        return '';
    }

    private function addstudiosityactivitytocourse() {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/mod/lti/lib.php');
        require_once($CFG->dirroot.'/mod/lti/locallib.php');
        require_once($CFG->dirroot.'/course/lib.php');

        $studiositytooltype = lti_get_tools_by_domain('studiosity.com');
        if (count($studiositytooltype) != 0) {
            // Create the activity object to be added to course.
            $studiosityobject = $this->generatestudiosityobject($studiositytooltype);

            // Create instance.
            $studiosityinstanceid = lti_add_instance($studiosityobject, null);

            // Add an invisible module to course.
            list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($this->page->course, 'lti', 0);
            $data->instance = $studiosityinstanceid;
            $data->visible = false;
            $data->visibleold = false;

            $cmid = add_course_module($data);
            course_add_cm_to_section($this->page->course->id, $cmid, 0);
        } else {
            debugging(get_string('debugnoexternaltooltype', 'tool_studiosity'), DEBUG_NORMAL);
        }
    }

    private function generatestudiosityobject($studiositytooltype) {
        $studiosityobject = new stdClass();
        $studiosityobject->name = get_string('activitytitle', 'block_studiosity');
        $studiosityobject->typeid = reset($studiositytooltype)->id; // Assumes only one Studiosity activity.
        $studiosityobject->course = $this->page->course->id;
        return $studiosityobject;
    }
}
