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
    private const defaultContentType = 'text/html';

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
        $requestedContentType = $frontendController->config['config']['contentType'] ?? self::defaultContentType;
        if (!$frontendController->isGeneratePage() || !isset($request->getQueryParams()[self::argumentNamespace])) {
            return $this->amendContentType($handler->handle($request), $requestedContentType);
        }
        $this->ensureRequiredEnvironment();

        $frontendController->config['config']['debug'] = 0;
        $frontendController->config['config']['disableAllHeaderCode'] = 1;
        $frontendController->config['config']['disableCharsetHeader'] = 0;
        $frontendController->pSetup = [
            '10' => 'TYPOSCRIPT_RENDERING',
            '10.' => [
                'request' => $request->getQueryParams()[self::argumentNamespace],
            ],
        ];

        return $this->amendContentType($handler->handle($request), $requestedContentType);
    }

    /**
     * TYPO3's frontend rendering allows to influence the content type,
     * but does not store this information in cache, which leads to wrong content type
     * to be sent when content if pulled from cache.
     * We add a tiny workaround, that allows plugins to set the content type, but also
     * store the content type in cache:
     *
     * $GLOBALS['TSFE']->setContentType('application/json');
     * $GLOBALS['TSFE']->config['config']['contentType'] = 'application/json';
     *
     * @param ResponseInterface $response
     * @param string $requestedContentType
     * @return ResponseInterface
     */
    private function amendContentType(ResponseInterface $response, string $requestedContentType): ResponseInterface
    {
        $originalContentTypeHeader = $response->getHeader('Content-Type')[0] ?? '';
        if (strpos($originalContentTypeHeader, self::defaultContentType) === 0 && strpos($originalContentTypeHeader, $requestedContentType) === false) {
            $response = $response->withHeader('Content-Type', \str_replace(self::defaultContentType, $requestedContentType, $originalContentTypeHeader));
        }

        return $response;
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
