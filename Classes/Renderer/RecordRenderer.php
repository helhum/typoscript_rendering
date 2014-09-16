<?php
namespace Helhum\TyposcriptRendering\Renderer;

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

use Helhum\TyposcriptRendering\Mvc\Response;
use Helhum\TyposcriptRendering\Mvc\Request;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class RecordRenderer
 */
class RecordRenderer implements RenderingInterface {

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param RenderingContext $renderingContext
	 * @return void
	 */
	public function renderRequest(Request $request, Response $response, RenderingContext $renderingContext) {
		$contentObjectRenderer = new ContentObjectRenderer();
		$content = $contentObjectRenderer->cObjGetSingle('RECORDS', $this->resolveRenderingConfiguration($request, $renderingContext));
		$response->setContent($content);
	}

	/**
	 * Whether the required arguments for rendering are present or not
	 *
	 * @param Request $request
	 * @return bool
	 */
	public function canRender(Request $request) {
		return $request->hasArgument('record') || $request->hasArgument('path');
	}

	/**
	 * @param Request $request
	 * @param RenderingContext $renderingContext
	 * @return array
	 */
	protected function resolveRenderingConfiguration(Request $request, RenderingContext $renderingContext) {
		if ($request->hasArgument('path')) {
			$renderingPath = $request->getArgument('path');
		}
		if ($request->hasArgument('record')) {
			if (strpos($request->getArgument('record'), '_') !== FALSE) {
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

		$configuration = array();

		$configuration['source'] = $table . '_' . $id;
		$configuration['tables'] = $table;

		if (!empty($renderingPath)) {
			$configuration['conf.'][$table] = '< ' . $renderingPath;
		}

		return $configuration;
	}
}