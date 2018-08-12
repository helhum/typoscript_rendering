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

use Nimut\TestingFramework\Http\Response;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use PHPUnit\Util\PHP\DefaultPhpProcess;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Test case.
 */
abstract class AbstractRenderingTestCase extends FunctionalTestCase
{
    /**
     * @var string[]
     */
    protected $testExtensionsToLoad = array('typo3conf/ext/typoscript_rendering');

    /**
     * @var string[]
     */
    protected $coreExtensionsToLoad = array('fluid');

    public function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/Fixtures/Database/pages.xml');
        $this->setUpFrontendRootPage(1, array('EXT:typoscript_rendering/Tests/Functional/Fixtures/Frontend/Basic.ts'));
    }

    /* ***********************************************
     * Utility methods for browsing a frontend page
     * ***********************************************/

    /**
     * @param int $pageId
     * @param int $languageId
     * @param string $path
     *
     * @return string
     */
    protected function getRenderUrl($pageId, $languageId, $path)
    {
        $requestArguments = array('id' => $pageId, 'L' => $languageId, 'path' => $path);
        return $this->fetchFrontendResponse($requestArguments)->getContent();
    }

    /**
     * @param array $requestArguments
     * @param bool $failOnFailure
     *
     * @return Response
     */
    protected function fetchFrontendResponse(array $requestArguments, $failOnFailure = true)
    {
        if (!empty($requestArguments['url'])) {
            $requestUrl = '/' . ltrim($requestArguments['url'], '/');
        } else {
            $requestUrl = '/?' . GeneralUtility::implodeArrayForUrl('', $requestArguments);
        }

        $arguments = array(
            'documentRoot' => $this->getInstancePath(),
            'requestUrl' => 'http://localhost' . $requestUrl,
        );

        $template = new \Text_Template('ntf://Frontend/Request.tpl');
        $template->setVar(
            array(
                'arguments' => var_export($arguments, true),
                'originalRoot' => ORIGINAL_ROOT,
                'ntfRoot' => __DIR__ . '/../../.Build/vendor/nimut/testing-framework/',
            )
        );

        if (class_exists('PHPUnit_Util_PHP')) {
            $php = \PHPUnit_Util_PHP::factory();
        } else {
            $php = DefaultPhpProcess::factory();
        }
        $response = $php->runJob($template->render());
        $result = json_decode($response['stdout'], true);

        if ($result === null) {
            $this->fail('Frontend Response is empty: ' . $response['stdout'] . $response['stderr']);
        }

        if ($failOnFailure && $result['status'] === Response::STATUS_Failure) {
            $this->fail('Frontend Response has failure:' . LF . $result['error']);
        }

        $response = new Response($result['status'], $result['content'], $result['error']);
        return $response;
    }
}
