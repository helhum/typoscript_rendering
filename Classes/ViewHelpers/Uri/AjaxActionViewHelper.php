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
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * Initialize arguments
     *
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('action', 'string', 'Target action');
        $this->registerArgument('arguments', 'array', 'Arguments', false, []);
        $this->registerArgument('controller', 'string', 'Target controller. If NULL current controllerName is used');
        $this->registerArgument('extensionName', 'string', 'Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used');
        $this->registerArgument('pluginName', 'string', 'Target plugin. If empty, the current plugin name is used');
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI', false, '');
        $this->registerArgument('format', 'string', 'The requested format, e.g. ".html', false, '');
        $this->registerArgument('linkAccessRestrictedPages', 'bool', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.', false, false);
        $this->registerArgument('additionalParams', 'array', 'additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)', false, []);
        $this->registerArgument('absolute', 'bool', 'If set, an absolute URI is rendered', false, false);
        $this->registerArgument('addQueryString', 'bool', 'If set, the current query parameters will be kept in the URI', false, false);
        $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'arguments to be removed from the URI. Only active if $addQueryString = TRUE', false, []);
        $this->registerArgument('addQueryStringMethod', 'string', 'Set which parameters will be kept. Only active if $addQueryString = TRUE');
        $this->registerArgument('contextRecord', 'string', 'The record that the rendering should depend upon. e.g. current (default: record is fetched from current Extbase plugin), tt_content:12 (tt_content record with uid 12), pages:15 (pages record with uid 15), \'currentPage\' record of current page', false, 'current');
    }

    /**
     *
     * @throws \Helhum\TyposcriptRendering\Configuration\ConfigurationBuildingException
     * @return string Rendered link
     *
     */
    public function render()
    {
        $pluginName = $this->arguments['pluginName'];
        $extensionName = $this->arguments['extensionName'];
        $contextRecord = $this->arguments['contextRecord'];

        if ($pluginName === null) {
            $pluginName = $this->renderingContext->getControllerContext()->getRequest()->getPluginName();
        }
        if ($extensionName === null) {
            $extensionName = $this->renderingContext->getControllerContext()->getRequest()->getControllerExtensionName();
        }
        if ($contextRecord === 'current') {
            if (
                $pluginName !== $this->renderingContext->getControllerContext()->getRequest()->getPluginName()
                || $extensionName !== $this->renderingContext->getControllerContext()->getRequest()->getControllerExtensionName()
            ) {
                $contextRecord = 'currentPage';
            } else {
                $contextRecord = $this->configurationManager->getContentObject()->currentRecord;
            }
        }
        $renderingConfiguration = $this->buildTypoScriptRenderingConfiguration($extensionName, $pluginName, $contextRecord);
        $additionalParams['tx_typoscriptrendering']['context'] = json_encode($renderingConfiguration);

        $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
        $uriBuilder->reset()
            ->setTargetPageUid($this->arguments['pageUid'])
            ->setUseCacheHash(true)
            ->setSection($this->arguments['section'])
            ->setFormat($this->arguments['format'])
            ->setLinkAccessRestrictedPages($this->arguments['linkAccessRestrictedPages'])
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($this->arguments['absolute'])
            ->setAddQueryString($this->arguments['addQueryString'])
            ->setAddQueryStringMethod($this->arguments['addQueryStringMethod'])
            ->setArgumentsToBeExcludedFromQueryString($this->arguments['argumentsToBeExcludedFromQueryString']);

        return $uriBuilder->uriFor($this->arguments['action'], $this->arguments['arguments'], $this->arguments['controller'], $extensionName, $pluginName);
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
