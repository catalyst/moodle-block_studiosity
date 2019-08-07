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
 * Studiosity block for displaying link and sending context.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class for Studiosity block.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_studiosity extends block_base {

    /**
     * Initial function called to load block.
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_studiosity');
    }

    /**
     * Called immediately after init() and has access to config.
     * @throws moodle_exception
     */
    public function specialization() {
        parent::specialization();

        // Check if there is already a studiosity activity installed.
        $modinfo = get_fast_modinfo($this->page->course->id);
        $studiosityid = $this->getstudiosityid($modinfo);

        // If already installed or not in course, don't install the activity in the course.
        if (!$this->ispageincourse() || !empty($studiosityid)) {
            return;
        }

        $studiosityinstanceid = $this->createstudiosityinstance($this->page->course->id);

        if ($studiosityinstanceid !== null) {
            $this->addstudiosityactivitytocourse($this->page->course, $studiosityinstanceid);
        } else {
            debugging(get_string('debug:activitynotcreated', 'block_studiosity'), DEBUG_DEVELOPER);
        }
    }

    /**
     * Set up the content of the block.
     *
     * @return stdClass Contains the text and footer for the block
     * @throws moodle_exception
     */
    public function get_content() {
        $this->content = new stdClass;
        $renderer = $this->page->get_renderer('block_studiosity');

        $courseid = $this->page->course->id;
        $modinfo = get_fast_modinfo($courseid);
        $studiosityid = $this->getstudiosityid($modinfo);

        $this->content->text = $renderer->render_block(new \block_studiosity\output\block($courseid, $studiosityid));
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * When instance is deleted, remove Studiosity activity from course if it is there.
     *
     * @return bool|void
     * @throws coding_exception
     * @throws moodle_exception
     * @overrides
     */
    public function instance_delete() {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $modinfo = get_fast_modinfo($this->page->course->id);
        $studiosityid = $this->getstudiosityid($modinfo);
        if (!empty($studiosityid)) {
            course_delete_module($studiosityid);
        }
    }

    /**
     * If there is a Studiosity course module in course, get the id.
     *
     * @param course_modinfo $modinfo Cached information about course modules.
     * @return string The id of the studiosity lti activity or and empty string if no activity.
     * @throws coding_exception
     */
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

    /**
     * Creates an instance of the Studiosity activity for a course.
     *
     * @param int   $courseid Id of course the activity will be added to.
     * @param null  $studiositytooltype The id of the Studiosity external tool activity type setup in Site Admin.
     * @return int|null The id of the instance that is created.
     * @throws coding_exception
     */
    private function createstudiosityinstance($courseid, $studiositytooltype = null) {
        global $CFG;
        require_once($CFG->dirroot.'/mod/lti/lib.php');
        require_once($CFG->dirroot.'/mod/lti/locallib.php');

        $studiositytooltype = $studiositytooltype ?? lti_get_tools_by_domain('studiosity.com');
        if (count($studiositytooltype) != 0) {
            // Create the activity object to be added to course.
            $studiosityobject = $this->generatestudiosityobject($courseid, reset($studiositytooltype)->id);

            // Create instance.
            return lti_add_instance($studiosityobject, null);
        } else {
            debugging(get_string('debug:noexternaltooltype', 'tool_studiosity'), DEBUG_NORMAL);
            return null;
        }
    }

    /**
     * Creates course module from the activity and adds it to a course.
     *
     * @param $course
     * @param $studiosityinstanceid int Id of the Studiosity activity.
     * @throws dml_exception
     */
    private function addstudiosityactivitytocourse($course, $studiosityinstanceid) {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        // Add a module to course.
        $data = $this->preparemoduleinfodata($course, 'lti', 0);
        $data->instance = $studiosityinstanceid;

        $cmid = add_course_module($data);
        course_add_cm_to_section($course->id, $cmid, 0);
    }

    /**
     * Sets up the studiosity activity object to be instantiated.
     *
     * @param $studiositytooltypeid int Reference for parent configuration of activity.
     * @return stdClass
     * @throws coding_exception
     */
    private function generatestudiosityobject($courseid, $studiositytooltypeid) {
        $studiosityobject = new stdClass();
        $studiosityobject->name = get_string('activitytitle', 'block_studiosity');
        $studiosityobject->typeid = $studiositytooltypeid;
        $studiosityobject->course = $courseid;
        $studiosityobject->coursemodule = null; // Placeholder until added to course.
        return $studiosityobject;
    }

    /**
     * Sets up the module data to be used in creating a course module.
     *
     * @param $course
     * @param $modulename string Name of module e.g. LTI.
     * @param $section int Section to add module to.
     * @return stdClass data for the course module instantiation.
     * @throws dml_exception
     */
    private function preparemoduleinfodata($course, $modulename, $section) {
        global $DB;
        $module = $DB->get_record('modules', array('name' => $modulename), '*', MUST_EXIST);

        $data = new stdClass();
        $data->section          = $section;
        $data->visible          = false;
        $data->visibleold       = false;
        $data->course           = $course->id;
        $data->module           = $module->id;
        $data->modulename       = $module->name;
        $data->groupmode        = $course->groupmode;
        $data->groupingid       = $course->defaultgroupingid;
        $data->id               = '';
        $data->instance         = '';
        $data->coursemodule     = '';

        return $data;
    }

    /**
     * Checks whether the page is within a course.
     *
     * @return bool
     */
    private function ispageincourse() {
        if ($this->page->course && $this->page->course->id != SITEID) {
            return true;
        }
        return false;
    }
}
