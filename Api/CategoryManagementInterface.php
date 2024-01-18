<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace Bombardier\PopulateCatalog\Api;

interface CategoryManagementInterface
{
    public function fetchAndProcessCategories(): void;
}
