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
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Lightweight alternative to regular frontend requests based at typoscript_rendering extensions; used when $_GET[tx_typoscriptrendering] is set.
 */
class TypoScriptRenderingHandler implements MiddlewareInterface
{
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
        $TsRendering = $request->getParsedBody()['tx_typoscriptrendering'] ?? $request->getQueryParams()['tx_typoscriptrendering'] ?? null;

        if ($TsRendering === null) {
            return $handler->handle($request);
        }

        // Remove any output produced until now
        ob_clean();

        //prepare and return final output
        if ($GLOBALS['TSFE']->isINTincScript()) {
            $GLOBALS['TSFE']->INTincScript();
        }

        $response = GeneralUtility::makeInstance(Response::class);

        $response->getBody()->write($GLOBALS['TSFE']->content);

        return $response;
    }
}
