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

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class RenderingContext
{
    /**
     * @var TypoScriptFrontendController
     */
    protected $frontendController;

    /**
     * Constructor.
     *
     * @param TypoScriptFrontendController $frontendController
     */
    public function __construct(TypoScriptFrontendController $frontendController)
    {
        $this->frontendController = $frontendController;
    }

    /**
     * @return TypoScriptFrontendController
     */
    public function getFrontendController()
    {
        return $this->frontendController;
    }
}
