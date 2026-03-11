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
 * Block course_panel is defined here.
 *
 * @package     block_course_panel
 * @copyright   2026 Renzo Medina <medinast30@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_panel extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_course_panel');
    }

    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = [];
        $this->content->icons = [];
        $this->content->footer = '';

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            global $OUTPUT, $COURSE, $USER;

            //this section is for date also startdate and enddate, also calculate the remaining days to end of course.
            $startdatelabel = get_string('startdate', 'block_course_panel', userdate($COURSE->startdate, get_string('strftimedatefullshort', 'langconfig')));
            $enddatelabel = get_string('enddate', 'block_course_panel', userdate($COURSE->enddate, get_string('strftimedatefullshort', 'langconfig')));
            $enddate = $COURSE->enddate;
            $now = time();
            $secondsremaining = $COURSE->enddate - $now;
            $dayremaining = (int)($secondsremaining / DAYSECS);
            $dayslabel = get_string('dayslabel', 'block_course_panel', $dayremaining);
            $colors = '';
            if ($dayremaining >= 30) {
                $colors = 'success';
            } else if ($dayremaining > 10) {
                $colors = 'warning';
            } else {
                $colors = 'danger';
            }
            $coursedate = [
                'fullname' => $COURSE->fullname,
                'startdatelabel' => $startdatelabel,
                'enddatelabel' => !empty($enddatelabel) ? $enddatelabel : get_string('noenddate', 'block_course_panel'),
                'dayslabel' => $dayslabel,
                'colors' => $colors,
            ];

            //this section is for data student, teacher and admin
            $completion = new completion_info($COURSE);
            if(!$completion->is_enabled()) {
                $this->content->text = $OUTPUT->render_from_template('block_course_panel/main', [
                'progress' => false,
                'message'  => get_string('completionnotenabled', 'block_course_panel'),
            ]);
                return $this->content;
            }
            $modinfo = get_fast_modinfo($COURSE);
            $total = 0;
            $completed = 0;
            foreach ($modinfo->cms as $cm) {
                if ($cm->completion == COMPLETION_TRACKING_NONE) {
                    continue;
                }
                
                $total++;
                
                $details = \core_completion\cm_completion_details::get_instance(
                    $cm,
                    $USER->id,
                    true
                );
                
                if ($details->is_overall_complete()) {
                    $completed++;
                }
            }
            $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
            $messagestudent = !empty($this->config->messagestudent['text']) ? $this->config->messagestudent['text'] : get_string('defaultmessage', 'block_course_panel');
            $timestart = time();
            $dayremaining = isset($this->config->valueactivities) ? $this->config->valueactivities : 7;
            $timeend = $timestart + ((int)$dayremaining * DAYSECS);
            $coursefilter = [$COURSE->id];
            $events = \core_calendar\local\api::get_events(null, null, $timestart,$timeend, null, null, 20, null, null, null, $coursefilter);
            $activitiesstudents = count($events);
            $activitiesstudentslabel = get_string('activitiesstudent', 'block_course_panel',$activitiesstudents);

            // this a section for teacher and admin, also calculate the average of completion of students, also calculate the total of activities and hidden activities.
            $context = \context_course::instance($COURSE->id);
            $isteacher = has_capability('moodle/grade:viewall', $context);
            $isadmin = has_capability('moodle/site:config', context_system::instance());

            //this section is for calculate the average of completion of students
            $studentsactive = count_enrolled_users($context, 'moodle/course:isincompletionreports');
            $averagecompletion = 0;
            $groupaverage = 0;
            $liststudents = get_enrolled_users($context, 'moodle/course:isincompletionreports');
            $totalstudents = 0;
            foreach ($liststudents as $student) {
                $studencompleted = 0;
                foreach ($modinfo->cms as $cm) {
                    if ($cm->completion == COMPLETION_TRACKING_NONE) {
                        continue;
                    }
                    $details = \core_completion\cm_completion_details::get_instance(
                        $cm,
                        $student->id,
                        true
                    );
                    if ($details->is_overall_complete()) {
                        $studencompleted++;
                }
                
                }
            $average = $total > 0 ? round(($studencompleted / $total) * 100) : 0;
            $averagecompletion += $average;
            $totalstudents++;
        }
            $groupaverage = $totalstudents > 0 ? round($averagecompletion / $totalstudents) : 0;
            
            //this section is for calculate the total of hidden activities
            $activitieshidden = 0;
            foreach ($modinfo->cms as $cm) {
                if ($cm->visible) {
                    continue;
                    }
                $activitieshidden++;
            }

            //this a section data of template
            $template = [
                'coursesinfo' => $coursedate,
                'endate' => !empty($enddate),
                'isstudent' => !$isteacher && !$isadmin,
                'isteacheradmin' => $isadmin || $isteacher,
                'isadmin' => $isadmin,
                'progress' => $percentage < 100,
                'finish' => $percentage >= 100,
                'messagefinish' => get_string('messagefinish', 'block_course_panel'),
                'percentage' => $percentage,
                'studentmessage' => $messagestudent,
                'isactivities' => $activitiesstudents > 1,
                'activitiesstudent' => $activitiesstudentslabel,
                'countsutendts' => $studentsactive,
                'average' => $groupaverage,
                'activities' => $activitiesstudents > 1,
                'activitieshidden' => $activitieshidden > 1 ? $activitieshidden : 0,
                ];
            $this->content->text = $OUTPUT->render_from_template('block_course_panel/main', $template);
        }

        return $this->content;
    }
    /**
     * Defines configuration data.
     *
     * The function is called immediately after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_course_panel');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    public function has_config() {
        return true;
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
    public function applicable_formats() {
        return [
            'course-view' => true,
        ];
    }
    /**
     * Performs a self-test to check if the block is working correctly.
     * @return bool
     */
    function _self_test() {
        return true;
    }
}
