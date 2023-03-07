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
 
defined('MOODLE_INTERNAL') || die;


function get_tag_groups() {
    $maxtemplates = get_config('local_college_course_templates', 'maxtemplates')+1;
    $possibletemplates = range(1,$maxtemplates);
    $taglists = array();
    $taglines = array();
    $tags = array();
    $tag_groups = array();
    foreach ($possibletemplates as $pt) {
        $isenabled = get_config('local_college_course_templates', 'enabletemplate'.$pt);
        if ($isenabled == 1) {
            $template = get_template(1, $pt);
            $taglists[] .= $template["tags"];
        }
    }
    foreach ($taglists as $taglist) {
        $tag_groups[] = explode(" / ",$taglist);
    }
    return $tag_groups;
}

function get_tags() {
    $tag_groups = get_tag_groups();
    return call_user_func_array('array_merge', $tag_groups);
}

function get_template_tags($template) {
    $template_tags = get_config('local_college_course_templates', 'tags'.$template);
    $tags = str_replace(" ","",$template_tags);
    $tags = str_replace("/"," ",$tags);
    $tags = strtolower($tags);
    return $tags;
}



function print_tag_filter_buttons() {
    $tags = get_tags();
    $tags = array_unique($tags);
    asort($tags);
    $output = \html_writer::start_tag('div', ['class' => 'filter_buttons_container']);
    $output .= html_writer::tag('button', 'All', array('href' => '#','id' => 'showall','class' => 'btn btn-info filter-button','data-filter' => 'all'));
    foreach ($tags as $tag) {
        $clean_tag = str_replace(" ","",$tag);
        $clean_tag = strtolower($clean_tag);
        $output .= html_writer::tag('button', $tag, array('href' => '#','id' => $clean_tag,'class' => 'btn btn-info filter-button','data-filter' => $clean_tag));
    }
    $output .= \html_writer::end_tag('div');
    return $output;
}



function get_course_teacher_activity($course) {
    global $DB;
    $count = $DB->count_records('course_modules', array('course'=>$course,'deletioninprogress'=>0));
    return $count;
}


function local_college_course_templates_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=[]) {
    global $CFG, $DB;
    require_login($course, false, $cm);
    // if ($filearea !== 'filearea') {
    //     return false;
    // }
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/local_college_course_templates/$filearea/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    // Finally send the file.
    send_stored_file($file, null, 0, $forcedownload, $options);
}

function get_template($pagectxid, $tid) {
    $template = array();

    // Get Title
    $title = 'template'.$tid.'_title';
    $template['title'] = get_config('local_college_course_templates', $title);

    // Get Thumb
    $filearea = 'filearea'.$tid;
    $url = '';
    $fs = get_file_storage();
    $files = $fs->get_area_files($pagectxid, 'local_college_course_templates', $filearea);
    foreach ($files as $file) {
        if ($file->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
        }
    }
    $template['thumb'] = $url;

    // Get Summary
    $summ = 'template'.$tid.'_summ';
    $template['summ'] = get_config('local_college_course_templates', $summ);

    // Get tags
    $tags = 'tags'.$tid;
    $template['tags'] = get_config('local_college_course_templates', $tags);

    // Get course id for template
    $templateid = 'template'.$tid.'_cid';
    $template['templateid'] = get_config('local_college_course_templates', $templateid);

    return $template;

}

function get_template_course_ids() {
    $ids = array();
    $range = range(1,20);
    foreach ($range as $key => $t) {
        if (get_config('local_college_course_templates', 'template'.$t.'_cid')) {
            $ids[] .= get_config('local_college_course_templates', 'template'.$t.'_cid');
        }
    }
    return $ids;
}

function course_is_template($courseid) {
    $template_cids = get_template_course_ids();
    if (in_array($courseid,$template_cids)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function activities_exceed_threshold($courseid) {
    $count = get_course_teacher_activity($courseid);
    $threshold = get_config('local_college_course_templates', 'threshold');
    if ($count < $threshold) {
        return FALSE;
    } else if ($count >= $threshold) {
        return TRUE;
    }
}



function local_college_course_templates_extends_navigation(global_navigation $navigation) {
    local_college_course_templates_extend_navigation($navigation);
}



function local_college_course_templates_extend_settings_navigation(settings_navigation $settingsnav, context $context){
    global $PAGE;
    $url = new moodle_url('/local/college_course_templates/index.php', array(
        'contextid' => $context->id
    ));
    $courseid = $PAGE->course->id;
    if (course_is_template($courseid) === FALSE) {
        if (strpos($PAGE->pagetype, 'course-view') !== false) {
            if ( has_capability('moodle/course:update', context_course::instance($courseid)) &&  $courseid > 1 ) {
                $pluginname = get_string('pluginname', 'local_college_course_templates');
                $settingnode = $settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);

                if (($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) && ($courseid != '1')) {
                    $setMotdMenuLbl = get_string('useatemplate', 'local_college_course_templates');
                    $setMotdUrl = new moodle_url('/local/college_course_templates/index.php', array('cid' => $courseid));
                    $setMotdnode = navigation_node::create(
                        $setMotdMenuLbl,
                        $setMotdUrl,
                        navigation_node::NODETYPE_LEAF,
                        'local_college_course_templates',
                        'local_college_course_templates',
                        new pix_icon('clone', $pluginname, 'local_college_course_templates'));

                    if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
                        $node->make_active();
                    }

                    $settingnode->add_node($setMotdnode);
                }
            }
        }
    }
}

function local_college_course_templates_extend_navigation(global_navigation $nav){
    global $CFG, $PAGE;
    $courseid = $PAGE->course->id;
    if (strpos($PAGE->pagetype, 'course-view') !== false) {
        if ( (course_is_template($courseid) === FALSE) && (activities_exceed_threshold($courseid) === FALSE && (has_capability('moodle/course:update', context_course::instance($courseid))) &&  $courseid > 1) ) {
        $previewnode = $PAGE->navigation->add('Courses', new moodle_url($CFG->wwwroot.'course/index.php'), navigation_node::TYPE_CONTAINER);
        $thingnode = $previewnode->add($PAGE->heading, new moodle_url(''));
        $thingnode->make_active();
    
    
            // echo '<script src="'.$CFG->wwwroot.'/local/college_course_templates/js/tour.js"></script>';
            }
    }
}


/**
 * Map icons for font-awesome themes.
 */
function local_college_course_templates_get_fontawesome_icon_map() {
    return [
        'local_college_course_templates:clone' => 'fa-clone'
    ];
}



