<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright (c) 2024.
 *
 */

namespace Bombardier\PopulateCatalog\Api;

use Magento\Framework\Exception\CronException;

interface ProductManagementInterface
{
    /**
     * @return array
     */
    public function fetchAndProcessProducts(): array;
}
