<?php

namespace NotifyIfAvail\Service;

use Doctrine\DBAL\Connection;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Shopware\Core\Framework\Uuid\Uuid;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class NotificationService
{
    private Connection $connection;
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private SystemConfigService $configService;

    public function __construct(
        Connection $connection,
        MailerInterface $mailer,
        LoggerInterface $logger,
        SystemConfigService $configService
    ) {
        $this->connection = $connection;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->configService = $configService;
    }

    public function saveNotification(string $email, string $productId): void
    {
        try {
            $this->connection->insert('notifyifavail_plugin_notification', [
                'id' => Uuid::randomBytes(),
                'email' => $email,
                'product_id' => Uuid::fromHexToBytes($productId),
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Fehler beim Speichern der Benachrichtigung: ' . $e->getMessage());
            throw $e;
        }
    }

    public function notifyCustomers(string $productId, string $productName, string $productUrl, string $shopName): void
    {
        try {
            $emails = $this->connection->fetchFirstColumn(
                "SELECT email FROM notifyifavail_plugin_notification WHERE product_id = :productId",
                ['productId' => Uuid::fromHexToBytes($productId)]
            );

            $emailSender = $this->configService->get('NotifyIfAvail.config.emailSender') ?? 'shop@example.com';
            $emailSubject = $this->configService->get('NotifyIfAvail.config.emailSubject') ?? 'Ihr gewünschter Artikel ist wieder verfügbar!';

            foreach ($emails as $email) {
                $message = (new Email())
                    ->from($emailSender)
                    ->to($email)
                    ->subject($emailSubject)
                    ->html("
                        <p>Hallo,</p>
                        <p>Sie haben sich für eine Benachrichtigung angemeldet, sobald der folgende Artikel wieder verfügbar ist:</p>
                        <p><strong>{$productName}</strong></p>
                        <p><a href='{$productUrl}'>Jetzt bestellen</a></p>
                        <p>Vielen Dank für Ihr Interesse an unseren Produkten!</p>
                        <p>Beste Grüße,<br>{$shopName}-Team</p>
                    ");

                $this->mailer->send($message);
            }

            $this->connection->executeStatement(
                "DELETE FROM notifyifavail_plugin_notification WHERE product_id = :productId",
                ['productId' => Uuid::fromHexToBytes($productId)]
            );
        } catch (\Exception $e) {
            $this->logger->error('Fehler beim Versand der Benachrichtigungen: ' . $e->getMessage());
        }
    }
}
