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

interface RenderingInterface
{
    /**
     * Evaluates request arguments, renders a string based on them
     * and sets the string content to the response.
     *
     * @param Request $request
     * @param Response $response
     * @param RenderingContext $renderingContext
     *
     * @return void
     */
    public function renderRequest(Request $request, Response $response, RenderingContext $renderingContext);

    /**
     * Whether the required arguments for rendering are present or not
     *
     * @param Request $request
     *
     * @return bool
     */
    public function canRender(Request $request);
}
