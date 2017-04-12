<?php
namespace Helhum\TyposcriptRendering\Mvc;

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

use Helhum\TyposcriptRendering\Exception;

class RequestBuilder
{
    /**
     * @param mixed $rawRequestArgument
     *
     * @throws \Helhum\TyposcriptRendering\Exception
     * @return Request
     *
     */
    public function build($rawRequestArgument)
    {
        if (empty($rawRequestArgument['context']) || !is_string($rawRequestArgument['context'])) {
            throw new Exception('tx_typoscriptrendering|context must not be empty and must be of type string!', 1403793452);
        }
        $rawRequest = @json_decode($rawRequestArgument['context'], true);
        if (null === $rawRequest) {
            throw new Exception('tx_typoscriptrendering|context must contain a valid json string!', 1466679262);
        }
        return new Request($rawRequest);
    }
}
