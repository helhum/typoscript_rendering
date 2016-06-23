<?php
namespace Helhum\TyposcriptRendering\Tests\Unit\Configuration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Helhum\TyposcriptRendering\Configuration\RecordRenderingConfigurationBuilder;
use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class RecordRenderingConfigurationBuilderTest
 */
class RecordRenderingConfigurationBuilderTest extends UnitTestCase
{
    /**
     * @var RecordRenderingConfigurationBuilder
     */
    protected $configurationBuilder;

    /**
     * @var TypoScriptFrontendController|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $typoScriptControllerMock;

    protected function setUp()
    {
        $this->typoScriptControllerMock = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', false);
        $this->configurationBuilder = new RecordRenderingConfigurationBuilder(new RenderingContext($this->typoScriptControllerMock));
    }

    /**
     * @return array
     */
    public function pluginContextDataProvider()
    {
        return array(
            'page specified' => array(
                'News',
                'Pi1',
                'pages:1',
                array('record' => 'pages_1', 'path' => 'tt_content.list.20.news_pi1'),
            ),
            'tt_content specified' => array(
                'News',
                'Pi1',
                'tt_content:12',
                array('record' => 'tt_content_12', 'path' => 'tt_content.list.20.news_pi1'),
            ),
            'no record specified falls back to current page' => array(
                'News',
                'Pi1',
                '',
                array('record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'),
            ),
            'invalid record specified falls back to current page' => array(
                'News',
                'Pi1',
                ':',
                array('record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'),
            ),
            'uid only specified falls back to current page' => array(
                'News',
                'Pi1',
                ':1',
                array('record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'),
            ),
            'tableName only specified falls back to current page' => array(
                'News',
                'Pi1',
                'foo:',
                array('record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'),
            ),
            'too many colons only specified falls back to current page' => array(
                'News',
                'Pi1',
                'foo:bar:baz',
                array('record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'),
            ),
            'second argument is no int specified falls back to current page' => array(
                'News',
                'Pi1',
                'foo:bar',
                array('record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'),
            ),
            'random string specified falls back to current page' => array(
                'News',
                'Pi1',
                uniqid('foo_', true),
                array('record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'),
            ),
        );
    }

    /**
     * @param $extensionName
     * @param $pluginName
     * @param $recordContext
     * @param $expectedConfiguration
     * @test
     * @dataProvider pluginContextDataProvider
     */
    public function buildingConfigurationWorks($extensionName, $pluginName, $recordContext, $expectedConfiguration)
    {
        $this->typoScriptControllerMock->id = 42;
        $this->assertSame($expectedConfiguration, $this->configurationBuilder->configurationFor($extensionName, $pluginName, $recordContext));
    }

    /**
     * @test
     * @expectedException \Helhum\TyposcriptRendering\Configuration\ConfigurationBuildingException
     */
    public function buildingConfigurationThrowsExceptionIfInvalidTypesAreGiven()
    {
        $this->configurationBuilder->configurationFor('foo', 'PiBar', array());
    }
}
