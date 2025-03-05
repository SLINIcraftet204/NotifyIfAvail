<?php

namespace NotifyIfAvail\Entity;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;

class NotificationRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function saveNotification(string $email, string $productId): void
    {
        $this->connection->insert('notifyifavail_plugin_notification', [
            'id' => Uuid::randomBytes(),
            'email' => $email,
            'product_id' => Uuid::fromHexToBytes($productId),
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    public function deleteNotification(string $productId): void
    {
        $this->connection->delete('notifyifavail_plugin_notification', [
            'product_id' => Uuid::fromHexToBytes($productId),
        ]);
    }

    public function getNotificationsForProduct(string $productId): array
    {
        return $this->connection->fetchFirstColumn(
            "SELECT email FROM notifyifavail_plugin_notification WHERE product_id = :productId",
            ['productId' => Uuid::fromHexToBytes($productId)]
        );
    }
}
