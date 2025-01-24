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
 * AMD module used to select a volunteer.
 *
 * @module     local_volunteer/volunteer
 * @copyright  2025 Odei Alba <odeialba@odeialba.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import Ajax from 'core/ajax';
import {getString} from 'core/str';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {add as toastAdd} from 'core/toast';

const SELECTORS = {
    FINDVOLUNTEER: "[data-action='findvolunteer']"
};

const getCheckedUsers = () => {
    const reportElement = document.querySelector(reportSelectors.regions.report);
    return reportElement.querySelectorAll('[data-togglegroup="report-select-all"][data-toggle="slave"]:checked');
};
const getSelectAll = () => {
    const reportElement = document.querySelector(reportSelectors.regions.report);
    return reportElement.querySelectorAll('[data-togglegroup="report-select-all"]');
};

/**
 * Find a volunteer.
 * @param {Element} findVolunteerElement
 * @param {Integer} courseid
 */
const findVolunteer = async(findVolunteerElement, courseid) => {
    findVolunteerElement.setAttribute('disabled', true);
    const checkedUsers = getCheckedUsers();

    var userids = [];
    checkedUsers.forEach(function(user) {
        userids.push(user.value);
    });

    if (!checkedUsers.length) {
        toastAdd(getString('notenoughusers', 'local_volunteer'), {type: 'danger'});
        findVolunteerElement.removeAttribute('disabled');
        return;
    }

    // Get volunteer.
    var promises = Ajax.call([
        {methodname: 'local_volunteer_find_volunteer', args: {courseid: courseid, userids: userids}}
    ]);
    promises[0].done(function(result) {
        if (result.success) {
            Notification.alert(getString('volunteerfound', 'local_volunteer'), result.volunteerfullname);
        } else {
            toastAdd(result.errormessage, {type: 'danger'});
        }
    }).then(function() {
        findVolunteerElement.removeAttribute('disabled');
        return;
    }).fail(Notification.exception);
};

/**
 * Check all users.
 * @param {Integer} userid
 */
const checkAllUsersExceptOne = async(userid) => {
    const selectAll = await getSelectAll();
    selectAll.forEach(function(checkbox) {
        checkbox.checked = true;
    });
    uncheckUser(userid);
};

/**
 * Uncheck user.
 * @param {Integer} userid
 */
const uncheckUser = async(userid) => {
    const checkedUsers = await getCheckedUsers();
    checkedUsers.forEach(function(user) {
        if (user.value == userid) {
            user.checked = false;
        }
    });
};

/**
 * Init page
 * @param {Integer} courseid
 * @param {Integer} userid
 */
export function init(courseid, userid) {
    checkAllUsersExceptOne(userid);

    document.addEventListener('click', event => {
        // Find a volunteer.
        const findVolunteerElement = event.target.closest(SELECTORS.FINDVOLUNTEER);
        if (findVolunteerElement) {
            event.preventDefault();
            findVolunteer(findVolunteerElement, courseid);
        }
    });
}
