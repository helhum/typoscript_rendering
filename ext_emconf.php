<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'TypoScript Rendering',
    'description' => 'Can render a TypoScript snippet by URL, especially useful for Ajax dispatching',
    'category' => 'Rendering',
    'author' => 'Helmut Hummel',
    'author_email' => 'info@helmut-hummel.de',
    'author_company' => 'Helmut Hummel',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '1.0.5',
    'constraints' =>
        [
            'depends' =>
                [
                    'php' => '5.5.0-7.0.999',
                    'typo3' => '6.2.0-8.99.99',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                ],
        ],
];
