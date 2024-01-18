<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Cron;

use JulioBarbosa\BombardierPopulateCatalog\Api\CategoryManagementInterface;
use JulioBarbosa\BombardierPopulateCatalog\Api\ProductManagementInterface;
use JulioBarbosa\BombardierPopulateCatalog\Api\Report\ReportCsvGeneratorInterface;

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
     * Import Catalog from external api
     *
     * @return void
     */
    public function execute(): void
    {
        $this->categoryManagement->fetchAndProcessCategories();
        $productsToCsv = $this->productManagement->fetchAndProcessProducts();
        $this->reportCsvGenerator->generateCsv($productsToCsv);
    }
}
