<?php
namespace Tests\Unit\Renderer;

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
class RecordRendererTest extends UnitTestCase {

	/**
	 * @var RecordRenderer|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface
	 */
	protected $renderer;

	protected function setUp() {
		$this->renderer = $this->getAccessibleMock('Helhum\\TyposcriptRendering\\Renderer\\RecordRenderer', array('dummy'));
	}

	/**
	 * @return array
	 */
	public function configurationDataProvider() {
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
			'path only' => array(
				array('path' => 'lib.foo'),
				array('source' => 'pages_42', 'tables' => 'pages', 'conf.' => array('pages' => '< lib.foo')),
			),
			'record with path' => array(
				array('record' => '1', 'path' => 'lib.bar'),
				array('source' => 'tt_content_1', 'tables' => 'tt_content', 'conf.' => array('tt_content' => '< lib.bar')),
			),
		);
	}

	/**
	 * @param array $requestArguments
	 * @param array $expectedConfiguration
	 * @test
	 * @dataProvider configurationDataProvider
	 */
	public function configurationIsGeneratedCorrectlyFromRequest(array $requestArguments, array $expectedConfiguration) {
		$tsfeMock = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', FALSE);
		$tsfeMock->id = 42;
		$contextFixture = new RenderingContext($tsfeMock);
		$this->assertSame(
			$expectedConfiguration,
			$this->renderer->_call('resolveRenderingConfiguration', new Request($requestArguments), $contextFixture)
		);
	}
} 