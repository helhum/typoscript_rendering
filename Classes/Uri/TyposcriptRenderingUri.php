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
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Fluid\Core\Widget\WidgetRequest;

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

    public function withWidgetContext(ViewHelperContext $viewHelperContext): self
    {
        $newUri = clone $this;
        $newUri->viewHelperContext = $viewHelperContext;
        $newUri->parseWidgetContext($viewHelperContext);

        return $newUri;
    }

    private function parseViewHelperContext(ViewHelperContext $viewHelperContext): void
    {
        $arguments = $viewHelperContext->getArguments();
        $controllerContext = $viewHelperContext->getControllerContext();
        $request = $controllerContext->getRequest();

        $pluginName = $arguments['pluginName'] ?? null;
        $extensionName = $arguments['extensionName'] ?? null;
        $contextRecord = $arguments['contextRecord'];
        $additionalParams = $arguments['additionalParams'] ?? [];
        $renderingPath = $arguments['typoscriptObjectPath'] ?? null;

        if ($pluginName === null) {
            $pluginName = $request->getPluginName();
        }
        if ($extensionName === null) {
            $extensionName = $request->getControllerExtensionName();
        }
        if ($contextRecord === 'current') {
            if (
                $pluginName !== $request->getPluginName()
                || $extensionName !== $request->getControllerExtensionName()
            ) {
                $contextRecord = 'currentPage';
            } else {
                $contextRecord = $viewHelperContext->getContentObject()->currentRecord;
            }
        }
        if (is_string($renderingPath)) {
            $renderingConfiguration = $this->buildConfigurationForPath($renderingPath, $contextRecord);
        } else {
            $renderingConfiguration = $this->buildTypoScriptRenderingConfiguration($extensionName, $pluginName, $contextRecord);
        }
        $additionalParams['tx_typoscriptrendering']['context'] = json_encode($renderingConfiguration);

        $uriBuilder = $controllerContext->getUriBuilder();
        $uriBuilder->reset();
        if (is_callable([$uriBuilder, 'setUseCacheHash'])) {
            $uriBuilder->setUseCacheHash(true);
        }
        $uriBuilder
            ->setSection($arguments['section'] ?? '')
            ->setFormat($arguments['format'] ?? 'html')
            ->setLinkAccessRestrictedPages($arguments['linkAccessRestrictedPages'] ?? false)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($arguments['absolute'] ?? false)
            ->setAddQueryString($arguments['addQueryString'] ?? false)
            ->setAddQueryStringMethod('GET')
            ->setArgumentsToBeExcludedFromQueryString($arguments['argumentsToBeExcludedFromQueryString'] ?? []);

        $targetPageUid = $arguments['pageUid'] ?? null;
        if (MathUtility::canBeInterpretedAsInteger($targetPageUid)) {
            $uriBuilder->setTargetPageUid((int)$targetPageUid);
        }

        $this->parseUri(
            $uriBuilder->uriFor(
                $arguments['action'] ?? null,
                $arguments['arguments'] ?? null,
                $arguments['controller'] ?? null,
                $extensionName ?? '',
                $pluginName ?? ''
            ),
            $renderingPath !== null
        );
    }

    private function parseWidgetContext(ViewHelperContext $viewHelperContext): void
    {
        $arguments = $viewHelperContext->getArguments();
        $controllerContext = $viewHelperContext->getControllerContext();
        /** @var $request WidgetRequest $request */
        $request = $controllerContext->getRequest();
        if (!$request instanceof WidgetRequest) {
            throw new \RuntimeException('Called from wrong context', 1589401907);
        }

        $pluginName = $arguments['pluginName'] ?? null;
        $extensionName = $arguments['extensionName'] ?? null;
        $contextRecord = $arguments['contextRecord'];
        $additionalParams = $arguments['additionalParams'] ?? [];
        $renderingPath = $arguments['typoscriptObjectPath'] ?? null;

        if ($pluginName === null) {
            $pluginName = $request->getWidgetContext()->getParentPluginName();
        }
        if ($extensionName === null) {
            $extensionName = $request->getWidgetContext()->getParentExtensionName();
        }
        if ($contextRecord === 'current') {
            if (
                $pluginName !== $request->getWidgetContext()->getParentPluginName()
                || $extensionName !== $request->getWidgetContext()->getParentExtensionName()
            ) {
                $contextRecord = 'currentPage';
            } else {
                $contextRecord = $viewHelperContext->getContentObject()->currentRecord;
            }
        }
        if (is_string($renderingPath)) {
            $renderingConfiguration = $this->buildConfigurationForPath($renderingPath, $contextRecord);
        } else {
            $renderingConfiguration = $this->buildTypoScriptRenderingConfiguration($extensionName, $pluginName, $contextRecord);
        }
        // @deprecated ajax set to false is deprecated
        if ($arguments['ajax']) {
            $additionalParams['tx_typoscriptrendering']['context'] = json_encode($renderingConfiguration);
        }

        // adding the widget prefix for the arguments, use them together with the additionalParams
        $additionalParams[$request->getArgumentPrefix()] = $arguments['arguments'];

        $uriBuilder = $controllerContext->getUriBuilder();
        $uriBuilder->reset()
            ->setUseCacheHash(true)
            ->setSection($arguments['section'] ?? '')
            ->setFormat($arguments['format'] ?? 'html')
            ->setLinkAccessRestrictedPages($arguments['linkAccessRestrictedPages'] ?? false)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($arguments['absolute'] ?? false)
            ->setAddQueryString(true)
            ->setAddQueryStringMethod('GET')
            ->setArgumentsToBeExcludedFromQueryString($arguments['argumentsToBeExcludedFromQueryString'] ?? []);

        $targetPageUid = $arguments['pageUid'] ?? null;
        if (MathUtility::canBeInterpretedAsInteger($targetPageUid)) {
            $uriBuilder->setTargetPageUid((int)$targetPageUid);
        }

        $uri = $uriBuilder->build();

        $this->parseUri(
            $uri,
            $renderingPath !== null
        );
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

    private function buildConfigurationForPath(string $renderingPath, string $contextRecordId): array
    {
        $configurationBuilder = new RecordRenderingConfigurationBuilder(new RenderingContext($GLOBALS['TSFE']));

        return $configurationBuilder->configurationForPath($renderingPath, $contextRecordId);
    }

    protected function parseUri($uri, $removeControllerArgument = false)
    {
        if ($removeControllerArgument) {
            $uri = str_replace('&tx__%5Bcontroller%5D=Standard', '', $uri);
        }
        parent::parseUri($uri);
    }
}
