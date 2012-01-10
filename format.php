<?php
      // format.php - course format featuring single activity
      //              included from view.php


    $supportedmodules = array('scorm'); //TODO: make this automated by checking for modules that have a function _course_format_display
    $modules = array();
    foreach ($supportedmodules as $supportedmodule) {
        $modules[$supportedmodule] = get_string('pluginname', $supportedmodule);
    }
    if (count($modules) > 1) {
        //check to see if a module is set in db
        $module = $DB->get_field('config_plugins', 'value', array('plugin'=> 'format_singlemod', 'name'=>$COURSE->id));
        if (empty($module)) {
            //check form post
            $choosemodule = optional_param('choosesinglemod', '', PARAM_ALPHANUM);
            $sesskey = optional_param('sesskey', '', PARAM_RAW);
            if (!empty($choosemodule) && confirm_sesskey($sesskey)) {
                //check plugin exists first
                if (file_exists($CFG->dirroot.'/mod/'.$choosemodule.'/locallib.php')) {
                    set_config($COURSE->id, $choosemodule, 'format_singlemod');
                    $module = $choosemodule;
                } else {
                    error("invalidmoduleprovided");
                }
            } else {
                //show form and allow the user to select which module to use.
                echo '<form id="choosemodform" method="post" action="' . $PAGE->url->out(false) .'">';
                echo html_writer::select($modules, 'choosesinglemod', '', '');
                echo '<input type="submit" value="'.get_string('choose').'"/>';
                echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
                echo '</form>';
                echo $OUTPUT->footer();
                exit;
            }
        }
    } else {
        $module = reset($supportedmodules);
    }


    require_once($CFG->dirroot.'/mod/'.$module.'/locallib.php');

    $strgroups  = get_string('groups');
    $strgroupmy = get_string('groupmy');
    $editing    = $PAGE->user_is_editing();

    $moduleformat = $module.'_course_format_display';
    if (function_exists($moduleformat)) {
        $moduleformat($USER, $course);
    } else {
        echo $OUTPUT->notification('The module '. $module. ' does not support single activity course format');
    }
