<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [

    'local/curriculum:manageplans' => [
        'riskbitmask' => RISK_CONFIG,
        'captype'     => 'write',
        'contextlevel'=> CONTEXT_SYSTEM,
        'archetypes'  => [
            'manager' => CAP_ALLOW,
        ],
    ],

    'local/curriculum:manageareas' => [
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => [
            'manager' => CAP_ALLOW,
        ],
    ],

];
