<?php
namespace Helhum\TyposcriptRendering\Tests\Unit\Configuration;

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
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Test case.
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
        $this->typoScriptControllerMock = $this->getMockBuilder('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configurationBuilder = new RecordRenderingConfigurationBuilder(new RenderingContext($this->typoScriptControllerMock));
        $this->typoScriptControllerMock->tmpl = new \stdClass();
        $this->typoScriptControllerMock->tmpl->setup = [
            'tt_content.' => [
                'list.' => [
                    '20.' => [
                        'news_pi1' => 'USER',
                        'news_pi1.' => [],
                    ],
                ],
                'news_pi2.' => [
                    '20' => 'USER',
                    '20.' => [],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function pluginContextDataProvider()
    {
        return [
            'page specified' => [
                'News',
                'Pi1',
                'pages:1',
                ['record' => 'pages_1', 'path' => 'tt_content.list.20.news_pi1'],
            ],
            'page specified, content element' => [
                'News',
                'Pi2',
                'pages:1',
                ['record' => 'pages_1', 'path' => 'tt_content.news_pi2.20'],
            ],
            'tt_content specified' => [
                'News',
                'Pi1',
                'tt_content:12',
                ['record' => 'tt_content_12', 'path' => 'tt_content.list.20.news_pi1'],
            ],
            'no record specified falls back to current page' => [
                'News',
                'Pi1',
                '',
                ['record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'],
            ],
            'invalid record specified falls back to current page' => [
                'News',
                'Pi1',
                ':',
                ['record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'],
            ],
            'uid only specified falls back to current page' => [
                'News',
                'Pi1',
                ':1',
                ['record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'],
            ],
            'tableName only specified falls back to current page' => [
                'News',
                'Pi1',
                'foo:',
                ['record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'],
            ],
            'too many colons only specified falls back to current page' => [
                'News',
                'Pi1',
                'foo:bar:baz',
                ['record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'],
            ],
            'second argument is no int specified falls back to current page' => [
                'News',
                'Pi1',
                'foo:bar',
                ['record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'],
            ],
            'random string specified falls back to current page' => [
                'News',
                'Pi1',
                uniqid('foo_', true),
                ['record' => 'pages_42', 'path' => 'tt_content.list.20.news_pi1'],
            ],
        ];
    }

    /**
     * @param string $extensionName
     * @param string $pluginName
     * @param string $recordContext
     * @param string[] $expectedConfiguration
     * @test
     * @dataProvider pluginContextDataProvider
     */
    public function buildingConfigurationWorks($extensionName, $pluginName, $recordContext, array $expectedConfiguration)
    {
        $this->typoScriptControllerMock->id = 42;
        $this->assertSame($expectedConfiguration, $this->configurationBuilder->configurationFor($extensionName, $pluginName, $recordContext));
    }

    /**
     * @test
     * @expectedException \Helhum\TyposcriptRendering\Configuration\ConfigurationBuildingException
     * @expectedExceptionCode 1416846201
     */
    public function buildingConfigurationThrowsExceptionIfInvalidTypesAreGiven()
    {
        $this->configurationBuilder->configurationFor('foo', 'PiBar', []);
    }

    /**
     * @test
     * @expectedException \Helhum\TyposcriptRendering\Configuration\ConfigurationBuildingException
     * @expectedExceptionCode 1466779430
     */
    public function buildingConfigurationThrowsExceptionIfRenderingConfigIsNotFound()
    {
        $this->configurationBuilder->configurationFor('News', 'Pi3', 'current');
    }
}
