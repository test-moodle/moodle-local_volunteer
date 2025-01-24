# Volunteer [![Build Status](https://github.com/odeialba/moodle-local_volunteer/workflows/Moodle%20Plugin%20CI/badge.svg)](https://github.com/odeialba/moodle-local_volunteer/actions)

## Random Volunteer Selection for Teachers

The local_volunteer plugin simplifies the process of randomly selecting a volunteer in your Moodle course. Teachers can quickly choose from a list of course participants, ensuring a fair and engaging selection process.

With just a click of the "Find Volunteer" button, a modal will appear showing the name of the randomly selected user. It’s an easy and efficient way to keep students engaged and encourage participation in both small group settings and large classes.

Key Features:
- Display a table of all course users for easy selection.
- Randomly select a volunteer with a single click.
- Pop-up modal shows the selected volunteer’s name.
- Ensure fairness and randomness in volunteer selection.

Perfect for keeping your class dynamic and interactive!

## Installing via uploaded ZIP file

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/volunteer

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## TODO - Roadmap

- [X] Add select all checkbox.
- [X] Remove unused code.
- [X] Add the button to select user.
- [X] Uncheck the user executing the selection by default and check all the rest.
- [X] Allow filtering with groups.
- [ ] Add a history of previously selected users.
- [ ] Add a setting to allow the user to select the number of users to select.
- [ ] Send a notification to the selected user (add a setting to handle this).
- [ ] Pop-up a message in the screen of the selected user.
- [ ] Add a setting to allow the user to opt-in/opt-out themselves of the selection.
- [ ] More tests (PHPUnit and Behat).

## License

2025 Odei Alba <odeialba@odeialba.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
