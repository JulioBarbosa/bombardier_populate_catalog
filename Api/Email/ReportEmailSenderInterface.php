<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 *  @copyright 2024.
 */

namespace Bombardier\PopulateCatalog\Api\Email;

interface ReportEmailSenderInterface
{
    /**
     * @return mixed
     */
    public function sendReportEmail();
}
