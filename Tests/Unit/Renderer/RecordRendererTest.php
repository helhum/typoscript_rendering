<?php
namespace Helhum\TyposcriptRendering\Tests\Unit\Renderer;

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

use Helhum\TyposcriptRendering\Mvc\Request;
use Helhum\TyposcriptRendering\Renderer\RecordRenderer;
use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Test case.
 */
class RecordRendererTest extends UnitTestCase
{
    /**
     * @var RecordRenderer|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = $this->getAccessibleMock('Helhum\\TyposcriptRendering\\Renderer\\RecordRenderer', array('dummy'));
    }

    /**
     * @return array
     */
    public function configurationDataProvider()
    {
        return array(
            'record id only' => array(
                array('record' => '1'),
                array('source' => 'tt_content_1', 'tables' => 'tt_content'),
            ),
            'record with table in id' => array(
                array('record' => 'tx_foo_table_2'),
                array('source' => 'tx_foo_table_2', 'tables' => 'tx_foo_table'),
            ),
            'record with table' => array(
                array('record' => '2', 'table' => 'tx_bar'),
                array('source' => 'tx_bar_2', 'tables' => 'tx_bar'),
            ),
            'record with empty table' => array(
                array('record' => '2', 'table' => ''),
                array('source' => 'tt_content_2', 'tables' => 'tt_content'),
            ),
            'path only' => array(
                array('path' => 'lib.foo'),
                array('source' => 'pages_42', 'tables' => 'pages', 'conf.' => array('pages' => '< lib.foo')),
            ),
            'record with path' => array(
                array('record' => '1', 'path' => 'lib.bar'),
                array('source' => 'tt_content_1', 'tables' => 'tt_content', 'conf.' => array('tt_content' => '< lib.bar')),
            ),
            'wrong record with path' => array(
                array('record' => '_', 'path' => 'lib.bar'),
                array('source' => 'pages_42', 'tables' => 'pages', 'conf.' => array('pages' => '< lib.bar')),
            ),
            'empty record with path' => array(
                array('record' => '', 'path' => 'lib.bar'),
                array('source' => 'pages_42', 'tables' => 'pages', 'conf.' => array('pages' => '< lib.bar')),
            ),
            'empty record with empty path' => array(
                array('record' => '', 'path' => ''),
                array('source' => 'pages_42', 'tables' => 'pages'),
            ),
            'empty record' => array(
                array('record' => ''),
                array('source' => 'pages_42', 'tables' => 'pages'),
            ),
            'empty path' => array(
                array('path' => ''),
                array('source' => 'pages_42', 'tables' => 'pages'),
            ),
            'root page' => array(
                array('path' => ''),
                array('dontCheckPid' => '1', 'source' => 'pages_1', 'tables' => 'pages'),
                '1',
            ),
        );
    }

    /**
     * @param array $requestArguments
     * @param string[] $expectedConfiguration
     * @param string $pageId
     * @test
     * @dataProvider configurationDataProvider
     */
    public function configurationIsGeneratedCorrectlyFromRequest(array $requestArguments, array $expectedConfiguration, $pageId = '42')
    {
        /** @var TypoScriptFrontendController|\PHPUnit_Framework_MockObject_MockObject $tsfeMock */
        $tsfeMock = $this->getMockBuilder('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController')
            ->disableOriginalConstructor()
            ->getMock();
        $pageRepositoryMock = $this->getMockBuilder('TYPO3\\CMS\\Frontend\\Page\\PageRepository')->getMock();
        $pageRepositoryMock->expects($this->any())->method('getRootLine')->willReturn(
                array(
                    array(
                        'uid' => '1',
                        'pid' => '0',
                    ),
                )
        );
        $tsfeMock->id = $pageId;
        $tsfeMock->sys_page = $pageRepositoryMock;
        $contextFixture = new RenderingContext($tsfeMock);
        $requestFixture = new Request($requestArguments);

        // This tests if the provided data makes sense
        $this->assertTrue($this->renderer->canRender($requestFixture));

        // Actual test
        $this->assertSame(
            $expectedConfiguration,
            $this->renderer->_call('resolveRenderingConfiguration', new Request($requestArguments), $contextFixture)
        );
    }
}
