<?php

namespace GeorgRinger\Deprecationloganalyzer;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Analyzer implements SingletonInterface
{

    /**
     * Shrink the log file
     *
     * @return array
     */
    public function getShortLog()
    {
        $logFile = GeneralUtility::getDeprecationLogFileName();

        if (!is_file($logFile)) {
            throw new \Exception('error_no-logfile-found');
        }

        $all2 = [];
        $handle = fopen($logFile, 'rb');
        $hashMap = [];
        $found = [];
        $duplicates = 0;

        if ($handle) {
            while (!feof($handle)) {
                $line = trim(fgets($handle, 4096));
                if (empty($line)) {
                    continue;
                }

                $line2 = substr($line, 16);
                $line2 = $this->stripBackTrace($line2);

                $time = substr($line, 0, 14);

                $h2 = md5($line2);
                if (isset($found[$h2])) {
                    $found[$h2]['count']++;
                    $duplicates++;
                    continue;
                } else {
                    $found[$h2] = [
                        'msg' => $line2,
                        'count' => 1
                    ];
                }

                $time2 = strtotime($time);
                if ($time2) {
                    $line2 = substr($line, 16);
//					$hash = md5($line2);
                    if (!isset($hashMap[$hash])) {
                        $all2[] = [
                            'msg' => $line2,
                            'count' => 1,
                            'time' => $time
                        ];
//						$hashMap[$hash] = 1;
                    } else {
                        $duplicates++;
                        $all2[] = [];
                    }
                } else {
                    $c = count($all2);
                    $all2[$c - 1]['msg'] .= $line;
                }
            }
            fclose($handle);
        } else {
            throw new \Exception('error_logfile-not-readable');
        }

        $response = [
            'duplicates' => $duplicates,
            'unique' => $all2,
            'fileSize' => filesize($logFile),
            'formattedFileSize' => GeneralUtility::formatSize(filesize($logFile))
        ];
        return $response;
    }

    /**
     * Try to remove backtrace to get less duplicates
     *
     * @param string $line
     * @return string
     */
    protected function stripBackTrace($line)
    {
        $keys = [' - require_once#', ' - require#', ' - include#', ' - GeneralUtility::callUserFunction#', ' - SC_alt_doc->processData#', ' - SC_alt_doc->main#'];
        $found = false;

        foreach ($keys as $key) {
            if (!$found) {
                $pos = strpos($line, $key);
                if ($pos !== false) {
                    $found = true;
                    $line = substr($line, 0, $pos);
                }
            }
        }
        return $line;
    }
}
