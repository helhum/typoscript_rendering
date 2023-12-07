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
  'version' => '2.5.0',
  'constraints' => [
    'depends' => [
      'php' => '7.2.0-8.999.999',
      'typo3' => '12.4.0-12.4.99',
    ],
    'conflicts' => [
    ],
    'suggests' => [
    ],
  ],
];
