<?php
namespace Helhum\TyposcriptRendering\ViewHelpers\Widget;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 *
 */
class LinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Initialize arguments
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
        $this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
        $this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
        $this->registerArgument('addQueryStringMethod', 'string', 'Method to be used for query string');
    }

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     * @inject
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Extbase\Service\ExtensionService
     * @inject
     */
    protected $extensionService;

    /**
     * Render the Uri.
     *
     * @param string $pluginName
     * @param string $extensionName
     * @param string $action Target action
     * @param array $arguments Arguments
     * @param string $section The anchor to be added to the URI
     * @param string $format The requested format, e.g. ".html
     * @param bool $ajax TRUE if the URI should be to an AJAX widget, FALSE otherwise.
     * @return string The rendered link
     * @api
     */
    public function render($pluginName, $extensionName, $action = null, $arguments = array(), $section = '', $format = '', $ajax = true)
    {
        if ($ajax === true) {
            $uri = $this->getAjaxUri();
        } else {
            $uri = $this->getWidgetUri();
        }
        $this->tag->addAttribute('href', $uri);
        $this->tag->setContent($this->renderChildren());
        return $this->tag->render();
    }

    /**
     * Get the URI for an AJAX Request.
     *
     * @return string the AJAX URI
     */
    protected function getAjaxUri()
    {
        list($table, $uid) = explode(':', $this->configurationManager->getContentObject()->currentRecord);
        $pluginName = $this->arguments['pluginName'];
        $extensionName = $this->arguments['extensionName'];
        $pluginNamespace = $this->extensionService->getPluginNamespace($extensionName, $pluginName);
        $arguments = $this->hasArgument('arguments') ? $this->arguments['arguments'] : array();
        $ajaxContext = array(
            'record' => $table . '_' . $uid,
            'path' => 'tt_content.list.20.' . str_replace('tx_', '', $pluginNamespace)
        );
        $additionalParams['tx_typoscriptrendering']['context'] = json_encode($ajaxContext);

        $uriBuilder = $this->controllerContext->getUriBuilder();
        $argumentPrefix = $this->controllerContext->getRequest()->getArgumentPrefix();

        $uriBuilder->reset()
            ->setArguments(array_merge(array($argumentPrefix => $arguments), $additionalParams))
            ->setSection($this->arguments['section'])
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString(array($argumentPrefix, 'cHash'))
            ->setFormat($this->arguments['format'])
            ->setUseCacheHash(true);

        // TYPO3 6.0 compatibility check:
        if (method_exists($uriBuilder, 'setAddQueryStringMethod')) {
            $uriBuilder->setAddQueryStringMethod($this->arguments['addQueryStringMethod']);
        }

        return $uriBuilder->build();
    }

    /**
     * Get the URI for a non-AJAX Request.
     *
     * @return string the Widget URI
     */
    protected function getWidgetUri()
    {
        $uriBuilder = $this->controllerContext->getUriBuilder();
        $argumentPrefix = $this->controllerContext->getRequest()->getArgumentPrefix();
        $arguments = $this->hasArgument('arguments') ? $this->arguments['arguments'] : array();
        if ($this->hasArgument('action')) {
            $arguments['action'] = $this->arguments['action'];
        }
        if ($this->hasArgument('format') && $this->arguments['format'] !== '') {
            $arguments['format'] = $this->arguments['format'];
        }
        if ($this->hasArgument('addQueryStringMethod') && $this->arguments['addQueryStringMethod'] !== '') {
            $arguments['addQueryStringMethod'] = $this->arguments['addQueryStringMethod'];
        }
        $uriBuilder->reset()
            ->setArguments(array($argumentPrefix => $arguments))
            ->setSection($this->arguments['section'])
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString(array($argumentPrefix, 'cHash'))
            ->setFormat($this->arguments['format']);

        // TYPO3 6.0 compatibility check:
        if (method_exists($uriBuilder, 'setAddQueryStringMethod')) {
            $uriBuilder->setAddQueryStringMethod($this->arguments['addQueryStringMethod']);
        }

        return $uriBuilder->build();
    }
}
