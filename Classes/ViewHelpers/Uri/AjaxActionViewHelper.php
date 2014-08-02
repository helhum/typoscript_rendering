<?php
namespace Helhum\TyposcriptRendering\ViewHelpers\Uri;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * A view helper for creating "Ajax" URIs to extbase actions.
 *
 * = Examples =
 *
 * <code title="URI to the show-action of the current controller">
 * <h:uri.action action="show" />
 * </code>
 * <output>
 * index.php?id=123&tx_typoscriptrendering[context]={"record":"tt_content_123","path":"tt_content.list.20.myextension_plugin"}&tx_myextension_plugin[action]=show&tx_myextension_plugin[controller]=Standard&cHash=xyz
 * (depending on the current page and your TS configuration)
 * </output>
 */
class AjaxActionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Service\ExtensionService
	 * @inject
	 */
	protected $extensionService;

	/**
	 * @param string $action Target action
	 * @param array $arguments Arguments
	 * @param string $controller Target controller. If NULL current controllerName is used
	 * @param string $extensionName Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used
	 * @param string $pluginName Target plugin. If empty, the current plugin name is used
	 * @param integer $pageUid target page. See TypoLink destination
	 * @param string $section the anchor to be added to the URI
	 * @param string $format The requested format, e.g. ".html
	 * @param boolean $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
	 * @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
	 * @param boolean $absolute If set, an absolute URI is rendered
	 * @param boolean $addQueryString If set, the current query parameters will be kept in the URI
	 * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
	 * @param string $addQueryStringMethod Set which parameters will be kept. Only active if $addQueryString = TRUE
	 * @return string Rendered link
	 */
	public function render($action = NULL, array $arguments = array(), $controller = NULL, $extensionName = NULL, $pluginName = NULL, $pageUid = NULL, $section = '', $format = '', $linkAccessRestrictedPages = FALSE, array $additionalParams = array(), $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array(), $addQueryStringMethod = NULL) {
		list($table, $uid) = explode(':', $this->configurationManager->getContentObject()->currentRecord);
		if ($pluginName === NULL) {
			$pluginName = $this->controllerContext->getRequest()->getPluginName();
		}
		if ($extensionName === NULL) {
			$extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
		}
		$pluginNamespace = $this->extensionService->getPluginNamespace($extensionName, $pluginName);
		$ajaxContext = array(
			'record' => $table . '_' . $uid,
			'path' => 'tt_content.list.20.' . str_replace('tx_', '', $pluginNamespace)
		);
		$additionalParams['tx_typoscriptrendering']['context'] = json_encode($ajaxContext);

		$uriBuilder = $this->controllerContext->getUriBuilder();
		$uriBuilder->reset()
			->setTargetPageUid($pageUid)
			->setUseCacheHash(TRUE)
			->setSection($section)
			->setFormat($format)
			->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
			->setArguments($additionalParams)
			->setCreateAbsoluteUri($absolute)
			->setAddQueryString($addQueryString)
			->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString);

		// TYPO3 6.0 compatibility check:
		if (method_exists($uriBuilder, 'setAddQueryStringMethod')) {
			$uriBuilder->setAddQueryStringMethod($this->arguments['addQueryStringMethod']);
		}

		return $uriBuilder->uriFor($action, $arguments, $controller, $extensionName, $pluginName);
	}
}
