# Course Panel #

I am an academic coordinator and I need a block that gives me a quick overview of the status of a course without having to navigate through several menus. I want to see the most relevant information about the current course in one place so I can make quick decisions.

## Features ##

The block must display different sections depending on the user's role: 

### All roles ###
- Course name 
- Course start and end dates 
- Days remaining (colored according to proximity, as in Challenge 3) 
- If the course has no end date, display a corresponding message

### Student ###

- Their percentage of progress in the course (as in Challenge 4) 
- Number of activities due in the next 7 days 
- A motivational message that can be configured in the block settings

### Teacher ###

- Total number of students enrolled in the course 
- Average progress of the group 
- Number of activities with a due date in the next 7 days

### Admin ###

- Everything the teacher sees 
- Number of hidden activities in the course

### Block settings ###
- The administrator can enable or disable each section separately
- The motivational message for students is configurable
- The administrator can define how many days are considered “upcoming” for activities (default is 7)

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/blocks/course_panel

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2026 Renzo Medina <medinast30@gmail.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
