<?php
defined('TYPO3_MODE') or die();

call_user_func(function($packageKey) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][] = 'Helhum\\TyposcriptRendering\\RenderingDispatcher';
}, $_EXTKEY);