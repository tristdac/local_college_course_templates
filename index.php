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

define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require('../../course/externallib.php');
require('lib.php');
global $PAGE, $USER, $CFG;
$cid = required_param('cid', PARAM_INT);

// Load the course and context
$course = $DB->get_record('course', array('id'=>$cid), '*', MUST_EXIST);
$coursecontext = context_course::instance($cid);
$context = context_system::instance();

// Must pass login
require_login($course);

// Must hold restoretargetimport in the current course
require_capability('moodle/restore:restoretargetimport', $coursecontext);
require_capability('moodle/course:manageactivities', $coursecontext);
// require_capability('moodle/backup:backuptargetimport', $coursecontext);
// require_capability('local/college_course_templates:view', $coursecontext);

$tourid = get_config('local_college_course_templates', 'usertour');

$pagetitle = get_string('pluginname', 'local_college_course_templates');
$maxtemplates = get_config('local_college_course_templates', 'maxtemplates')+1;
$possibletemplates = range(1,$maxtemplates);
$template = array();
$course = $DB->get_field('course', 'fullname', array('id'=>$cid), MUST_EXIST);
$heading = 'Import a template into <strong>'.$course.'</strong>';

// Overwrite link options
if (activities_exceed_threshold($cid) === TRUE) {
	$threshold = get_config('local_college_course_templates', 'threshold');
	$ow_btn = '<a class="dropdown-item disabled" data-toggle="tooltip" data-placement="left" data-html="true" title="<large>Disabled</large><br><em>Cannot wipe courses with <b>'.$threshold.'</b> or more activities.</em>">Wipe & Import</a>';
} 

