<?php
declare(strict_types=1);
namespace Helhum\TyposcriptRendering\ViewHelpers\Widget;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class UriViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Widget\UriViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     * @inject
     */
    protected $configurationManager;

    /**
     * Initialize arguments
     *
     * @return void
     *
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('extensionName', 'string', 'The extension that the rendering should depend upon.', true);
        $this->registerArgument('pluginName', 'string', 'The plugin that the rendering should depend upon.', true);
        $this->registerArgument('contextRecord', 'string', 'The record that the rendering should depend upon. e.g. current (default: record is fetched from current Extbase plugin), tt_content:12 (tt_content record with uid 12), pages:15 (pages record with uid 15), \'currentPage\' record of current page');
    }

    /**
     * Get the URI for an AJAX Request.
     *
     * @param RenderingContextInterface $renderingContext
     * @param array $arguments
     * @throws \Helhum\TyposcriptRendering\Configuration\ConfigurationBuildingException
     * @return string the AJAX URI
     */
    protected static function getAjaxUri(RenderingContextInterface $renderingContext, array $arguments)
    {
        $pluginName = $arguments['pluginName'];
        $extensionName = $arguments['extensionName'];
        $contextRecord = $arguments['contextRecord'];
        $arguments = isset($arguments['arguments']) ? $arguments['arguments'] : [];
        if ($contextRecord === 'current') {
            if (
                $pluginName !== $renderingContext->getControllerContext()->getRequest()->getPluginName()
                || $extensionName !== $renderingContext->getControllerContext()->getRequest()->getControllerExtensionName()
            ) {
                $contextRecord = 'currentPage';
            } else {
                $configurationManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);
                $contextRecord = $configurationManager->getContentObject()->currentRecord;
            }
        }
        $renderingConfiguration = self::buildTypoScriptRenderingConfiguration($extensionName, $pluginName, $contextRecord);
        $additionalParams['tx_typoscriptrendering']['context'] = json_encode($renderingConfiguration);

        $uriBuilder = $renderingContext->getControllerContext()->getUriBuilder();
        $argumentPrefix = $renderingContext->getControllerContext()->getRequest()->getArgumentPrefix();

        $uriBuilder->reset()
            ->setArguments(array_merge([$argumentPrefix => $arguments], $additionalParams))
            ->setSection($arguments['section'])
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString([$argumentPrefix, 'cHash'])
            ->setFormat($arguments['format'])
            ->setUseCacheHash(true);

        // TYPO3 6.0 compatibility check:
        if (method_exists($uriBuilder, 'setAddQueryStringMethod')) {
            $uriBuilder->setAddQueryStringMethod($arguments['addQueryStringMethod']);
        }

        return $uriBuilder->build();
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
    public static function buildTypoScriptRenderingConfiguration($extensionName, $pluginName, $contextRecord)
    {
        $configurationBuilder = new RecordRenderingConfigurationBuilder(new RenderingContext($GLOBALS['TSFE']));
        return $configurationBuilder->configurationFor($extensionName, $pluginName, $contextRecord);
    }
}
