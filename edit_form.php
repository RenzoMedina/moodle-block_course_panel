<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Form for editing course_panel block instances.
 *
 * @package     block_course_panel
 * @copyright   2026 Renzo Medina <medinast30@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_panel_edit_form extends block_edit_form {

    /**
     * Extends the configuration form for block_course_panel.
     *
     * @param MoodleQuickForm $mform The form being built.
     */
    protected function specific_definition($mform) {

        // Section header title.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Please keep in mind that all elements defined here must start with 'config_'.

        $mform->addElement('select',  'config_valueactivities',  get_string('labelvalueactivities', 'block_course_panel'),[
            7 => get_string('seven', 'block_course_panel'),
            14 => get_string('fourteen', 'block_course_panel'),
            21 => get_string('twentyone', 'block_course_panel'),
            28 => get_string('twentyeight', 'block_course_panel'),
        ]);
        $mform->addHelpButton('config_valueactivities', 'labelvalueactivities', 'block_course_panel');
        $mform->setDefault('config_valueactivities', 7);

        // Text field for the message to students.
        $mform->addElement('editor',  'config_messagestudent',  get_string('labeltext', 'block_course_panel'));
        $mform->setType('config_messagestudent', PARAM_TEXT);
        $mform->addHelpButton('config_messagestudent', 'labeltext', 'block_course_panel');
    }
}
