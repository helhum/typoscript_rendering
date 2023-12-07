<?php
declare(strict_types=1);

namespace Helhum\TyposcriptRendering\Uri;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class ViewHelperContext
{
    /**
     * @var RenderingContextInterface
     */
    private $renderingContext;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    public function __construct(RenderingContextInterface $renderingContext, array $arguments, ConfigurationManager $configurationManager = null)
    {
        $this->renderingContext = $renderingContext;
        $this->arguments = $arguments;
        $this->configurationManager = $configurationManager;
    }


    public function getRequest()
    {
        if ($this->renderingContext instanceof \TYPO3\CMS\Fluid\Core\Rendering\RenderingContext) {
            return $this->renderingContext->getRequest();
        }
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getContentObject(): ContentObjectRenderer
    {
        $configurationManager = $this->configurationManager ?? GeneralUtility::makeInstance(ConfigurationManager::class);
        $contentObject = $configurationManager->getContentObject();

        return $contentObject ?? GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }
}
