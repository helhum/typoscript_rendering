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

class Request
{
    /**
     * @var array
     */
    protected $arguments = array();

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param string $argumentName
     *
     * @return bool
     */
    public function hasArgument($argumentName)
    {
        return isset($this->arguments[$argumentName]);
    }

    /**
     * @param string $argumentName
     *
     * @throws \InvalidArgumentException
     * @return mixed
     *
     */
    public function getArgument($argumentName)
    {
        if (!isset($this->arguments[$argumentName])) {
            throw new \InvalidArgumentException('No argument found with name: ' . $argumentName, 1403562449);
        }

        return $this->arguments[$argumentName];
    }
}
