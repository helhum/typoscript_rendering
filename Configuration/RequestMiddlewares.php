<?php

return [
    'frontend' => [
        'helhum/typoscript-rendering-handler' => [
            'target' => Helhum\TyposcriptRendering\Middleware\TypoScriptRenderingHandler::class,
            'description' => '',
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
        ],
    ],
];
