<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 *  @copyright 2024.
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Cron;

use JulioBarbosa\BombardierPopulateCatalog\Api\Email\ReportEmailSenderInterface;

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
     * Send email to receives
     *
     * @return void
     */
    public function execute()
    {
        $this->reportEmailSender->sendReportEmail();
    }
}
