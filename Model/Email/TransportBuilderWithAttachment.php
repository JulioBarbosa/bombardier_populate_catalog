<?php
/**
 * Magento Module developed by Júlio
 *
 * @author Júlio Barbosa de Oliveira
 * @copyright 2024.
 */

namespace Bombardier\PopulateCatalog\Model\Email;

use Laminas\Mime\Message;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\MimePartFactory;
use Magento\Framework\Mail\MimeMessageFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Zend\Mime\Mime;
use function quoted_printable_decode;

class TransportBuilderWithAttachment extends TransportBuilder
{
    /**
     * @var MimePartFactory
     */
    private $mimePartFactory;
    /**
     * @var MimeMessageFactory
     */
    private MimeMessageFactory $mimeMessageFactory;
    /**
     * @var array
     */
    private $attachments = [];

    /**
     * @param MimePartFactory $mimePartFactory
     * @param MimeMessageFactory $mimeMessageFactory
     * @param FactoryInterface $factory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $transportInterfaceFactory
     * @param MessageInterfaceFactory $messageInterfaceFactory
     * @param EmailMessageInterfaceFactory $emailMessageInterfaceFactory
     * @param MimeMessageInterfaceFactory $mimeMessageInterfaceFactory
     * @param MimePartInterfaceFactory $mimePartInterfaceFactory
     * @param AddressConverter $addressConverter
     */
    public function __construct(
        MimePartFactory              $mimePartFactory,
        MimeMessageFactory           $mimeMessageFactory,
        FactoryInterface             $factory,
        MessageInterface             $message,
        SenderResolverInterface      $senderResolver,
        ObjectManagerInterface       $objectManager,
        TransportInterfaceFactory    $transportInterfaceFactory,
        MessageInterfaceFactory      $messageInterfaceFactory,
        EmailMessageInterfaceFactory $emailMessageInterfaceFactory,
        MimeMessageInterfaceFactory  $mimeMessageInterfaceFactory,
        MimePartInterfaceFactory     $mimePartInterfaceFactory,
        AddressConverter             $addressConverter
    ) {
        parent::__construct(
            $factory,
            $message,
            $senderResolver,
            $objectManager,
            $transportInterfaceFactory,
            $messageInterfaceFactory,
            $emailMessageInterfaceFactory,
            $mimeMessageInterfaceFactory,
            $mimePartInterfaceFactory,
            $addressConverter
        );
        $this->mimePartFactory = $mimePartFactory;
        $this->mimeMessageFactory = $mimeMessageFactory;
    }

    /**
     * @param $content
     * @param $fileName
     * @return $this
     */
    public function addAttachment($content, $fileName)
    {
        $attachmentPart = $this->mimePartFactory->create([
            'content' => $content,
            'type' => 'text/csv',
            'disposition' => Mime::DISPOSITION_ATTACHMENT,
            'encoding' => Mime::ENCODING_BASE64,
            'fileName' => $fileName
        ]);

        $this->attachments[] = $attachmentPart;
        return $this;
    }

    /**
     * @throws LocalizedException
     */
    protected function prepareMessage()
    {
        parent::prepareMessage();
        $parts = [$this->mimePartFactory->create([
            'content' => $this->getDecodedEmailBody($this->message),
            'type' => 'text/html'
        ])];

        foreach ($this->attachments as $attachment) {
            $parts[] = $attachment;
        }

        if (!empty($parts)) {
            $laminasMimeMessage = new Message();
            $laminasMimeMessage->setParts($parts);
            $this->message->setSubject('Report Catalog Imported');
            $this->message->setBody($laminasMimeMessage);
        }
    }

    /**
     * @param $emailMessage
     * @return string
     */
    private function getDecodedEmailBody($emailMessage)
    {
        if ($emailMessage->getBodyText()) {
            return quoted_printable_decode($emailMessage->getBodyText());
        }
        $parts = $emailMessage->getBody()->getParts();

        if (!empty($parts)) {
            return quoted_printable_decode($parts[0]->getContent());
        }
        return '';
    }
}
