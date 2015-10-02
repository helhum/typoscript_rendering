TypoScript Rendering [![Build Status](https://travis-ci.org/helhum/typoscript_rendering.svg?branch=master)](https://travis-ci.org/helhum/typoscript_rendering)
=================

This extension provides a possibiltiy to render arbitrary TypoScript paths in a given record context.

This is espeacially useful for rendering a plugin via an AjaxRequest

Composer installation
---------------------

As TYPO3 core aims to embrace composer more and more, it might be helpful to know how to install the extension via composer. For this, not the github repository is used, but the official TER, so the same source you use for installation via extension manager.
TYPO3 has an own packagist service that distributes TER extensions for composer installation.

For this to work, copy the repository block as shown in the [information page](https://composer.typo3.org/satis.html#!/typoscript-rendering) into your composer.json file and then run the command _composer require typo3-ter/typoscript-rendering_ on command line.
