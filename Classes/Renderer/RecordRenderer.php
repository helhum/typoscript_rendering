<?php
namespace Helhum\TyposcriptRendering\Renderer;

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
use Helhum\TyposcriptRendering\Mvc\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class RecordRenderer
 */
class RecordRenderer implements RenderingInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @param RenderingContext $renderingContext
     *
     * @return void
     */
    public function renderRequest(Request $request, Response $response, RenderingContext $renderingContext)
    {
        $contentObjectRenderer = new ContentObjectRenderer();
        $content = $contentObjectRenderer->cObjGetSingle('RECORDS', $this->resolveRenderingConfiguration($request, $renderingContext));
        $response->setContent($content);
    }

    /**
     * Whether the required arguments for rendering are present or not
     *
     * @param Request $request
     *
     * @return bool
     */
    public function canRender(Request $request)
    {
        return $request->hasArgument('record') || $request->hasArgument('path');
    }

    /**
     * @param Request $request
     * @param RenderingContext $renderingContext
     *
     * @return array
     */
    protected function resolveRenderingConfiguration(Request $request, RenderingContext $renderingContext)
    {
        $configuration = [];

        if ($request->hasArgument('path')) {
            $renderingPath = $request->getArgument('path');
        }
        if ($request->hasArgument('record')) {
            if (strpos($request->getArgument('record'), '_') !== false) {
                list($table, $id) = GeneralUtility::revExplode('_', $request->getArgument('record'), 2);
            } else {
                $id = $request->getArgument('record');
            }
        }
        if ($request->hasArgument('table')) {
            $table = $request->getArgument('table');
        }

        if (empty($table) && empty($id)) {
            $table = 'pages';
            $id = $renderingContext->getFrontendController()->id;
        }

        if (!empty($id) && empty($table)) {
            $table = 'tt_content';
        }

        if ($table === 'pages') {
            // Allow rendering of a root page which has pid === 0 and would be denied otherwise
            $rootLine = GeneralUtility::makeInstance(RootlineUtility::class, (int)$id)->get();
            // $rootLine[0] is the root page. Check if the page we're going to render is a root page.
            // We explicitly ignore the case where the to be rendered id is in another root line (multi domain setup)
            // as this would require an additional record lookup. The use case for this is very limited anyway
            // and should be implemented in a different renderer instead of covering that here.
            if ((int)$rootLine[0]['uid'] === (int)$id) {
                $configuration['dontCheckPid'] = '1';
            }
        }

        $configuration['source'] = $table . '_' . $id;
        $configuration['tables'] = $table;

        if (!empty($renderingPath)) {
            $configuration['conf.'][$table] = '< ' . $renderingPath;
        }

        return $configuration;
    }
}
