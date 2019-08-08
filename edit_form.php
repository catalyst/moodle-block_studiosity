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
 * Extends the block configuration form.
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_studiosity_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG;

        // Section header title.
        $mform->addElement('header', 'config_heading', get_string('config:heading', 'block_studiosity'));

        // Allow image selection.
        $imageoptions = [
            'maxfiles' => 1,
            'maxbytes' => $CFG->maxbytes,
            'accepted_types' => ['image'],
            'subdirs' => 0,
        ];
        $mform->addElement('filemanager', 'config_image', get_string('config:selectimage', 'block_studiosity'),
                null, $imageoptions);
        $mform->setType('config_image', PARAM_FILE);
    }
}
