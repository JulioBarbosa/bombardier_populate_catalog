<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace Bombardier\PopulateCatalog\Cron;

use Bombardier\PopulateCatalog\Api\CategoryManagementInterface;
use Bombardier\PopulateCatalog\Api\ProductManagementInterface;
use Bombardier\PopulateCatalog\Api\Report\ReportCsvGeneratorInterface;
use Magento\Framework\Exception\CronException;
use function __;

class CatalogUpdate
{
    /**
     * @var CategoryManagementInterface
     */
    private CategoryManagementInterface $categoryManagement;
    /**
     * @var ProductManagementInterface
     */
    private ProductManagementInterface $productManagement;
    /**
     * @var ReportCsvGeneratorInterface
     */
    private ReportCsvGeneratorInterface $reportCsvGenerator;

    /**
     * @param CategoryManagementInterface $categoryManagement
     * @param ProductManagementInterface $productManagement
     * @param ReportCsvGeneratorInterface $reportCsvGenerator
     */
    public function __construct(
        CategoryManagementInterface $categoryManagement,
        ProductManagementInterface  $productManagement,
        ReportCsvGeneratorInterface $reportCsvGenerator
    ) {
        $this->categoryManagement = $categoryManagement;
        $this->productManagement = $productManagement;
        $this->reportCsvGenerator = $reportCsvGenerator;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $this->categoryManagement->fetchAndProcessCategories();
        $productsToCsv = $this->productManagement->fetchAndProcessProducts();
        $this->reportCsvGenerator->generateCsv($productsToCsv);
    }
}
