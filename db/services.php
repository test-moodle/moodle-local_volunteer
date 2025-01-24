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
 * External functions and service declaration for volunteer
 *
 * Documentation: {@link https://moodledev.io/docs/apis/subsystems/external/description}
 *
 * @package    local_volunteer
 * @category   webservice
 * @copyright  2025 Odei Alba <odeialba@odeialba.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_volunteer\external\find_volunteer;
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_volunteer_find_volunteer' => [
        'classname' => find_volunteer::class,
        'description' => 'Get volunteer from provided user ids.',
        'type' => 'read',
        'capabilities' => 'local/volunteer:selectvolunteer',
        'ajax' => true,
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile'],
    ],
];
