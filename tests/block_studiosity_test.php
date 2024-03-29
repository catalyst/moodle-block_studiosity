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
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        global $CFG;
        require_once($CFG->libdir . '/pagelib.php');
        require_once($CFG->libdir . '/blocklib.php');
        require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
        require_once($CFG->dirroot . '/blocks/studiosity/block_studiosity.php');
    }

    /**
     * Test check if the block is within a course.
     */
    public function test_is_page_in_course() {
        $this->resetAfterTest();

        $sitepage = new moodle_page();
        $block = new block_studiosity();
        $block->page = $sitepage;
        $this->assertEquals($this->invoke_method($block, 'is_page_in_course'), false);

        $course = $this->getDataGenerator()->create_course();
        $coursepage = new moodle_page();
        $coursepage->set_course($course);
        $block = new block_studiosity();
        $block->page = $coursepage;
        $this->assertEquals($this->invoke_method($block, 'is_page_in_course'), true);
    }

    /**
     * Tests activity is added to course.
     *
     * @param string $archetype Role archetype.
     * @throws coding_exception
     * @throws moodle_exception
     * @throws ReflectionException
     * @dataProvider role_data_provider
     */
    public function test_activity_added_to_course($archetype) {
        $this->resetAfterTest();
        $this->setup_user($archetype);

        // Setup page.
        $coursepage = new moodle_page();
        $course = $this->getDataGenerator()->create_course();
        $coursepage->set_course($course);

        // Add activity to a course.
        $block = new block_studiosity();
        $block->page = $coursepage;
        $this->add_activity_to_course($block);

        // Test activity course module exists in course.
        $modinfo = get_fast_modinfo($course->id);
        $studiosityid = $this->invoke_method($block, 'get_studiosity_id', [$modinfo]);
        $this->assertNotEmpty($studiosityid);
    }

    /**
     * Test activity not added if page is site level.
     *
     * @param string $archetype Role archetype.
     * @throws coding_exception
     * @throws moodle_exception
     * @throws ReflectionException
     * @dataProvider role_data_provider
     */
    public function test_activity_not_added_to_site($archetype) {
        $this->resetAfterTest();
        $this->setup_user($archetype);

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
        $studiosityid = $this->invoke_method($block, 'get_studiosity_id', [$modinfo]);
        $this->assertEmpty($studiosityid);
    }

    /**
     * Test that the activity is deleted when the block is.
     *
     * @param string $archetype Role archetype.
     * @throws ReflectionException
     * @throws coding_exception
     * @throws moodle_exception
     * @dataProvider role_data_provider
     */
    public function test_activity_deleted($archetype) {
        $this->resetAfterTest();
        $this->setup_user($archetype);

        // Setup page.
        $coursepage = new moodle_page();
        $course = $this->getDataGenerator()->create_course();
        $coursepage->set_course($course);

        // Add activity to a course.
        $block = new block_studiosity();
        $block->page = $coursepage;
        $this->add_activity_to_course($block);

        // Check it was added successfully.
        $modinfo = get_fast_modinfo($course->id);
        $studiosityid = $this->invoke_method($block, 'get_studiosity_id', [$modinfo]);
        $this->assertNotEmpty($studiosityid);

        $block->instance_delete(); // Simulate block is being deleted.

        // Test module has been removed on block deletion.
        $modinfo = get_fast_modinfo($course->id);
        $studiosityid = $this->invoke_method($block, 'get_studiosity_id', [$modinfo]);
        $this->assertEmpty($studiosityid);
    }

    /**
     * Data provider for role archetypes.
     *
     * @return array ['archetype']
     */
    public function role_data_provider() {
        return [
            'admin archetype' => ['admin'],
            'guest archetype' => ['guest'],
            'student archetype' => ['student'],
            'teacher archetype' => ['teacher'],
            'editingteacher archetype' => ['editingteacher'],
            'coursecreator archetype' => ['coursecreator'],
            'manager archetype' => ['manager'],
        ];
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $object Instantiated object that we will run method on.
     * @param string $methodname Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @throws ReflectionException
     */
    public function invoke_method(&$object, $methodname, array $parameters = array()) {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodname);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Use the blocks main methods to add activity to course.
     *
     * @param block_studiosity $block An instance of the Studiosity block.
     * @throws ReflectionException
     */
    private function add_activity_to_course(&$block) {
        $mocktooltype = new stdClass();
        $mocktooltype->id = '999';
        $studiosityinstanceid = $this->invoke_method($block, 'create_studiosity_instance',
                [$block->page->course->id, [$mocktooltype]]);
        if ($studiosityinstanceid !== null) {
            $this->invoke_method($block, 'add_studiosity_activity_to_course', [$block->page->course, $studiosityinstanceid]);
        }
    }

    /**
     * Setup the current user with a generated role.
     *
     * @param string $archetype An archetype for the generated role.
     * @throws coding_exception
     */
    private function setup_user($archetype) {
        // Setup user.
        $user = $this->getDataGenerator()->create_user();
        $roleid = $this->getDataGenerator()->create_role($record['archetype'] = $archetype);
        $this->getDataGenerator()->role_assign($roleid, $user->id);
        $this->setUser($user);
    }

}
