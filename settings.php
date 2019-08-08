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
 * Admin settings
 *
 * @package    block_studiosity
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext('block_studiosity/learninghubshortname',
            get_string('learninghubshortname', 'block_studiosity'),
            get_string('learninghubshortname_desc', 'block_studiosity'),
    'Learning-Hub'));

    $settings->add(new admin_setting_configtext('block_studiosity/defaultimageurl',
            get_string('defaultimageurl', 'block_studiosity'),
            get_string('defaultimageurl_desc', 'block_studiosity'),
    'https://lms.latrobe.edu.au/pluginfile.php/4730936/block_html/content/Tile%20Logo%20-%20Access%20Studiosity%20-%20Connect%20Here%20Now%20-%20200x112.png'));
}
