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
use TYPO3\CMS\Core\Tests\Functional\Framework\Frontend\Response;
use TYPO3\CMS\Core\Tests\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RenderingTest
 */
abstract class AbstractRenderingTestCase extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = array('typo3conf/ext/typoscript_rendering');

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = array('fluid');

    public function setUp()
    {
        parent::setUp();
        $this->setUpFrontendRootPage(1);
    }

    /* ***********************************************
     * Utility methods for browsing a frontend page
     * ***********************************************/

    /**
     * @param int $pageId
     * @param int $languageId
     * @param string $path
     * @return string
     */
    protected function getRenderUrl($pageId, $languageId, $path)
    {
        $requestArguments = array('id' => $pageId, 'L' => $languageId, 'path' => $path);
        return $this->fetchFrontendResponse($requestArguments)->getContent();
    }

    /**
     * @param int $pageId
     * @param array $typoScriptFiles
     */
    protected function setUpFrontendRootPage($pageId, array $typoScriptFiles = array())
    {
        $page = array(
            'uid' => $pageId,
            'title' => 'root'
        );
        $this->getDatabaseConnection()->exec_INSERTquery('pages', $page);
        parent::setUpFrontendRootPage($pageId, array('EXT:typoscript_rendering/Tests/Functional/Fixtures/Frontend/Basic.ts'));
    }

    /**
     * @param array $requestArguments
     * @param bool $failOnFailure
     * @return Response
     */
    protected function fetchFrontendResponse(array $requestArguments, $failOnFailure = true)
    {
        if (!empty($requestArguments['url'])) {
            $requestUrl = '/' . ltrim($requestArguments['url'], '/');
        } else {
            $requestUrl = '/?' . GeneralUtility::implodeArrayForUrl('', $requestArguments);
        }

        if (property_exists($this, 'instancePath')) {
            $instancePath = $this->instancePath;
        } else {
            $instancePath = ORIGINAL_ROOT . 'typo3temp/functional-' . substr(sha1(get_class($this)), 0, 7);
        }
        $arguments = array(
            'documentRoot' => $instancePath,
            'requestUrl' => 'http://localhost' . $requestUrl,
        );

        $template = new \Text_Template(ORIGINAL_ROOT . 'typo3/sysext/core/Tests/Functional/Fixtures/Frontend/request.tpl');
        $template->setVar(
            array(
                'arguments' => var_export($arguments, true),
                'originalRoot' => ORIGINAL_ROOT,
            )
        );

        $php = \PHPUnit_Util_PHP::factory();
        $response = $php->runJob($template->render());
        $result = json_decode($response['stdout'], true);

        if ($result === null) {
            $this->fail('Frontend Response is empty');
        }

        if ($failOnFailure && $result['status'] === Response::STATUS_Failure) {
            $this->fail('Frontend Response has failure:' . LF . $result['error']);
        }

        $response = new Response($result['status'], $result['content'], $result['error']);
        return $response;
    }
}
