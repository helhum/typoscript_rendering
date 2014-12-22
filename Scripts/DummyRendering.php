<?php
\TYPO3\CMS\Frontend\Page\PageGenerator::pagegenInit();
// Global content object
$TSFE->newCObj();
// LIBRARY INCLUSION, TypoScript
$temp_incFiles = \TYPO3\CMS\Frontend\Page\PageGenerator::getIncFiles();
foreach ($temp_incFiles as $temp_file) {
	include_once './' . $temp_file;
}
