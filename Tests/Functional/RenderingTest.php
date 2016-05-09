<?php
namespace Helhum\TyposcriptRendering\Tests\Functional;

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

require_once __DIR__ . '/AbstractRenderingTestCase.php';

/**
 * Class RenderingTest
 */
class RenderingTest extends AbstractRenderingTestCase
{
    /**
     * @test
     */
    public function urlGeneratedRespectAbsRefPrefixAndLinkVarsAndTarget()
    {
        $requestArguments = array('url' => $this->getRenderUrl(1, 1, 'lib.link'));
        $expectedContent = '<a href="/index.php?id=1&amp;L=1" target="_blank">link</a>';
        $this->assertSame($expectedContent, $this->fetchFrontendResponse($requestArguments)->getContent());
    }

    /**
     * @test
     */
    public function emailViewHelperWorksAlsoWithSpamProtection()
    {
        $requestArguments = array('url' => $this->getRenderUrl(1, 1, 'lib.fluid'));
        $expectedContent = '<a href="javascript:linkTo_UnCryptMailto(\'ocknvq,kphqBjgnjwo0kq\');">info(AT)helhum(DOT)io</a>';
        $this->assertSame($expectedContent, $this->fetchFrontendResponse($requestArguments)->getContent());
    }
}
