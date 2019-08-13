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
 * Tests for simple course class
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test class for simple course
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class simple_course_testcase extends advanced_testcase {

    /**
     * Test class is instantiated correctly.
     */
    public function test_simple_course_is_created() {
        $this->resetAfterTest();
        $shortname = 'shortnameunique';
        $course = $this->getDataGenerator()->create_course(['shortname' => $shortname]);
        $simplecourse = new \block_studiosity\simple_course($shortname);
        $this->assertEquals($course->id, $simplecourse->get_id());
    }

    /**
     * Test class is instantiated correctly without input.
     */
    public function test_simple_course_is_not_created() {
        $this->resetAfterTest();
        $shortname = 'shortnameunique';
        $simplecourse = new \block_studiosity\simple_course($shortname);
        $this->assertNull($simplecourse->get_id());
    }

    /**
     * Test class flags if course exists correctly.
     */
    public function test_course_exists() {
        $this->resetAfterTest();
        $shortname = 'shortnameunique';
        $course = $this->getDataGenerator()->create_course(['shortname' => $shortname]);
        $simplecourse = new \block_studiosity\simple_course($shortname);
        $this->assertTrue($simplecourse->course_exists());
    }

    /**
     * Test class flags if course does not exist correctly.
     */
    public function test_course_does_not_exist() {
        $this->resetAfterTest();
        $shortname = 'shortnameunique';
        $simplecourse = new \block_studiosity\simple_course($shortname);
        $this->assertFalse($simplecourse->course_exists());
    }

    /**
     * Test new course can be loaded into the object.
     */
    public function test_load_course() {
        $this->resetAfterTest();
        $shortname = 'shortnameunique';
        $shortname2 = 'anotheruniqueshortname';

        // First load one course.
        $course = $this->getDataGenerator()->create_course(['shortname' => $shortname]);
        $simplecourse = new \block_studiosity\simple_course($shortname);
        $this->assertEquals($course->id, $simplecourse->get_id());

        // Then load another.
        $course2 = $this->getDataGenerator()->create_course(['shortname' => $shortname2]);
        $simplecourse->load_course($shortname2);
        $this->assertEquals($course2->id, $simplecourse->get_id());
    }
}
