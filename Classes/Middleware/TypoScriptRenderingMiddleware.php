<?php
namespace Helhum\TyposcriptRendering\Middleware;

/*
 * This file is part of the TypoScript Rendering TYPO3 extension.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 *
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Exception;

/**
 * Lightweight alternative to regular frontend requests based on typoscript_rendering extensions; used when $_GET[tx_typoscriptrendering] is set.
 */
class TypoScriptRenderingMiddleware implements MiddlewareInterface
{
    private const argumentNamespace = 'tx_typoscriptrendering';

    /**
     * Dispatches the request to the corresponding typoscript_rendering configuration
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @throws Exception
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $frontendController = $GLOBALS['TSFE'];
        if (!$frontendController->isGeneratePage() || !isset($request->getQueryParams()[self::argumentNamespace])) {
            return $handler->handle($request);
        }
        $this->ensureRequiredEnvironment();

        $frontendController->config['config']['disableAllHeaderCode'] = 1;
        $frontendController->pSetup = [
            '10' => 'TYPOSCRIPT_RENDERING',
            '10.' => [
                'request' => $request->getQueryParams()[self::argumentNamespace],
            ],
        ];

        return $handler->handle($request);
    }

    /**
     * @throws Exception
     * @return void
     *
     */
    private function ensureRequiredEnvironment()
    {
        if (empty($GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFoundOnCHashError'])) {
            throw new Exception('$GLOBALS[\'TYPO3_CONF_VARS\'][\'FE\'][\'pageNotFoundOnCHashError\'] needs to be enabled when using out of bound typoscript rendering!', 1403808246);
        }
        if (empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering']['renderClasses']) || !is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering']['renderClasses'])) {
            throw new Exception('No renderer found in configuration: $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'typoscript_rendering\'][\'renderClasses\']', 1403808247);
        }
        if (!in_array('tx_typoscriptrendering[context]', $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['requireCacheHashPresenceParameters'], true)) {
            throw new Exception('tx_typoscriptrendering[context] must be set as required cHash parameter', 1403808248);
        }
    }
}
