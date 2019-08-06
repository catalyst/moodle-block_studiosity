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
 * PHPUnit tests for the block class.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class block_studiosity_testcase
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_studiosity_testcase extends advanced_testcase {

    // Setup course with the activity, and without the activity already installed.

    /**
     * Loads libraries needed to setup tests.
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        global $CFG;
        require_once($CFG->libdir . '/pagelib.php');
        require_once($CFG->libdir . '/blocklib.php');
        require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
        require_once($CFG->dirroot . '/blocks/studiosity/block_studiosity.php');
    }

    /**
     * @param $page moodle_page A mock of a moodle page.
     * @param $expected bool True if the page is within a course.
     * @dataProvider page_types_provider
     */
    public function test_ispageincourse($page, $expected) {
        $this->resetAfterTest();
        $block = new block_studiosity();
        $block->page = $page;
        $actual = $this->invokemethod($block, 'ispageincourse');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for page types
     *
     * @return array [moodle_page $page, bool $incourse]
     */
    public function page_types_provider() {
        $sitepage = new testable_moodle_page();
        $coursepage = new testable_moodle_page();
        $course = $this->getDataGenerator()->create_course();
        $coursepage->set_course($course);
        return [
            'Empty Page' => [$sitepage, false],
            'Page with Course' => [$coursepage, true],
        ];
    }

    // Test block can only be added inside a course

    // Test that activity is added correctly with correct attributes

//    public function test_add_studiosity_activity_to_course() {
//        $this->resetAfterTest();
//
//    }

    // Test that studiosity activity is not visible to students

    // Test studiosity activity is generated with correct attributes

    // Test content is created

    // Test mustache template works

    // Test modinfo accurately checks if activity is present

    // Test that activity is removed when block is removed?

    // Test the roles that can access it?

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokemethod(&$object, $methodName, array $parameters = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}

/**
 * Test-specific subclass to make some protected things public.
 * Taken from moodle_page_test.php
 */
class testable_moodle_page extends moodle_page {
    public function initialise_default_pagetype($script = null) {
        parent::initialise_default_pagetype($script);
    }
    public function url_to_class_name($url) {
        return parent::url_to_class_name($url);
    }
    public function all_editing_caps() {
        return parent::all_editing_caps();
    }
}
