<?php
declare(strict_types=1);
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

use TYPO3\CMS\Core\Package\Cache\PackageCacheInterface;

class RenderingTest extends AbstractRenderingTestCase
{
    /**
     * @test
     */
    public function urlGeneratedRespectAbsRefPrefixAndLinkVarsAndTarget(): void
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.link')];
        $expectedContent = '<a href="/da/" target="_blank">link</a>';
        $actualContent = trim($this->fetchFrontendResponse($requestArguments)->getContent());
        $this->assertSame($expectedContent, $actualContent);
    }

    /**
     * @test
     */
    public function emailViewHelperWorksAlsoWithSpamProtection(): void
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.fluid')];
        $expectedContent = '<a href="#" data-mailto-token="ocknvq,kphqBjgnjwo0kq" data-mailto-vector="2">info(AT)helhum(DOT)io</a>';
        if (!interface_exists(PackageCacheInterface::class)) {
            $expectedContent = '<a href="javascript:linkTo_UnCryptMailto(%27ocknvq%2CkphqBjgnjwo0kq%27);">info(AT)helhum(DOT)io</a>';
        }
        $actualContent = trim($this->fetchFrontendResponse($requestArguments)->getContent());
        $this->assertSame($expectedContent, $actualContent);
    }

    /**
     * @test
     */
    public function viewHelperOutputsUri(): void
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.viewHelper')];
        $expectedContent = '/da/?tx_typoscriptrendering%5Bcontext%5D=%7B%22record%22%3A%22pages_1%22%2C%22path%22%3A%22tt_content.typoscriptrendering_plugintest.20%22%7D&amp;tx_typoscriptrendering_plugintest%5Bcontroller%5D=Foo&amp;cHash=05eba63c2a1d73fbdb2e4702e42fab9e';
        $actualContent = trim($this->fetchFrontendResponse($requestArguments)->getContent());
        $this->assertSame($expectedContent, $actualContent);
    }

    /**
     * @test
     */
    public function cObjectUriViewHelperOutputsUri(): void
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.cObjectUriViewHelper')];
        $expectedContent = '/da/?tx_typoscriptrendering%5Bcontext%5D=%7B%22record%22%3A%22pages_1%22%2C%22path%22%3A%22lib.foo%22%7D&amp;cHash=cb0d36cfb1819138f899192eda25168e';
        $actualContent = trim($this->fetchFrontendResponse($requestArguments)->getContent());
        $this->assertSame($expectedContent, $actualContent);
    }

    /**
     * @test
     */
    public function cObjectLinkViewHelperOutputsUri(): void
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.cObjectLinkViewHelper')];
        $expectedContent = '<a href="/da/?tx_typoscriptrendering%5Bcontext%5D=%7B%22record%22%3A%22pages_1%22%2C%22path%22%3A%22lib.foo%22%7D&amp;cHash=cb0d36cfb1819138f899192eda25168e">Link</a>';
        $actualContent = trim($this->fetchFrontendResponse($requestArguments)->getContent());
        $this->assertSame($expectedContent, $actualContent);
    }

    /**
     * @test
     */
    public function oldViewHelperOutputsUri(): void
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.oldViewHelper')];
        $expectedContent = '/da/?tx_typoscriptrendering%5Bcontext%5D=%7B%22record%22%3A%22pages_1%22%2C%22path%22%3A%22tt_content.typoscriptrendering_plugintest.20%22%7D&amp;tx_typoscriptrendering_plugintest%5Bcontroller%5D=Foo&amp;cHash=05eba63c2a1d73fbdb2e4702e42fab9e';
        $actualContent = trim($this->fetchFrontendResponse($requestArguments)->getContent());
        $this->assertSame($expectedContent, $actualContent);
    }

    /**
     * @test
     */
    public function linkViewHelperOutputsUri(): void
    {
        $requestArguments = ['url' => $this->getRenderUrl(1, 1, 'lib.linkViewHelper')];
        $expectedContent = '<a href="/da/?tx_typoscriptrendering%5Bcontext%5D=%7B%22record%22%3A%22pages_1%22%2C%22path%22%3A%22tt_content.typoscriptrendering_plugintest.20%22%7D&amp;tx_typoscriptrendering_plugintest%5Bcontroller%5D=Foo&amp;cHash=05eba63c2a1d73fbdb2e4702e42fab9e">Link</a>';
        $actualContent = trim($this->fetchFrontendResponse($requestArguments)->getContent());
        $this->assertSame($expectedContent, $actualContent);
    }
}
