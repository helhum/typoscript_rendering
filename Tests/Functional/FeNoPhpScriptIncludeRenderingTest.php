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

class FeNoPhpScriptIncludeRenderingTest extends AbstractRenderingTestCase
{
    /**
     * @var array
     */
    protected $configurationToUseInTestInstance = array(
        'FE' => array(
            'noPHPscriptInclude' => true,
        ),
    );

    /**
     * @test
     */
    public function contentIsRenderedWithNoPhpScriptInclusionEnabled()
    {
        $requestArguments = array('url' => $this->getRenderUrl(1, 1, 'lib.link'));
        $expectedContent = '<a href="/index.php?id=1&amp;L=1" target="_blank">link</a>';
        $this->assertSame($expectedContent, $this->fetchFrontendResponse($requestArguments)->getContent());
    }
}
