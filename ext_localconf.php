<?php
defined('TYPO3_MODE') or die();

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['TYPOSCRIPT_RENDERING'] = \Helhum\TyposcriptRendering\ContentObject\TypoScriptRenderingContentObject::class;
    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['requireCacheHashPresenceParameters'][] = 'tx_typoscriptrendering[context]';

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering'] = [];
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering']['renderClasses'] = [
        'record' => 'Helhum\\TyposcriptRendering\\Renderer\\RecordRenderer',
    ];
})();
