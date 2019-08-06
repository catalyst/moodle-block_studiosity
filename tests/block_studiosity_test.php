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
    public function test_is_page_in_course($page, $expected) {
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
        $sitepage = new moodle_page();
        $coursepage = new moodle_page();
        $course = $this->getDataGenerator()->create_course();
        $coursepage->set_course($course);
        return [
            'Empty Page' => [$sitepage, false],
            'Page with Course' => [$coursepage, true],
        ];
    }

    public function test_studiosity_object_generated() {
        $this->resetAfterTest();
        $block = new block_studiosity();
        $page = new moodle_page();
        $course = $this->getDataGenerator()->create_course();
        $page->set_course($course);
        $block->page = $page;
        $typeid = 1;
        $studiosityobject = $this->invokemethod($block, 'generatestudiosityobject', [$course->id, $typeid]);
        // Test activity type id is passed to object.
        $this->assertEquals($typeid, $studiosityobject->typeid);
        // Test course set properly.
        $this->assertEquals($course->id, $studiosityobject->course);
        // Test name is set.
        $this->assertNotEmpty($studiosityobject->name);
    }

    public function test_activity_added_to_course() {
        $this->resetAfterTest();
        // Setup page.
        $coursepage = new moodle_page();
        $course = $this->getDataGenerator()->create_course();
        $coursepage->set_course($course);

        // Add activity to a course.
        $block = new block_studiosity();
        $mocktooltype = new stdClass();
        $mocktooltype->id = '999';
        $studiosityinstanceid = $this->invokemethod($block, 'createstudiosityinstance', [$course->id, [$mocktooltype]]);
        if ($studiosityinstanceid !== null) {
            $this->invokemethod($block, 'addstudiosityactivitytocourse', [$course, $studiosityinstanceid]);
        }

        $modinfo = get_fast_modinfo($course->id);
        $studiosityid = $this->invokemethod($block, 'getstudiosityid', [$modinfo]);
        $this->assertNotEmpty($studiosityid);
    }

    public function test_activity_not_added_to_site() {
        $this->resetAfterTest();
        // Setup page.
        $sitepage = new moodle_page();
        $course = $this->getDataGenerator()->create_course();
        $course->id = 1; // Site course id.
        $sitepage->set_course($course);

        // Mock page load.
        $block = new block_studiosity();
        $block->page = $sitepage;
        $block->specialization();

        $modinfo = get_fast_modinfo(1); // Site course.
        $studiosityid = $this->invokemethod($block, 'getstudiosityid', [$modinfo]);
        $this->assertEmpty($studiosityid);
    }

    public function role_data_provider() {
        $generator = $this->getDataGenerator();
        $adminrole = $generator->create_role(['archetype' => 'admin']);
        $guestrole = $generator->create_role(['archetype' => 'guest']);
        $studentrole = $generator->create_role(['archetype' => 'student']);
        $teacherrole = $generator->create_role(['archetype' => 'teacher']);
        $editingteacherrole = $generator->create_role(['archetype' => 'editingteacher']);
        $coursecreatorrole = $generator->create_role(['archetype' => 'coursecreator']);
        $managerrole = $generator->create_role(['archetype' => 'manager']);

        return [
            'adminrole' => [$adminrole],
            'guestrole' => [$guestrole],
            'studentrole' => [$studentrole],
            'teacherrole' => [$teacherrole],
            'editingteacherrole' => [$editingteacherrole],
            'coursecreatorrole' => [$coursecreatorrole],
            'managerrole' => [$managerrole],
        ];
    }

    // Test the roles that can access it?

    // Test that studiosity activity is not visible to students

    // TODO behat - Test content is created

    // TODO behat - Test mustache template works

    // Test that activity is removed when block is removed?

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
