<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 *  @copyright 2024.
 */

namespace Bombardier\PopulateCatalog\Cron;

use Bombardier\PopulateCatalog\Api\Email\ReportEmailSenderInterface;

class SendReport
{
    /**
     * @var ReportEmailSenderInterface
     */
    private ReportEmailSenderInterface $reportEmailSender;

    /**
     * @param ReportEmailSenderInterface $reportEmailSender
     */
    public function __construct(ReportEmailSenderInterface $reportEmailSender)
    {
        $this->reportEmailSender = $reportEmailSender;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->reportEmailSender->sendReportEmail();
    }
}
