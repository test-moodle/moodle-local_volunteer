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

namespace local_volunteer\reportbuilder\local\systemreports;

use core_course\reportbuilder\local\entities\enrolment;
use core_enrol\reportbuilder\local\entities\enrol;
use core_group\reportbuilder\local\entities\group;
use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\system_report;
use core_reportbuilder\local\entities\user;
use core_role\reportbuilder\local\entities\role;
use lang_string;
use stdClass;

/**
 * Class users for the main table.
 *
 * @package    local_volunteer
 * @copyright  2025 Odei Alba <odeialba@odeialba.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users extends system_report {

    /**
     * Initialise report
     */
    protected function initialise(): void {
        global $DB;
        $currentcourse = get_course($this->get_context()->instanceid);

        $courseentity = new course();
        $this->add_entity($courseentity);

        $context = $courseentity->get_table_alias('context');
        $course = $courseentity->get_table_alias('course');
        $this->set_main_table('course', $course);

        // Exclude site course.
        $paramsiteid = database::generate_param_name();
        $this->add_base_condition_sql("{$course}.id != :{$paramsiteid}", [$paramsiteid => SITEID]);

        // Join the enrolment method entity.
        $enrolentity = new enrol();
        $enrol = $enrolentity->get_table_alias('enrol');
        $this->add_entity($enrolentity
            ->add_join("LEFT JOIN {enrol} {$enrol} ON {$enrol}.courseid = {$course}.id"));

        // Join the enrolments entity.
        $enrolmententity = (new enrolment())
            ->set_table_alias('enrol', $enrol);
        $userenrolment = $enrolmententity->get_table_alias('user_enrolments');
        $this->add_entity($enrolmententity
            ->add_joins($enrolentity->get_joins())
            ->add_join("LEFT JOIN {user_enrolments} {$userenrolment} ON {$userenrolment}.enrolid = {$enrol}.id"));

        // Join user entity.
        $userentity = new user();
        $user = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_joins($enrolmententity->get_joins())
            ->add_join("LEFT JOIN {user} {$user} ON {$user}.id = {$userenrolment}.userid AND {$user}.deleted = 0"));
        $this->add_base_condition_simple("{$user}.deleted", 0);
        $this->add_base_fields("{$user}.id, {$user}.confirmed, {$user}.suspended"); // Necessary for get_row_class.

        // Join the role entity.
        $roleentity = (new role())
            ->set_table_alias('context', $context);
        $role = $roleentity->get_table_alias('role');
        $this->add_entity($roleentity
            ->add_joins($userentity->get_joins())
            ->add_join($courseentity->get_context_join())
            ->add_join("LEFT JOIN {role_assignments} ras ON ras.contextid = {$context}.id AND ras.userid = {$user}.id")
            ->add_join("LEFT JOIN {role} {$role} ON {$role}.id = ras.roleid")
        );

        // Join group entity.
        $groupentity = (new group())
            ->set_table_alias('context', $context);
        $groups = $groupentity->get_table_alias('groups');

        // Show only groups that the user can see.
        $groupmode = groups_get_course_groupmode($currentcourse);
        if ($groupmode == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $this->get_context())) {
            $currentusergroups = groups_get_all_groups($currentcourse->id, 0, $currentcourse->defaultgroupingid, 'g.id', true);
            $currentusergroupids = array_column($currentusergroups, 'id');
            [$insql, $params] = $DB->get_in_or_equal(
                $currentusergroupids,
                SQL_PARAMS_NAMED,
                database::generate_param_name('_'),
                onemptyitems: null
            );

            $this->add_base_condition_sql("{$groups}.id {$insql}", $params);
        }

        // Sub-select for all course group members.
        $groupsinnerselect = "
            SELECT grs.*, grms.userid
            FROM {groups} grs
            JOIN {groups_members} grms ON grms.groupid = grs.id";

        $this->add_entity($groupentity
            ->add_join($courseentity->get_context_join())
            ->add_joins($userentity->get_joins())
            ->add_join("
                LEFT JOIN ({$groupsinnerselect}) {$groups}
                       ON {$groups}.courseid = {$course}.id AND {$groups}.userid = {$user}.id")
        );

        $this->add_columns($user);
        $this->add_filters();

        $this->set_downloadable(false);
    }

    /**
     * Validates access to view this report with the given parameters
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('local/volunteer:selectvolunteer', $this->get_context());
    }

    /**
     * Set the columns for the report.
     * @param string $usertablealias
     */
    protected function add_columns(string $usertablealias): void {
        // First column with checkboxes.
        $this->set_checkbox_toggleall(static function(stdClass $user): array {
            return [$user->id, fullname($user)];
        });

        $this->add_column_from_entity('user:fullnamewithpicture')
            ->set_title(new lang_string('fullname'))
            ->add_fields("{$usertablealias}.lastaccess, {$usertablealias}.suspended, {$usertablealias}.confirmed,
                {$usertablealias}.deleted");

        $this->add_column_from_entity('group:name')
            ->set_title(new lang_string('groups'))
            ->set_aggregation('groupconcatdistinct');

        $this->add_column_from_entity('role:name')
            ->set_title(new lang_string('roles'))
            ->set_aggregation('groupconcatdistinct');

        $this->set_initial_sort_column('user:fullnamewithpicture', SORT_ASC);
    }

    /**
     * Define report filters
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'user:fullname',
            'group:name',
            'role:name',
        ]);

        if (has_capability('moodle/course:enrolreview', $this->get_context())) {
            $this->add_filter_from_entity('enrolment:status');
        }

        $this->get_filter('group:name')->set_header(new lang_string('group'));
        $this->get_filter('role:name')->set_header(new lang_string('role'));
    }

    /**
     * CSS class for the row.
     *
     * @param stdClass $row
     * @return string
     */
    public function get_row_class(stdClass $row): string {
        return ($row->suspended || !$row->confirmed) ? 'text-muted' : '';
    }
}
