<?php

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_academicload';
$plugin->version   = 2026010500;
$plugin->requires  = 2025100601.00;
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.1';
$plugin->dependencies = [
    'local_coursebuilder' => 2025122701,
    'local_curriculum' => 2025121306
];