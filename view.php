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
 * Prints an instance of local_volunteer to allow teachers to select volunteers.
 *
 * @package    local_volunteer
 * @copyright  2025 Odei Alba <odeialba@odeialba.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

// Course id.
$courseid = (int) required_param('courseid', PARAM_INT);

$url = new moodle_url('/local/volunteer/view.php', ['courseid' => $courseid]);
$PAGE->set_url($url);

// Basic access checks.
if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    throw new \moodle_exception('invalidcourseid');
}
require_login($course);

$context = context_course::instance($course->id);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('coursevolunteers', 'local_volunteer'));
$PAGE->add_body_class('limitedwidth');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('coursevolunteers', 'local_volunteer'));

// Show the report using core_reportbuilder.
$report = core_reportbuilder\system_report_factory::create(
    local_volunteer\reportbuilder\local\systemreports\users::class,
    $context
);
echo $report->output();

echo html_writer::empty_tag(
    'input',
    [
        'class' => 'btn btn-primary form-submit',
        'type' => 'submit',
        'value' => get_string('findvolunteer', 'local_volunteer'),
        'data-action' => 'findvolunteer',
    ]
);

$PAGE->requires->js_call_amd('local_volunteer/volunteer', 'init', [$courseid, $USER->id]);

echo $OUTPUT->footer();
