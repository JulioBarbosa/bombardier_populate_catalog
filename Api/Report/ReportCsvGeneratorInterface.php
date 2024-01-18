<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 *  @copyright 2024.
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Api\Report;

interface ReportCsvGeneratorInterface
{
    /**
     * @param $reportData
     * @return string
     */
    public function generateCsv($reportData): string;
}
