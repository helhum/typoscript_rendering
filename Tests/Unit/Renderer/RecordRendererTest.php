<?php
namespace Helhum\TyposcriptRendering\Tests\Unit\Renderer;

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

use Helhum\TyposcriptRendering\Mvc\Request;
use Helhum\TyposcriptRendering\Renderer\RecordRenderer;
use Helhum\TyposcriptRendering\Renderer\RenderingContext;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class RecordRendererTest
 */
class RecordRendererTest extends UnitTestCase
{
    /**
     * @var RecordRenderer|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $renderer;

    protected function setUp()
    {
        $this->renderer = $this->getAccessibleMock('Helhum\\TyposcriptRendering\\Renderer\\RecordRenderer', ['dummy']);
    }

    /**
     * @return array
     */
    public function configurationDataProvider()
    {
        return [
            'record id only' => [
                ['record' => '1'],
                ['source' => 'tt_content_1', 'tables' => 'tt_content'],
            ],
            'record with table in id' => [
                ['record' => 'tx_foo_table_2'],
                ['source' => 'tx_foo_table_2', 'tables' => 'tx_foo_table'],
            ],
            'record with table' => [
                ['record' => '2', 'table' => 'tx_bar'],
                ['source' => 'tx_bar_2', 'tables' => 'tx_bar'],
            ],
            'record with empty table' => [
                ['record' => '2', 'table' => ''],
                ['source' => 'tt_content_2', 'tables' => 'tt_content'],
            ],
            'path only' => [
                ['path' => 'lib.foo'],
                ['source' => 'pages_42', 'tables' => 'pages', 'conf.' => ['pages' => '< lib.foo']],
            ],
            'record with path' => [
                ['record' => '1', 'path' => 'lib.bar'],
                ['source' => 'tt_content_1', 'tables' => 'tt_content', 'conf.' => ['tt_content' => '< lib.bar']],
            ],
            'wrong record with path' => [
                ['record' => '_', 'path' => 'lib.bar'],
                ['source' => 'pages_42', 'tables' => 'pages', 'conf.' => ['pages' => '< lib.bar']],
            ],
            'empty record with path' => [
                ['record' => '', 'path' => 'lib.bar'],
                ['source' => 'pages_42', 'tables' => 'pages', 'conf.' => ['pages' => '< lib.bar']],
            ],
            'empty record with empty path' => [
                ['record' => '', 'path' => ''],
                ['source' => 'pages_42', 'tables' => 'pages'],
            ],
            'empty record' => [
                ['record' => ''],
                ['source' => 'pages_42', 'tables' => 'pages'],
            ],
            'empty path' => [
                ['path' => ''],
                ['source' => 'pages_42', 'tables' => 'pages'],
            ],
            'root page' => [
                ['path' => ''],
                ['dontCheckPid' => '1', 'source' => 'pages_1', 'tables' => 'pages'],
                '1'
            ],
        ];
    }

    /**
     * @param array $requestArguments
     * @param array $expectedConfiguration
     * @param string $pageId
     * @test
     * @dataProvider configurationDataProvider
     */
    public function configurationIsGeneratedCorrectlyFromRequest(array $requestArguments, array $expectedConfiguration, $pageId = '42')
    {
        $tsfeMock = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', [], [], '', false);
        $pageRepositoryMock = $this->getMock('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
        $pageRepositoryMock->expects($this->any())->method('getRootLine')->willReturn(
                [
                    [
                        'uid' => '1',
                        'pid' => '0',
                    ],
                ]
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
