<?php
namespace Helhum\TyposcriptRendering\Mvc;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Helhum\TyposcriptRendering\Exception;

/**
 * Class RequestBuilder
 */
class RequestBuilder
{
    /**
     * @param array $rawRequestArgument
     * @return Request
     * @throws \Helhum\TyposcriptRendering\Exception
     */
    public function build($rawRequestArgument)
    {
        if (empty($rawRequestArgument['context']) || !is_string($rawRequestArgument['context'])) {
            throw new Exception('tx_typoscriptrendering|context must not be empty and must be of type string!', 1403793452);
        }
        return new Request(json_decode($rawRequestArgument['context'], true));
    }
}
