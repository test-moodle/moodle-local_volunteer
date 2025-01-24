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

namespace local_volunteer;

/**
 * Class permission to perform permission checks.
 *
 * @package    local_volunteer
 * @copyright  2025 Odei Alba <odeialba@odeialba.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class permission {
    /**
     * User can select volunteer.
     *
     * @param \context_course $context
     * @return bool
     */
    public static function can_select_volunteer(\context_course $context): bool {
        return has_capability('local/volunteer:selectvolunteer', $context);
    }

    /**
     * Make sure user can select volunteer.
     *
     * @param \context_course $context
     */
    public static function require_can_select_volunteer(\context_course $context) {
        require_capability('local/volunteer:selectvolunteer', $context);
    }
}
