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

        global $CFG, $DB;
        require_once($CFG->dirroot.'/mod/lti/lib.php');
        require_once($CFG->dirroot.'/mod/lti/locallib.php');
        require_once($CFG->dirroot.'/course/lib.php');

        $courseid = $this->page->course->id;
        $modinfo = get_fast_modinfo($courseid);
        $coursemoduleid = $this->getstudiosityid($modinfo);

        // Check if page is in course
        if (!empty($coursemoduleid)) {
            $this->incourse = true;
        }

        // Check if there is already a studiosity activity installed.
        $studiositytooltype = lti_get_tools_by_domain('studiosity.com');

            // If not installed and in course, install the activity in the course.
        if (count($studiositytooltype) == 1) {
            // Create the activity object to be added to course
            $studiosityobject = new stdClass();
            $studiosityobject->name = get_string('activitytitle', 'block_studiosity');
            $studiosityobject->typeid = reset($studiositytooltype)->id; // Get id of first (and only) object.
            $studiosityobject->course = $courseid;
            $studiosityobject->introformat = 1;
            $studiosityobject->showtitlelaunch = 1;

            // Create instance.
            $studiosityinstanceid = lti_add_instance($studiosityobject, null);
//            $studiosityinstance = $DB->get_record('lti', ['id' => $studiosityinstanceid]);

            // Add module to course
            list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($this->page->course, 'lti', 0);
            $data->return = 0;
            $data->sr = 0;
            $data->add = 'lti';
            $data->instance = $studiosityinstanceid;

            $cmid = add_course_module($data);
            course_add_cm_to_section($this->page->course->id, $cmid, 0);

        } else if (count($studiositytooltype) > 1) {
            // TODO handle if more than one studiosity plugin.
        } else {
            debugging(get_string('debugnoexternaltooltype', 'tool_studiosity'), DEBUG_NORMAL);
        }
        return true;
    }

    public function get_content() {
        global $CFG;

        $this->content = new stdClass;
        $renderer = $this->page->get_renderer('block_studiosity');

        $courseid = $this->page->course->id;
        $modinfo = get_fast_modinfo($courseid);
        $coursemoduleid = $this->getstudiosityid($modinfo);

        // If not in a course, do not render content.
        if (!empty($coursemoduleid)) {
            $this->content->text = $renderer->render_block(new \block_studiosity\output\block($courseid, $coursemoduleid));
            $this->content->footer = '';
        }
        return $this->content;
    }

    private function getstudiosityid(course_modinfo $modinfo) : string {
        // Check if the studiosity lti is present in the course and get LTI id if so.
        if (isset($modinfo->instances['lti'])) {
            foreach ($modinfo->instances['lti'] as $lti) {
                if (strpos(core_text::strtolower($lti->name),'studiosity') !== false) {
                    return $lti->id;
                }
            }
        }

        return '';
    }
}
