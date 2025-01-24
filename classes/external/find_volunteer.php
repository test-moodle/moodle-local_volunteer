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

namespace local_volunteer\external;

use context_course;
use core_user;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use local_volunteer\permission;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/externallib.php');

/**
 * External function find_volunteer for local_volunteer.
 *
 * @package    local_volunteer
 * @copyright  2025 Odei Alba <odeialba@odeialba.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class find_volunteer extends external_api {
    /**
     * Returns the structure of parameters for find_volunteer.
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'The course id to check permissions.', VALUE_REQUIRED),
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'User id.', VALUE_REQUIRED),
                    'User ids to get the volunteer from.'
                ),
            ]
        );
    }

    /**
     * Finding a volunteer.
     *
     * @param int $courseid The course id to check permissions.
     * @param array $userids User ids to get the volunteer from.
     * @return array
     */
    public static function execute(int $courseid, array $userids) {
        global $DB, $USER, $PAGE;

        // Parameter validation.
        [
            'courseid' => $courseid,
            'userids' => $userids
        ] = self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
                'userids' => $userids,
            ]
        );

        if (empty($userids)) {
            return [
                'success' => false,
                'errormessage' => get_string('notenoughusers', 'local_volunteer'),
                'volunteeruserid' => (int) 0,
                'volunteerfullname' => (string) '',
            ];
        }

        $context = context_course::instance($courseid);
        self::validate_context($context);
        permission::require_can_select_volunteer($context);

        $volunteeruserindex = array_rand($userids);
        $volunteer = core_user::get_user($userids[$volunteeruserindex]);

        return [
            'success' => true,
            'volunteeruserid' => (int) $userids[$volunteeruserindex],
            'volunteerfullname' => (string) fullname($volunteer),
        ];
    }

    /**
     * Describes the return function of find_volunteer.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure(
            [
                'success' => new external_value(PARAM_BOOL, 'Returns true on success or throws an error.', VALUE_REQUIRED),
                'errormessage' => new external_value(PARAM_TEXT, 'Error text to inform user.', VALUE_OPTIONAL),
                'volunteeruserid' => new external_value(PARAM_INT, 'User id of selected volunteer.', VALUE_REQUIRED),
                'volunteerfullname' => new external_value(PARAM_TEXT, 'Full name of selected volunteer.', VALUE_REQUIRED),
            ]
        );
    }
}
