<?php
namespace Helhum\TyposcriptRendering\Configuration;

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

use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Class RecordRenderingConfigurationBuilder
 */
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
     * @return array
     */
    public function configurationFor($extensionName, $pluginName, $contextRecord = 'currentPage')
    {
        list($tableName, $uid) = $this->resolveTableNameAndUidFromContextString($contextRecord);
        $pluginSignature = $this->buildPluginSignature($extensionName, $pluginName);

        return array(
            'record' => $tableName . '_' . $uid,
            'path' => 'tt_content.list.20.' . $pluginSignature
        );
    }

    /**
     * Resolves the table name and uid for the record the rendering is based upon.
     * Falls back to current page if none is available
     *
     * @param $contextRecord
     * @return array table name as first and uid as second index of the array
     *
     * @throws ConfigurationBuildingException
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
     * @return string
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
}
