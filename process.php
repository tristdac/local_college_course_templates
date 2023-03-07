<?php
// This file is part of Moodle - http://moodle.org/
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
 * @package   local_college_course_templates
 * @copyright 2017 onwards, emeneo (www.emeneo.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('../../course/externallib.php');
require_once('../../lib/blocklib.php');
require('lib.php');
// require_capability('local/college_course_templates:view', context_system::instance());
require_login();
global $USER;

$target = optional_param('target', '', PARAM_INT);
$source = optional_param('source', '', PARAM_INT);
$wipeit = optional_param('wipeit', '', PARAM_INT);
$template = get_template(1, optional_param('template', '', PARAM_INT));

$target_c = $DB->get_record('course', ['id' => $target], '*', MUST_EXIST);
$source_c = $DB->get_record('course', ['id' => $source], '*', MUST_EXIST);

$pagetitle = get_string('pluginname', 'local_college_course_templates').'-processing';
$heading = 'Importing a template to '.$target_c->fullname;
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/college_course_templates/process.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);


require_capability('moodle/restore:restoretargetimport', context_course::instance($target));
require_capability('moodle/course:manageactivities', context_course::instance($target));
require_capability('moodle/backup:backuptargetimport', context_course::instance($source));

if (activities_exceed_threshold($courseid) === TRUE) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading);
    // echo '<script src="' . $CFG->wwwroot . '/local/college_course_templates/js/process.js"></script>';
    echo '<div id="returntocourse"><h4>Import cancelled</h4>It looks like someone has added content to the course page while you\'ve been here. For safetey, importing templates is only possible when less than <strong>'.$threshold.'</strong> activity(ies) or resourse(s) exists on the course page. The import has been cancelled.</br>';
    echo '<a id="returntocourse_btn" href="' . $CFG->wwwroot . '/course/view.php?id=' . $target . '&sesskey=' . $USER->sesskey . '&edit=off" role="button" aria-pressed="true" class="btn btn-warning">Return to course</a>';
    echo '</div>';
} else {

if ($wipeit == 1) {

    // We need to get the current courses enrolments so we don't lose users
//    $enrol = $DB->get_records('enrol', array('courseid' => $target));
//    // $enrolments = $DB->get_records('enrol', array('courseid' => $target));
//    $enrolids = $DB->get_fieldset_select('enrol', 'id', 'courseid = ?', array($target));
//    $ue = $DB->get_records('user_enrolements', 'enrolid IN ?', array(explode(',',$enrolids)));
//    print_object($enrolids);
    // $contents = core_course_external::get_course_contents($source);
    // print_object($contents);

     $target_cmids = $DB->get_fieldset_select('course_modules', 'id', 'course = ?', array($target));
     core_course_external::delete_modules($target_cmids);

     $target_ctx = context_course::instance($target);
     $target_blocks = $DB->get_records('block_instances', array('parentcontextid'=>$target_ctx->id));
     foreach ($target_blocks as $block) {
        blocks_delete_instance($block);
     }
 }

    // What do we want to overwrite apart from activities/blocks
    $target_c->format = $source_c->format;
    $target_c->summary = $source_c->summary;
    $target_c->groupmode = $source_c->groupmode;
    $target_c->theme = $source_c->theme;
    $target_c->enablecompletion = $source_c->enablecompletion;
    $target_c->completionnotify = $source_c->completionnotify;
    $DB->update_record('course', $target_c);


    // $options = array(
    // 	'id' => $target,
    //        'format' => $source_c->format,
    //        'showgrades' => $source_c->showgrades,
    //        'newsitems' => $source_c->newsitems,
    //        'numsections' => $source_c->numsections,
    //        'maxbytes' => $source_c->maxbytes,
    //        'showreports' => $source_c->showreports,
    //        'groupmode' => $source_c->groupmode,
    //        'groupmodeforce' => $source_c->groupmodeforce,
    //        'defaultgroupingid' => $source_c->defaultgroupingid,
    //        'enablecompletion' => $source_c->enablecompletion,
    //        'completionnotify' => $source_c->completionnotify,
    //        'lang' => $source_c->lang,
    //        'forcetheme' => $source_c->forcetheme
    //    );
    // core_course_external::get_courses(array($source[$source]));
    // core_course_external::update_courses(array($target));
// print_object($options);
// 	core_course_external::update_courses($options);

    // Remove any previous course format options
    $DB->delete_records('course_format_options', ['courseid' => $target]);

    // We need to get the templates course format options
    $source_c_opt = $DB->get_records('course_format_options', array('courseid' => $source));

    // Update course format options to match template
    foreach ($source_c_opt as $key => $option) {
        if ($option->courseid == $target) {
            continue;
        } else {
            $target_c_opt->courseid = $target;
            $target_c_opt->format = $option->format;
            $target_c_opt->sectionid = $option->sectionid;
            $target_c_opt->name = $option->name;
            $target_c_opt->value = $option->value;
            $DB->insert_record('course_format_options', $target_c_opt);
        }
    }

//}



    core_course_external::import_course($source, $target, 0);

// if ($wipeit == 1) {

// 	// rewrite enrolments & methods after wipe
// 	$DB->insert_records('enrol',$enrol);
// 	$DB->insert_records('enrol',$enrols);

// }

    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading);
    echo '<script src="' . $CFG->wwwroot . '/local/college_course_templates/js/process.js"></script>';
    // echo '
    //       <div class="row filter '.$tags.'">
    //         <div class="col-md-7">
    //           <a href="'.$CFG->wwwroot.'/course/view.php?id='.$template["templateid"].'">
    //             <img class="img-fluid rounded mb-3 mb-md-0 template_thumb" src="'.$template["thumb"].'" alt="Template '.$pt.'"><br>
    //           </a>
              
    //         </div>
    //         <div class="col-md-5">
    //           <h3>'.$template["title"].'</h3>
    //           <p>'.$template["summ"].'</p>
    //           <span class="tags clearfix">Tags: '.$template["tags"].'</span>
    //         </div>
    //       </div>';
    echo '<div class="progress">
  <div id="dynamic" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
    <span id="current-progress"></span>
  </div>
</div>';
    echo '<div id="gotocourse" hidden>' . get_string('process_success', 'local_college_course_templates');
    echo '<a id="gotocourse_btn" href="' . $CFG->wwwroot . '/course/view.php?id=' . $target . '&sesskey=' . $USER->sesskey . '&edit=off" role="button" aria-pressed="true" class="btn btn-primary">View course</a></br>';
    echo '</div>';
}

echo $OUTPUT->footer();
