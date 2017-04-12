<?php
defined('TYPO3_MODE') or die();

call_user_func(function ($packageKey) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkDataSubmission'][$packageKey] = 'Helhum\\TyposcriptRendering\\RenderingDispatcher';
    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['requireCacheHashPresenceParameters'][] = 'tx_typoscriptrendering[context]';

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering'] = array();
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering']['renderClasses'] = array(
        'record' => 'Helhum\\TyposcriptRendering\\Renderer\\RecordRenderer',
    );
}, $_EXTKEY);
