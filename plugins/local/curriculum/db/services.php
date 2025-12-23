<?php
$functions = [
    'local_curriculum_delete_subject' => [
        'classname'   => 'local_curriculum\\external\\subject_external',
        'methodname'  => 'delete',
        'description' => 'Delete a subject',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'local/curriculum:manageplans',
    ],
    'local_curriculum_save_structure' => [
        'classname'   => 'local_curriculum\\external\\plan_external',
        'methodname'  => 'save_structure',
        'description' => 'Save plan structure ordering',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'local/curriculum:manageplans',
    ],
];
