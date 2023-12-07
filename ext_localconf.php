<?php

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['TYPOSCRIPT_RENDERING'] = \Helhum\TyposcriptRendering\ContentObject\TypoScriptRenderingContentObject::class;
    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['requireCacheHashPresenceParameters'][] = 'tx_typoscriptrendering[context]';

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering'] = [];
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering']['renderClasses'] = [
        'record' => 'Helhum\\TyposcriptRendering\\Renderer\\RecordRenderer',
    ];

    // Ignore fake controller argument that gets removed
    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx__[controller]';
})();
