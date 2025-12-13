<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage(
        'local_curriculum',
        get_string('pluginname', 'local_curriculum')
    );

    $settings->add(new admin_setting_heading(
        'local_curriculum_desc',
        '',
        get_string('plugindescription', 'local_curriculum') .
        html_writer::empty_tag('br') .
        html_writer::empty_tag('br') .
        html_writer::link(
            new moodle_url('/local/curriculum/index.php'),
            get_string('enterplugin', 'local_curriculum'),
            ['class' => 'btn btn-primary']
        )
    ));

    $ADMIN->add('localplugins', $settings);
}
