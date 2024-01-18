<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Api;

interface ProductManagementInterface
{
    /**
     * @return array
     */
    public function fetchAndProcessProducts(): array;
}