$PAGE->set_context($context);
$PAGE->set_url('/local/college_course_templates/index.php', array('cid'=>$cid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading('Import a template');

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

// echo $OUTPUT->notification('This is a notification - $cid = '.$cid);
echo '<div id="btn_container"><a href="'.$CFG->wwwroot.'/course/view.php?id='.$cid.'" role="button" aria-pressed="true" class="btn btn-warning">Return to course</a>';

if (is_siteadmin($USER)) {
	echo '<a id="cct_admin_settings" href="'.$CFG->wwwroot.'/admin/settings.php?section=college_course_templates" class="btn btn-dark"><i class="fa fa-cog"></i> Settings</a>';
}
echo '</div>';
echo '<div class="container">';

echo (print_tag_filter_buttons());
foreach ($possibletemplates as $pt) {
	$isenabled = get_config('local_college_course_templates', 'enabletemplate'.$pt);
	if ($isenabled == 1) {
		$template = get_template(1, $pt);
		$tags = get_template_tags($pt);

		
		$import_btn = '<form method="POST" name="myForm" action="../../backup/import.php">
		        <input type="hidden" name="id" value="'.$cid.'">
		        <input type="hidden" name="stage" value="1">
		        <input type="hidden" name="backup" value="tar'.$cid.'tem'.$template["templateid"].'t'.time().'">
		        <input type="hidden" name="importid" value="'.$template["templateid"].'">
		        <input type="hidden" name="sesskey" value="'.$USER->sesskey.'">
		        <input type="hidden" name="_qf__backup_initial_form" value="1">
		        <input type="hidden" name="setting_root_users" value="0">
		        <input type="hidden" name="setting_root_activities" value="1">
		        <input type="hidden" name="setting_root_blocks" value="0">
		        <input type="hidden" name="setting_root_filters" value="1">
		        <input type="hidden" name="setting_root_calendarevents" value="0">
		        <input type="hidden" name="setting_root_questionbank" value="1">
		        <input type="hidden" name="competencies" value="1">
		        <input type="hidden" name="submitbutton" value="Next">
			  <input type="submit" value="Import elements" class="notabutton dropdown-item" onclick="submitform()" style="cursor:pointer;">
			</form>';
		// create overwrite button dependant on target activity count and disable if necessary
		if (activities_exceed_threshold($cid) === FALSE) {
			$ow_btn = '<a class="dropdown-item" href="#" data-toggle="modal" data-target="#ow_confirm'.$pt.'">Wipe & Import</a>';
		}
		echo '
	      <div class="row filter '.$tags.'">
	        <div class="col-md-7">
	          <a href="'.$CFG->wwwroot.'/course/view.php?id='.$template["templateid"].'">
	            <img class="img-fluid rounded mb-3 mb-md-0 template_thumb" src="'.$template["thumb"].'" alt="Template '.$pt.'"><br>
	          </a>
	          
	        </div>
	        <div class="col-md-5">
	          <h2>'.$template["title"].'</h2>
	          <p>'.$template["summ"].'</p>
	          <span class="tags clearfix">Tags: '.$template["tags"].'</span>
	          <a class="btn btn-primary" href="'.$CFG->wwwroot.'/course/view.php?id='.$template["templateid"].'" target="_blank">View Template</a>
	          <div class="dropdown">
		          <button class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    Use this template
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						'.$ow_btn.'
		          		<a class="dropdown-item" href="#" data-toggle="modal" data-target="#merge_confirm'.$pt.'">Merge into your course</a>
		          		'.$import_btn.'
		          	</div>
		        </div>
	        </div>
	      </div>';

		// echo '<hr style="margin:20px;">';

		// overwrite confirmation modal
		echo '<div class="modal fade confirm" id="ow_confirm'.$pt.'" tabindex="-1" role="dialog" aria-labelledby="template'.$pt.'_ow" aria-hidden="true">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="template'.$pt.'_ow">Confirm Overwrite Course</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">';
		echo $OUTPUT->notification('<h3><i class="fa fa-warning"></i> DANGER</h3>');

		echo get_string('confirmationofwipe','local_college_course_templates');
		echo '<br><br>';
		echo '<div class="confirm_container">';
		echo '<form name="proceed" action="process.php">
		<input id="target" name="target" type="hidden" value="'.$cid.'">
		<input id="source" name="source" type="hidden" value="'.$template["templateid"].'">
		<input id="template" name="template" type="hidden" value="'.$pt.'">
		<input id="wipeit" name="wipeit" type="hidden" value="1">
		<button id="begin_import" type="submit" value="Yes, I understand" class="btn btn-danger">Yes, I understand</button>
		</form>';

		echo '<button type="button" class="btn btn-dark" data-dismiss="modal">No, get me out of here!</button></div>
				      </div>
				      <div class="modal-footer">

				      </div>
				    </div>
				  </div>
				</div>';


		// merge confirmation modal
		echo '<div class="modal fade confirm" id="merge_confirm'.$pt.'" tabindex="-1" role="dialog" aria-labelledby="template'.$pt.'merge" aria-hidden="true">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="template'.$pt.'merge">Confirm Merged Import</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        Are you sure?
			        <br><br>';

		echo '<div class="confirm_container">';
		echo '<form name="proceed" action="process.php">
		<input id="target" name="target" type="hidden" value="'.$cid.'">
		<input id="source" name="source" type="hidden" value="'.$template["templateid"].'">
		<input id="template" name="template" type="hidden" value="'.$pt.'">
		<input id="wipeit" name="wipeit" type="hidden" value="0">
		<input type="submit" value="Yes, I understand" class="btn btn-danger">
		</form>';

		echo '<button type="button" class="btn btn-dark" data-dismiss="modal">No, get me out of here!</button></div>
				      </div>
				      <div class="modal-footer">
				        
				      </div>
				    </div>
				  </div>
				</div>';
	}
}

// echo '<ul class="pagination justify-content-center">
//         <li class="page-item">
//           <a class="page-link" href="#" aria-label="Previous">
//             <span aria-hidden="true">&laquo;</span>
//             <span class="sr-only">Previous</span>
//           </a>
//         </li>
//         <li class="page-item">
//           <a class="page-link" href="#">1</a>
//         </li>
//         <li class="page-item">
//           <a class="page-link" href="#">2</a>
//         </li>
//         <li class="page-item">
//           <a class="page-link" href="#">3</a>
//         </li>
//         <li class="page-item">
//           <a class="page-link" href="#" aria-label="Next">
//             <span aria-hidden="true">&raquo;</span>
//             <span class="sr-only">Next</span>
//           </a>
//         </li>
//       </ul>

//     </div>';

echo '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$cid.'" role="button" aria-pressed="true" class="btn btn-warning">Return to course</a></br>';
echo '<script src="'.$CFG->wwwroot.'/local/college_course_templates/js/templates.js"></script>';

echo $OUTPUT->footer();