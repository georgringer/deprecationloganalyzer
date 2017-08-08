<?php

namespace GeorgRinger\Deprecationloganalyzer;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Reports\Controller\ReportController;
use TYPO3\CMS\Reports\ReportInterface;

class Report implements ReportInterface
{

    /**
     * @var ReportController
     */
    protected $reportsModule;

    /**
     * Constructor
     *
     * @param ReportController $reportsModule Back-reference to the calling reports module
     */
    public function __construct(ReportController $reportsModule)
    {
        $this->reportsModule = $reportsModule;
    }

    /**
     * This method renders the report
     *
     * @return string The status report as HTML
     */
    public function getReport()
    {
        // Rendering of the output via fluid
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:deprecationloganalyzer/Resources/Private/Templates/Report.html'
        ));

        if (!$this->isEnabled()) {
            $view->assign('isDisabled', true);
        } else {
            try {
                $analyzer = GeneralUtility::makeInstance(Analyzer::class);
                $view->assignMultiple($analyzer->getShortLog());
            } catch (\Exception $e) {
                $view->assign('error', $e);
            }
        }

        return $view->render();
    }

    /**
     * Render single line
     *
     * @param array $msg
     * @return string
     */
    protected function renderSingleLine(array $msg)
    {
        $content = '<span style="display:block;font-family:courier;margin-bottom:14px;">' . htmlspecialchars($msg['msg']) . '</span>';

        return $content;
    }

    /**
     * Check if deprecation log is enabled
     *
     * @return bool
     */
    protected function isEnabled()
    {
        $log = $GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'];

        // legacy values (no strict comparison, $log can be boolean, string or int)
        if ($log === true || (int)$log === 1) {
            $log = 'file';
        }

        if (stripos($log, 'file') !== false) {
            return true;
        }
        return false;
    }
}
