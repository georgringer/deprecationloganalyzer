<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Analyze the deprecation log',
    'description' => 'Filter out the duplicates of the log file because it is easier to check 11 messages than a 400mb log file.',
    'category' => 'be',
    'version' => '2.0.0',
    'state' => 'stable',
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'constraints' => [
        'depends' => [
            'typo3' => '6.2.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];
