<?php
namespace Helhum\TyposcriptRendering;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Helmut Hummel <helmut.hummel@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the text file GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Helhum\TyposcriptRendering\Core\FrontendRenderingProvisioner;
use Helhum\TyposcriptRendering\Mvc\Request;
use Helhum\TyposcriptRendering\Mvc\RequestBuilder;
use Helhum\TyposcriptRendering\Mvc\Response;
use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use Helhum\TyposcriptRendering\Renderer\RenderingInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class TypoScriptRenderer
 */
class RenderingDispatcher
{
    /**
     * @var string
     */
    protected $argumentNamespace = 'tx_typoscriptrendering';

    /**
     * @var RequestBuilder
     */
    protected $requestBuilder;

    /**
     * @var array
     */
    protected $renderer = array();

    /**
     * @param RequestBuilder $requestBuilder
     * @param array $renderer
     */
    public function __construct(RequestBuilder $requestBuilder = null, array $renderer = null)
    {
        $this->requestBuilder = $requestBuilder ?: new RequestBuilder();
        $this->renderer = $renderer ?: $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typoscript_rendering']['renderClasses'];
    }

    /**
     * @param TypoScriptFrontendController $typoScriptFrontendController
     */
    public function checkDataSubmission(TypoScriptFrontendController $typoScriptFrontendController)
    {
        // Do not do anything in this hook, if there are no parameters
        if ($typoScriptFrontendController->isGeneratePage() && GeneralUtility::_GET($this->argumentNamespace)) {
            $this->ensureRequiredEnvironment();

            $frontendRenderingProvisioner = new FrontendRenderingProvisioner();
            $frontendRenderingProvisioner->provision($typoScriptFrontendController);

            $request = $this->requestBuilder->build(GeneralUtility::_GET($this->argumentNamespace));
            $response = new Response();

            $renderer = $this->resolveRenderer($request);
            $renderingContext = new RenderingContext($typoScriptFrontendController);
            $renderer->renderRequest($request, $response, $renderingContext);

            $typoScriptFrontendController->content = $response->getContent();
            $typoScriptFrontendController->config['config']['pageGenScript'] = 'EXT:typoscript_rendering/Scripts/DummyRendering.php';
        }
    }

    /**
     * @throws Exception
     */
    protected function ensureRequiredEnvironment()
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

    /**
     * @param Request $request
     * @return RenderingInterface
     * @throws Exception
     */
    protected function resolveRenderer(Request $request)
    {
        /** @var RenderingInterface $renderer */
        if ($request->hasArgument('renderer') && isset($this->renderer[$request->getArgument('renderer')])) {
            $rendererClassName = $this->renderer[$request->getArgument('renderer')];
            $renderer = new $rendererClassName();
            if ($renderer->canRender($request)) {
                return $renderer;
            }
        }

        foreach ($this->renderer as $rendererClassName) {
            $renderer = new $rendererClassName();
            if ($renderer->canRender($request)) {
                return $renderer;
            }
        }

        throw new Exception('No renderer found for this request!', 1403628294);
    }
}
