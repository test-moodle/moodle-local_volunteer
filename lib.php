<?php
// This file is part of the local_volunteer plugin for Moodle - http://moodle.org/
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
 * Callback implementations for volunteer
 *
 * @package    local_volunteer
 * @copyright  2025 Odei Alba <odeialba@odeialba.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extend the course navigation with a "Volunteers" link which allows to randomly select a user.
 *
 * @param settings_navigation $navigation The settings navigation object
 * @param stdClass $course The course
 * @param stdclass $context Course context
 * @return void
 */
function local_volunteer_extend_navigation_course($navigation, $course, $context): void {
    if (has_capability('local/volunteer:selectvolunteer', $context)) {
        $url = new moodle_url('/local/volunteer/view.php', ['courseid' => $course->id]);
        $settingsnode = navigation_node::create(
            get_string('coursevolunteers', 'local_volunteer'),
            $url,
            navigation_node::TYPE_CUSTOM,
            null,
            'coursevolunteers'
        );
        $navigation->add_node($settingsnode);
    }
}
