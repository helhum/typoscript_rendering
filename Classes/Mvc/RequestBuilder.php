<?php
namespace Helhum\TyposcriptRendering\Mvc;

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

use Helhum\TyposcriptRendering\Exception;

class RequestBuilder
{
    /**
     * @param array $rawRequestArgument
     *
     * @return Request
     *
     * @throws \Helhum\TyposcriptRendering\Exception
     */
    public function build(array $rawRequestArgument)
    {
        if (empty($rawRequestArgument['context']) || !is_string($rawRequestArgument['context'])) {
            throw new Exception('tx_typoscriptrendering|context must not be empty and must be of type string!', 1403793452);
        }
        return new Request(json_decode($rawRequestArgument['context'], true));
    }
}
