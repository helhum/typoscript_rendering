<?php
namespace Helhum\TyposcriptRendering\ContentObject;

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

use Helhum\TyposcriptRendering\Exception;
use Helhum\TyposcriptRendering\Mvc\Request;
use Helhum\TyposcriptRendering\Mvc\RequestBuilder;
use Helhum\TyposcriptRendering\Mvc\Response;
use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use Helhum\TyposcriptRendering\Renderer\RenderingInterface;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class TypoScriptRenderingContentObject extends AbstractContentObject
{
    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var string[]
     */
    private $renderer;

    /**
     * @param RequestBuilder $requestBuilder
     * @param string[] $renderer
     */
    public function __construct(ContentObjectRenderer $cObj, RequestBuilder $requestBuilder = null, array $renderer = null)
    {
        $this->requestBuilder = $requestBuilder ?: new RequestBuilder();
        $this->renderer = $renderer ?: $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering']['renderClasses'];
    }

    public function render($conf = []): string
    {
        $renderingRequest = $this->requestBuilder->build($conf['request']);
        $response = new Response();

        $renderer = $this->resolveRenderer($renderingRequest);
        $renderingContext = new RenderingContext($GLOBALS['TSFE']);
        $renderer->renderRequest($renderingRequest, $response, $renderingContext);

        return $response->getContent();
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     * @return RenderingInterface
     *
     */
    protected function resolveRenderer(Request $request)
    {
        if ($request->hasArgument('renderer') && isset($this->renderer[$request->getArgument('renderer')])) {
            $rendererClassName = $this->renderer[$request->getArgument('renderer')];
            /** @var RenderingInterface $renderer */
            $renderer = new $rendererClassName();
            if ($renderer->canRender($request)) {
                return $renderer;
            }
        }

        foreach ($this->renderer as $rendererClassName) {
            /** @var RenderingInterface $renderer */
            $renderer = new $rendererClassName();
            if ($renderer->canRender($request)) {
                return $renderer;
            }
        }

        throw new Exception('No renderer found for this request!', 1403628294);
    }
}
