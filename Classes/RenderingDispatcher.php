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

use Helhum\TyposcriptRendering\Mvc\Request;
use Helhum\TyposcriptRendering\Mvc\RequestBuilder;
use Helhum\TyposcriptRendering\Mvc\Response;
use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use Helhum\TyposcriptRendering\Renderer\RenderingInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class TypoScriptRenderer
 */
class RenderingDispatcher {

	/**
	 * @var string
	 */
	protected $argumentNamespace = 'tx_typoscriptrendering';

	/**
	 * @var RequestBuilder
	 */
	protected $requestBuilder;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @param RequestBuilder $requestBuilder
	 */
	public function __construct(RequestBuilder $requestBuilder = NULL) {
		$this->requestBuilder = $requestBuilder ?: new RequestBuilder();
	}

	/**
	 * @param TypoScriptFrontendController $typoScriptFrontendController
	 * @throws Exception
	 */
	public function checkDataSubmission(TypoScriptFrontendController $typoScriptFrontendController) {
		// Do not do anything in this hook, if there are no parameters
		if ($typoScriptFrontendController->isGeneratePage() && GeneralUtility::_GP($this->argumentNamespace)) {
			$this->ensureRequiredEnvironment();
			$this->request = $this->requestBuilder->build(GeneralUtility::_GP($this->argumentNamespace));
			$this->response = new Response();
			if (!$this->request->hasArgument('renderer')) {
				throw new Exception('No renderer specified!', 1403628294);
			}

			$rendererClassName = $this->request->getArgument('renderer');
			if (strpos($rendererClassName, '\\') === FALSE) {
				$rendererClassName = 'Helhum\\TyposcriptRendering\\Renderer\\' . ucfirst($rendererClassName) . 'Renderer';
			}

			if (!class_exists($rendererClassName) || !in_array('Helhum\\TyposcriptRendering\\Renderer\\RenderingInterface', class_implements($rendererClassName), TRUE)) {
				throw new Exception(sprintf('Renderer of class "%s" does not implement rendering interface', $rendererClassName), 1403631454);
			}

			$renderingContext = new RenderingContext($typoScriptFrontendController);
			/** @var RenderingInterface $renderer */
			$renderer = new $rendererClassName();
			$renderer->renderRequest($this->request, $this->response, $renderingContext);

			$typoScriptFrontendController->content = $this->response->getContent();
			$typoScriptFrontendController->config['config']['pageGenScript'] = 'EXT:typoscript_rendering/Scripts/DummyRendering.php';
		}
	}

	/**
	 * @throws Exception
	 */
	protected function ensureRequiredEnvironment() {
		if (empty($GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFoundOnCHashError'])) {
			throw new Exception('$GLOBALS[\'TYPO3_CONF_VARS\'][\'FE\'][\'pageNotFoundOnCHashError\'] needs to be enabled when using out of bound typoscript rendering!', 1403808246);
		}
	}
}
