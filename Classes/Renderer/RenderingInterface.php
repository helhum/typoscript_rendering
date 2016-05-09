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

use Helhum\TyposcriptRendering\Mvc\Request;
use Helhum\TyposcriptRendering\Mvc\Response;

/**
 * Interface RenderingInterface
 */
interface RenderingInterface
{
    /**
     * Evaluates request arguments, renders a string based on them
     * and sets the string content to the response.
     *
     * @param Request $request
     * @param Response $response
     * @param RenderingContext $renderingContext
     * @return void
     */
    public function renderRequest(Request $request, Response $response, RenderingContext $renderingContext);

    /**
     * Whether the required arguments for rendering are present or not
     *
     * @param Request $request
     * @return bool
     */
    public function canRender(Request $request);
}
