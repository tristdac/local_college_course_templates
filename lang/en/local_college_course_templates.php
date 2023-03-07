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
global $CFG;

$string['pluginname'] = 'College Course Templates';
$string['useastemplate'] = 'Use as template';
$string['continue'] = 'Continue';
$string['parametererror'] = 'Parameter error';
$string['coursename'] = 'Course name';
$string['courseshortname'] = 'Course short name';
$string['createsuccess'] = 'Create a new course successfully';
$string['createfailed'] = 'There was a problem. Did you use a course name which already exist? Please try again.';
$string['useatemplate'] = 'Import template';
$string['managetemplates'] = 'Manage course templates';
$string['choosetemplate'] = 'Please choose your template';
$string['choosecategory'] = 'Please select your course category';
$string['inputinfo'] = 'Please enter below infomation';
$string['inputinfotip'] = 'Please enter new course infomation';
$string['course_templates:view'] = 'View course template';
$string['course_templates:edit'] = 'Edit course template';
$string['hiddencategories'] = 'Hidden categries';
$string['maxtemplates'] = 'How many templates?';
$string['usertour'] = 'User Tour ID';
$string['usertour_desc'] = 'The ID of the usertour to trigger on new or blank courses (determined by "Threshold" below.';
$string['tags'] = 'Template tags';
$string['tags_desc'] = 'example... "Open Learning / MOOC / Distance / Remote"';
$string['threshold'] = 'Activity Count Threshold';
$string['threshold_desc'] = 'The number of activities on the course page for which we can class a course as "undeveloped". Use this number to determine when to show the template tour to course teachers. For example, if "1" is chosen here, the teacher will be prompted to import if less than 1 activity exists on the course.';
$string['process_success'] = '<h4>Success</h4><p>The template has been successfully restored into your course.</p><p>If you require further assistance or guidance in regards to editing your course, creating or uploading learning materials, please contact your <a href="'.$CFG->wwwroot.'" target="_blank">local Learning Technologist</a>.</p>';
$string['confirmationofwipe'] = 'Wiping your course will remove all blocks, activities, resources, course format, existing grades and feedback, overwriting with those included in the template. Users, groups, course name and dates will not be affected.<br><br><strong>Overwritten settings and items can not be recovered</strong></br></br>Are you sure you want to do this?';
$possibletemplates = range(1,12);
foreach ($possibletemplates as $pt) {
	$string['template'.$pt] = 'Template '.$pt.'';
	$string['template'.$pt.'_title'] = 'Template '.$pt.' Title';
	$string['template'.$pt.'_thumb'] = 'Template '.$pt.' thumbnail';
	$string['template'.$pt.'_thumb_desc'] = 'Upload a representative screenshot of template '.$pt.'. JPG/PNG XXXpx x XXXpx';
	$string['template'.$pt.'_summ'] = 'Template '.$pt.' Summary';
	$string['template'.$pt.'_summ_desc'] = 'Enter a description of the template';
	$string['enabletemplate'.$pt] = 'Enable Template '.$pt;
	$string['template'.$pt.'_cid'] = 'Template '.$pt.' course ID #';
}