<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    // Registering the report
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_deprecationloganalyzer']['index'] = [
        'title' => 'LLL:EXT:deprecationloganalyzer/Resources/Private/Language/locallang.xml:report.title',
        'description' => 'LLL:EXT:deprecationloganalyzer/Resources/Private/Language/locallang.xml:report.description',
        'report' => \GeorgRinger\Deprecationloganalyzer\Report::class,
        'icon' => 'EXT:deprecationloganalyzer/Resources/Public/Icons/report_icon.png'
    ];
}
