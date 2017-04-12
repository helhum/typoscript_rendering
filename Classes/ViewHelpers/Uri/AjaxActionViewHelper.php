<?php
namespace Helhum\TyposcriptRendering\ViewHelpers\Uri;

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

use Helhum\TyposcriptRendering\Configuration\RecordRenderingConfigurationBuilder;
use Helhum\TyposcriptRendering\Renderer\RenderingContext;

/**
 * A view helper for creating Ajax URIs to extbase actions.
 *
 * = Examples =
 *
 * <code title="URI to the show-action of the current controller">
 * <h:uri.ajaxAction action="show" />
 * </code>
 * <output>
 * index.php?id=123&tx_typoscriptrendering[context]={"record":"tt_content_123","path":"tt_content.list.20.myextension_plugin"}&tx_myextension_plugin[action]=show&tx_myextension_plugin[controller]=Standard&cHash=xyz
 * (depending on the current page and your TS configuration)
 * </output>
 */
class AjaxActionViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     * @inject
     */
    protected $configurationManager;

    /**
     * @param string $action Target action
     * @param array $arguments Arguments
     * @param string $controller Target controller in UpperCamelCase. If null, current controllerName is used.
     * @param string $extensionName Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used
     * @param string $pluginName Target plugin. If empty, the current plugin name is used
     * @param int $pageUid target page. See TypoLink destination
     * @param string $section the anchor to be added to the URI
     * @param string $format The requested format, e.g. ".html
     * @param bool $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
     * @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
     * @param bool $absolute If set, an absolute URI is rendered
     * @param bool $addQueryString If set, the current query parameters will be kept in the URI
     * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
     * @param string $addQueryStringMethod Set which parameters will be kept. Only active if $addQueryString = TRUE
     * @param string $contextRecord The record that the rendering should depend upon. e.g. current (default: record is fetched from current Extbase plugin), tt_content:12 (tt_content record with uid 12), pages:15 (pages record with uid 15), 'currentPage' record of current page
     *
     * @throws \Helhum\TyposcriptRendering\Configuration\ConfigurationBuildingException
     * @return string Rendered link
     *
     */
    public function render($action = null, array $arguments = array(), $controller = null, $extensionName = null, $pluginName = null, $pageUid = null, $section = '', $format = '', $linkAccessRestrictedPages = false, array $additionalParams = array(), $absolute = false, $addQueryString = false, array $argumentsToBeExcludedFromQueryString = array(), $addQueryStringMethod = null, $contextRecord = 'current')
    {
        if ($pluginName === null) {
            $pluginName = $this->controllerContext->getRequest()->getPluginName();
        }
        if ($extensionName === null) {
            $extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
        }
        if ($contextRecord === 'current') {
            if (
                $pluginName !== $this->controllerContext->getRequest()->getPluginName()
                || $extensionName !== $this->controllerContext->getRequest()->getControllerExtensionName()
            ) {
                $contextRecord = 'currentPage';
            } else {
                $contextRecord = $this->configurationManager->getContentObject()->currentRecord;
            }
        }
        $renderingConfiguration = $this->buildTypoScriptRenderingConfiguration($extensionName, $pluginName, $contextRecord);
        $additionalParams['tx_typoscriptrendering']['context'] = json_encode($renderingConfiguration);

        $uriBuilder = $this->controllerContext->getUriBuilder();
        $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setUseCacheHash(true)
            ->setSection($section)
            ->setFormat($format)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($absolute)
            ->setAddQueryString($addQueryString)
            ->setAddQueryStringMethod($addQueryStringMethod)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString);

        return $uriBuilder->uriFor($action, $arguments, $controller, $extensionName, $pluginName);
    }

    /**
     * @param string $extensionName
     * @param string $pluginName
     * @param string $contextRecord
     *
     * @throws \Helhum\TyposcriptRendering\Configuration\ConfigurationBuildingException
     * @return string[]
     *
     */
    public function buildTypoScriptRenderingConfiguration($extensionName, $pluginName, $contextRecord)
    {
        $configurationBuilder = new RecordRenderingConfigurationBuilder(new RenderingContext($GLOBALS['TSFE']));
        return $configurationBuilder->configurationFor($extensionName, $pluginName, $contextRecord);
    }
}
