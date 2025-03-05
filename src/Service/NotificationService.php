<?php

namespace NotifyIfAvail\Service;

use Doctrine\DBAL\Connection;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Shopware\Core\Framework\Uuid\Uuid;

class NotificationService
{
    private Connection $connection;
    private MailerInterface $mailer;

    public function __construct(Connection $connection, MailerInterface $mailer)
    {
        $this->connection = $connection;
        $this->mailer = $mailer;
    }

    public function notifyCustomers(string $productId, string $productName, string $productUrl, string $shopName): void
    {
        $sql = "SELECT email FROM notifyifavail_plugin_notification WHERE product_id = :productId";
        $emails = $this->connection->fetchFirstColumn($sql, ['productId' => Uuid::fromHexToBytes($productId)]);

        foreach ($emails as $email) {
            $message = (new Email())
                ->from('shop@example.com')
                ->to($email)
                ->subject('Ihr gewünschter Artikel ist wieder verfügbar!')
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

        // Nach Versand aus der Datenbank löschen
        $this->connection->executeStatement("DELETE FROM notifyifavail_plugin_notification WHERE product_id = :productId", [
            'productId' => Uuid::fromHexToBytes($productId)
        ]);
    }
}
