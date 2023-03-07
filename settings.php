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
 * Local plugin "college_course_templates" - Settings
 *
 * @package    local_college_course_templates
 * @copyright  2013 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$maxtemplates = get_config('local_college_course_templates', 'maxtemplates')+1;

if ($hassiteconfig) {
    // New settings page.
    $page = new admin_settingpage('college_course_templates', get_string('pluginname', 'local_college_course_templates', null, true));

    if ($ADMIN->fulltree) {

        // $page->add(new admin_setting_configtext('local_college_course_templates/usertour', get_string('usertour', 'local_college_course_templates'), get_string('usertour_desc', 'local_college_course_templates', null, true),''));

        // $page->add(new admin_setting_configselect('local_college_course_templates/threshold',
        //         get_string('threshold', 'local_college_course_templates', null, true),
        //         get_string('threshold_desc', 'local_college_course_templates', null, true), 1, range(0,15)));

        $page->add(new admin_setting_configselect('local_college_course_templates/maxtemplates',
                get_string('maxtemplates', 'local_college_course_templates', null, true),
                '', 5, range(1,12)));

        $possibletemplates = range(1,$maxtemplates);
        foreach ($possibletemplates as $pt) {
	        $page->add(new admin_setting_heading('local_college_course_templates/template'.$pt,
	                get_string('template'.$pt, 'local_college_course_templates', null, true),
	                ''));

	        $page->add(new admin_setting_configcheckbox('local_college_course_templates/enabletemplate'.$pt, get_string('enabletemplate'.$pt, 'local_college_course_templates'), '', 0));

	        $page->add(new admin_setting_configtext('local_college_course_templates/template'.$pt.'_title', get_string('template'.$pt.'_title', 'local_college_course_templates'), '', ''));

	        $page->add(new admin_setting_configstoredfile('local_college_course_templates/template'.$pt.'_thumb',
	                get_string('template'.$pt.'_thumb', 'local_college_course_templates', null, true),
	                get_string('template'.$pt.'_thumb_desc', 'local_college_course_templates', null, true),
	                'filearea'.$pt,
	                0,
	                array('maxfiles' => 1, 'accepted_types' => array('.jpg','.png'))));

	        $page->add(new admin_setting_confightmleditor('local_college_course_templates/template'.$pt.'_summ', get_string('template'.$pt.'_summ', 'local_college_course_templates'),
					get_string('template'.$pt.'_summ_desc', 'local_college_course_templates'), ''));

	        $page->add(new admin_setting_configtext('local_college_course_templates/tags'.$pt, get_string('tags', 'local_college_course_templates'), get_string('tags_desc', 'local_college_course_templates'), ''));

	        $page->add(new admin_setting_configtext('local_college_course_templates/template'.$pt.'_cid', get_string('template'.$pt.'_cid', 'local_college_course_templates'), '', ''));
	    }

    }

    // Add settings page to navigation tree.
    $ADMIN->add('courses', $page);
}
