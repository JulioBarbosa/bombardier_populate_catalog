<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 *  @copyright 2024.
 */

namespace JulioBarbosa\BombardierPopulateCatalog\Model\Email;

use JulioBarbosa\BombardierPopulateCatalog\Api\Email\ReportEmailSenderInterface;
use JulioBarbosa\BombardierPopulateCatalog\Model\Report\ReportCsvGenerator;
use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CronException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Filesystem;
use function __;
use function basename;
use function explode;
use function file_get_contents;
use function glob;
use function is_file;
use function unlink;

class ReportEmailSender implements ReportEmailSenderInterface
{
    /**
     * @var TransportBuilderWithAttachment
     */
    private $transportBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Escaper
     */
    private $escaper;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @param TransportBuilderWithAttachment $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param Escaper $escaper
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     */
    public function __construct(
        TransportBuilderWithAttachment $transportBuilder,
        StoreManagerInterface $storeManager,
        Escaper $escaper,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->scopeConfig = $scopeConfig;
        $this->filesystem = $filesystem;
    }

    /**
     * Send report email and delete files
     *
     * @return void
     * @throws CronException
     */
    public function sendReportEmail(): void
    {
        try {
            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()
            ];
            $emails = $this->getEmails();
            $emailList = explode(',', $emails);

            $folderPath = $this->filesystem
                ->getDirectoryRead(DirectoryList::VAR_DIR)
                ->getAbsolutePath(ReportCsvGenerator::PATH);
            $files = glob($folderPath . '*');

            foreach ($emailList as $email) {
                $totalProducts = 0;
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier('report_email_template')
                    ->setTemplateOptions($templateOptions)
                    ->setFrom([
                        'name' => 'Sr Admin',
                        'email' => $email
                    ])
                    ->addTo($email);

                foreach ($files as $file) {
                    $totalProducts = $totalProducts + $this->extractTotalProducts($file);
                    $transport->addAttachment(
                        file_get_contents($file),
                        basename($file)
                    );
                }
                $transport->setTemplateVars(['totalProducts' => $totalProducts]);
                $transport = $transport->getTransport();

                $transport->sendMessage();
            }

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        } catch (Exception $exception) {
            throw new CronException(__($exception->getMessage()), $exception);
        }
    }

    /**
     * Get Emails
     *
     * @return string
     */
    private function getEmails(): string
    {
        return $this->scopeConfig->getValue('bombardier/email_settings/email_recipients', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Extract Total Products
     *
     * @param $filePath
     * @return int
     */
    private function extractTotalProducts($filePath): int
    {
        $fileName = basename($filePath);
        $matches = [];

        if (preg_match('/^(\d+)-/', $fileName, $matches)) {
            return (int)$matches[1];
        }
        return 0;
    }
}
