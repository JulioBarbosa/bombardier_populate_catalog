<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright 2024.
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Model\Report;

use JulioBarbosa\BombardierPopulateCatalog\Api\Report\ReportCsvGeneratorInterface;
use DateTime;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use RuntimeException;
use function array_filter;
use function count;
use function fclose;
use function fopen;
use function fputcsv;
use function is_int;
use function is_string;

class ReportCsvGenerator implements ReportCsvGeneratorInterface
{
    public const PATH = '/export/report_catalog_imported/';
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param DirectoryList $directoryList
     */
    public function __construct(
        DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    /**
     * Generate Csv
     *
     * @param $reportData
     * @return string
     * @throws FileSystemException
     */
    public function generateCsv($reportData): string
    {
        $countLines = count($reportData);
        $date = new DateTime();
        $formattedDate = $date->format('Y-m-d_H-i-s');
        $filePath = $this->directoryList->getPath(DirectoryList::VAR_DIR) . self::PATH;

        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $filePath = $filePath . $countLines . '-' . $formattedDate . '.csv';
        $fileHandle = fopen($filePath, 'w');

        if ($fileHandle === false) {
            throw new RuntimeException('It was not possible to open the file for writing:' . $filePath);
        }

        foreach ($reportData as $line) {
            $line = $this->filterIntAndStringColumns($line);
            fputcsv($fileHandle, $line);
        }
        fclose($fileHandle);

        return $filePath;
    }

    /**
     * Filter Int And String Columns
     *
     * @param $array
     * @return array
     */
    function filterIntAndStringColumns($array): array
    {
        return array_filter($array, function($value) {
            return is_int($value) || is_string($value);
        });
    }
}
