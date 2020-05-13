<?php
$EM_CONF[$_EXTKEY] = [
  'title' => 'TypoScript Rendering',
  'description' => 'Can render a TypoScript path by URL, especially useful for Ajax dispatching',
  'category' => 'Rendering',
  'author' => 'Helmut Hummel',
  'author_email' => 'info@helmut-hummel.de',
  'author_company' => 'helhum.io',
  'state' => 'stable',
  'uploadfolder' => '0',
  'createDirs' => '',
  'clearCacheOnLoad' => 0,
  'version' => '2.3.0',
  'constraints' => [
    'depends' => [
      'php' => '7.2.0-7.4.999',
      'typo3' => '9.5.0-10.4.99',
    ],
    'conflicts' => [
    ],
    'suggests' => [
    ],
  ],
];
