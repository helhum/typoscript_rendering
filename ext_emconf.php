<?php
$EM_CONF[$_EXTKEY] = [
  'title' => 'TypoScript Rendering',
  'description' => 'Can render a TypoScript path by URL, especially useful for Ajax dispatching',
  'category' => 'Rendering',
  'author' => 'Helmut Hummel',
  'author_email' => 'info@helhum.io',
  'author_company' => 'helhum.io',
  'state' => 'stable',
  'uploadfolder' => '0',
  'createDirs' => '',
  'clearCacheOnLoad' => 0,
  'version' => '2.4.0',
  'constraints' => [
    'depends' => [
      'php' => '7.2.0-8.999.999',
      'typo3' => '9.5.31-11.5.99',
    ],
    'conflicts' => [
    ],
    'suggests' => [
    ],
  ],
];
