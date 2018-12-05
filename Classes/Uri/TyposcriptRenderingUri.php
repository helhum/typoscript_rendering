<?php
declare(strict_types=1);
namespace Helhum\TyposcriptRendering\Uri;

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

use Helhum\TyposcriptRendering\Configuration\ConfigurationBuildingException;
use Helhum\TyposcriptRendering\Configuration\RecordRenderingConfigurationBuilder;
use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use TYPO3\CMS\Core\Http\Uri;

class TyposcriptRenderingUri extends Uri
{
    /**
     * @var ViewHelperContext
     */
    private $viewHelperContext;

    public function withViewHelperContext(ViewHelperContext $viewHelperContext): self
    {
        $newUri = clone $this;
        $newUri->viewHelperContext = $viewHelperContext;
        $newUri->parseViewHelperContext($viewHelperContext);

        return $newUri;
    }

    private function parseViewHelperContext(ViewHelperContext $viewHelperContext): void
    {
        $arguments = $viewHelperContext->getArguments();
        $controllerContext = $viewHelperContext->getControllerContext();

        $pluginName = $arguments['pluginName'];
        $extensionName = $arguments['extensionName'];
        $contextRecord = $arguments['contextRecord'];

        if ($pluginName === null) {
            $pluginName = $controllerContext->getRequest()->getPluginName();
        }
        if ($extensionName === null) {
            $extensionName = $controllerContext->getRequest()->getControllerExtensionName();
        }
        if ($contextRecord === 'current') {
            if (
                $pluginName !== $controllerContext->getRequest()->getPluginName()
                || $extensionName !== $controllerContext->getRequest()->getControllerExtensionName()
            ) {
                $contextRecord = 'currentPage';
            } else {
                $contextRecord = $viewHelperContext->getContentObject()->currentRecord;
            }
        }
        $renderingConfiguration = $this->buildTypoScriptRenderingConfiguration($extensionName, $pluginName, $contextRecord);
        $additionalParams['tx_typoscriptrendering']['context'] = json_encode($renderingConfiguration);

        $uriBuilder = $controllerContext->getUriBuilder();
        $uriBuilder->reset()
            ->setTargetPageUid($arguments['pageUid'])
            ->setUseCacheHash(true)
            ->setSection($arguments['section'])
            ->setFormat($arguments['format'])
            ->setLinkAccessRestrictedPages($arguments['linkAccessRestrictedPages'])
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($arguments['absolute'])
            ->setAddQueryString($arguments['addQueryString'])
            ->setAddQueryStringMethod($arguments['addQueryStringMethod'])
            ->setArgumentsToBeExcludedFromQueryString($arguments['argumentsToBeExcludedFromQueryString']);

        $this->parseUri($uriBuilder->uriFor($arguments['action'], $arguments['arguments'], $arguments['controller'], $extensionName, $pluginName));
    }

    /**
     * @param string $extensionName
     * @param string $pluginName
     * @param string $contextRecordId
     *
     * @throws ConfigurationBuildingException
     * @return string[]
     *
     */
    public function buildTypoScriptRenderingConfiguration(string $extensionName, string $pluginName, string $contextRecordId): array
    {
        $configurationBuilder = new RecordRenderingConfigurationBuilder(new RenderingContext($GLOBALS['TSFE']));

        return $configurationBuilder->configurationFor($extensionName, $pluginName, $contextRecordId);
    }
}
