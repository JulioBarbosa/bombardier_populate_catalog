<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Api;

interface CategoryManagementInterface
{
    /**
     * @return void
     */
    public function fetchAndProcessCategories(): void;
}
