<?php
namespace Helhum\TyposcriptRendering\Core;

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
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class FrontendRenderingProvisioner
 * Initializes TYPO3 TypoScriptRendering to be prepared for rendering
 *
 * @see \TYPO3\CMS\Frontend\Page\PageGenerator::pagegenInit()
 * This is a try to only initialize a basic set as not everything in pagegenInit() makes sense in our case.
 * But it might well be that it'd be better to just use the (ugly) API instead.
 * Most things I removed will hopefully be removed from the core soon anyway ;)
 */
class FrontendRenderingProvisioner
{
    /**
     * @param TypoScriptFrontendController $typoScriptFrontendController
     *
     * @return void
     */
    public function provision(TypoScriptFrontendController $typoScriptFrontendController)
    {
        $this->configureLinkBuilding($typoScriptFrontendController);
        $this->configurePageRenderer($typoScriptFrontendController);
        $this->configureImageProcessing($typoScriptFrontendController);
        $this->fixNoScriptInclude($typoScriptFrontendController);

        // Create new top level content object which is required by some rendering methods
        $typoScriptFrontendController->newCObj();

        // Bypass any other page rendering, by including our own dummy rendering
        $typoScriptFrontendController->config['config']['pageGenScript'] = 'EXT:typoscript_rendering/Scripts/DummyRendering.php';
        // Make sure current working dir is in PATH_site, so that the DummyRendering file is resolved correctly
        chdir(PATH_site);
    }

    /**
     * @param TypoScriptFrontendController $typoScriptFrontendController
     *
     * @return void
     */
    private function configureLinkBuilding(TypoScriptFrontendController $typoScriptFrontendController)
    {
        // Mount point parameters
        if ($typoScriptFrontendController->config['config']['MP_defaults']) {
            $temp_parts = GeneralUtility::trimExplode('|', $typoScriptFrontendController->config['config']['MP_defaults'], true);
            foreach ($temp_parts as $temp_p) {
                list($temp_idP, $temp_MPp) = explode(':', $temp_p, 2);
                $temp_ids = GeneralUtility::intExplode(',', $temp_idP);
                foreach ($temp_ids as $temp_id) {
                    $typoScriptFrontendController->MP_defaults[$temp_id] = $temp_MPp;
                }
            }
        }

        // Internal and External target defaults
        $typoScriptFrontendController->intTarget = '' . $typoScriptFrontendController->config['config']['intTarget'];
        $typoScriptFrontendController->extTarget = '' . $typoScriptFrontendController->config['config']['extTarget'];
        $typoScriptFrontendController->fileTarget = '' . $typoScriptFrontendController->config['config']['fileTarget'];

        // calculate the absolute path prefix
        if (!empty($typoScriptFrontendController->config['config']['absRefPrefix'])) {
            $absRefPrefix = trim($typoScriptFrontendController->config['config']['absRefPrefix']);
            if ($absRefPrefix === 'auto') {
                $typoScriptFrontendController->absRefPrefix = GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
            } else {
                $typoScriptFrontendController->absRefPrefix = $absRefPrefix;
            }
        } else {
            $typoScriptFrontendController->absRefPrefix = '';
        }

        // linkVars
        $typoScriptFrontendController->calculateLinkVars();
        $typoScriptFrontendController->ATagParams = trim($typoScriptFrontendController->config['config']['ATagParams']) ? ' ' . trim($typoScriptFrontendController->config['config']['ATagParams']) : '';

        if ($typoScriptFrontendController->config['config']['spamProtectEmailAddresses'] === 'ascii') {
            $typoScriptFrontendController->spamProtectEmailAddresses = 'ascii';
        } else {
            $typoScriptFrontendController->spamProtectEmailAddresses = \TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($typoScriptFrontendController->config['config']['spamProtectEmailAddresses'], -10, 10, 0);
        }

        // dtdAllowsFrames indicates whether to use the target attribute in links
        $typoScriptFrontendController->dtdAllowsFrames = true;
    }

    /**
     * @param TypoScriptFrontendController $typoScriptFrontendController
     *
     * @return void
     */
    private function configurePageRenderer(TypoScriptFrontendController $typoScriptFrontendController)
    {
        $pageRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Page\\PageRenderer');
        // Setting XHTML-doctype from doctype
        if (!$typoScriptFrontendController->config['config']['xhtmlDoctype']) {
            $typoScriptFrontendController->config['config']['xhtmlDoctype'] = $typoScriptFrontendController->config['config']['doctype'];
        }
        if ($typoScriptFrontendController->config['config']['xhtmlDoctype']) {
            $typoScriptFrontendController->xhtmlDoctype = $typoScriptFrontendController->config['config']['xhtmlDoctype'];
            // Checking XHTML-docytpe
            switch ((string)$typoScriptFrontendController->config['config']['xhtmlDoctype']) {
                case 'xhtml_trans':
                case 'xhtml_strict':
                case 'xhtml_frames':
                    $typoScriptFrontendController->xhtmlVersion = 100;
                    break;
                case 'xhtml_basic':
                    $typoScriptFrontendController->xhtmlVersion = 105;
                    break;
                case 'xhtml_11':
                case 'xhtml+rdfa_10':
                    $typoScriptFrontendController->xhtmlVersion = 110;
                    break;
                case 'xhtml_2':
                    $typoScriptFrontendController->xhtmlVersion = 200;
                    break;
                default:
                    $pageRenderer->setRenderXhtml(false);
                    $typoScriptFrontendController->xhtmlDoctype = '';
                    $typoScriptFrontendController->xhtmlVersion = 0;
            }
        } else {
            $pageRenderer->setRenderXhtml(false);
        }
    }

    /**
     * @param TypoScriptFrontendController $typoScriptFrontendController
     *
     * @return void
     */
    private function configureImageProcessing(TypoScriptFrontendController $typoScriptFrontendController)
    {
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_noScaleUp'] = isset($typoScriptFrontendController->config['config']['noScaleUp']) ? '' . $typoScriptFrontendController->config['config']['noScaleUp'] : $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_noScaleUp'];
        if (property_exists($typoScriptFrontendController, 'TYPO3_CONF_VARS')) {
            $typoScriptFrontendController->TYPO3_CONF_VARS['GFX']['im_noScaleUp'] = $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_noScaleUp'];
        }
    }

    /**
     * @param TypoScriptFrontendController $typoScriptFrontendController
     *
     * @return void
     */
    private function fixNoScriptInclude(TypoScriptFrontendController $typoScriptFrontendController)
    {
        $GLOBALS['TYPO3_CONF_VARS']['FE']['noPHPscriptInclude'] = false;
        if (property_exists($typoScriptFrontendController, 'TYPO3_CONF_VARS')) {
            $typoScriptFrontendController->TYPO3_CONF_VARS['FE']['noPHPscriptInclude'] = $GLOBALS['TYPO3_CONF_VARS']['FE']['noPHPscriptInclude'];
        }
    }
}
