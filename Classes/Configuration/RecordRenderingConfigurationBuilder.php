<?php
namespace Helhum\TyposcriptRendering\Configuration;

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

use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use TYPO3\CMS\Core\Utility\MathUtility;

class RecordRenderingConfigurationBuilder
{
    /**
     * @var RenderingContext
     */
    protected $renderingContext;

    /**
     * @param RenderingContext $renderingContext
     */
    public function __construct(RenderingContext $renderingContext)
    {
        $this->renderingContext = $renderingContext;
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
    public function configurationFor($extensionName, $pluginName, $contextRecord = 'currentPage')
    {
        list($tableName, $uid) = $this->resolveTableNameAndUidFromContextString($contextRecord);
        $pluginSignature = $this->buildPluginSignature($extensionName, $pluginName);
        $renderingPath = $this->resolveRenderingPath($pluginSignature);
        return array(
            'record' => $tableName . '_' . $uid,
            'path' => $renderingPath,
        );
    }

    /**
     * Resolves the table name and uid for the record the rendering is based upon.
     * Falls back to current page if none is available
     *
     * @param string $contextRecord
     *
     * @throws ConfigurationBuildingException
     * @return string[] table name as first and uid as second index of the array
     *
     */
    protected function resolveTableNameAndUidFromContextString($contextRecord)
    {
        if (!is_string($contextRecord)) {
            throw new ConfigurationBuildingException(sprintf('Context record must be of type string "%s" given.', gettype($contextRecord)), 1416846201);
        }

        if ($contextRecord === 'currentPage') {
            $tableNameAndUid = array('pages', $this->renderingContext->getFrontendController()->id);
        } else {
            $tableNameAndUid = explode(':', $contextRecord);
            if (count($tableNameAndUid) !== 2 || empty($tableNameAndUid[0]) || empty($tableNameAndUid[1]) || !MathUtility::canBeInterpretedAsInteger($tableNameAndUid[1])) {
                $tableNameAndUid = array('pages', $this->renderingContext->getFrontendController()->id);
            }
        }
        // TODO: maybe check if the record is available
        return $tableNameAndUid;
    }

    /**
     * Builds the plugin signature for the tt_content rendering
     *
     * @param string $extensionName
     * @param string $pluginName
     *
     * @return string
     *
     * @see \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin()
     */
    protected function buildPluginSignature($extensionName, $pluginName)
    {
        // Check if vendor name is prepended to extensionName in the format {vendorName}.{extensionName}
        $delimiterPosition = strrpos($extensionName, '.');
        if ($delimiterPosition !== false) {
            $extensionName = substr($extensionName, $delimiterPosition + 1);
        }
        $extensionName = str_replace(' ', '', ucwords(str_replace('_', ' ', $extensionName)));

        return strtolower($extensionName . '_' . $pluginName);
    }

    /**
     * @param string $pluginSignature
     *
     * @throws ConfigurationBuildingException
     * @return string
     *
     */
    protected function resolveRenderingPath($pluginSignature)
    {
        $typoScriptRenderingSetup = $this->renderingContext->getFrontendController()->tmpl->setup['tt_content.'];
        if (isset($typoScriptRenderingSetup[$pluginSignature . '.']['20'])) {
            return sprintf('tt_content.%s.20', $pluginSignature);
        } elseif (isset($typoScriptRenderingSetup['list.']['20.'][$pluginSignature])) {
            return sprintf('tt_content.list.20.%s', $pluginSignature);
        }
        throw new ConfigurationBuildingException(sprintf('Could not determine rendering location for plugin signature "%s"', $pluginSignature), 1466779430);
    }
}
