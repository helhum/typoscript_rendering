<?php
namespace Helhum\TyposcriptRendering\Tests\Functional;

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

class RenderingTest extends AbstractRenderingTestCase
{
    /**
     * @test
     */
    public function urlGeneratedRespectAbsRefPrefixAndLinkVarsAndTarget()
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.link')];
        $expectedContent = '<a href="/index.php?id=1&amp;L=1" target="_blank">link</a>';
        $this->assertSame($expectedContent, trim($this->fetchFrontendResponse($requestArguments)->getContent()));
    }

    /**
     * @test
     */
    public function emailViewHelperWorksAlsoWithSpamProtection()
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.fluid')];
        $expectedContent = '<a href="javascript:linkTo_UnCryptMailto(\'ocknvq,kphqBjgnjwo0kq\');">info(AT)helhum(DOT)io</a>';
        $this->assertSame($expectedContent, trim($this->fetchFrontendResponse($requestArguments)->getContent()));
    }

    /**
     * @test
     */
    public function viewHelperOutputsUri()
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.viewHelper')];
        $expectedContent = '/index.php?id=1&amp;L=1&amp;tx_typoscriptrendering%5Bcontext%5D=%7B%22record%22%3A%22pages_1%22%2C%22path%22%3A%22tt_content.typoscriptrendering_plugintest.20%22%7D&amp;tx_typoscriptrendering_plugintest%5Bcontroller%5D=Foo&amp;cHash=';
        $this->assertSame(0, strpos(trim($this->fetchFrontendResponse($requestArguments)->getContent()), $expectedContent));
    }
}
