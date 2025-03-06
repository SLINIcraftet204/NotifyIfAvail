<?php

namespace NotifyIfAvail;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Doctrine\DBAL\Connection;

class NotifyIfAvail extends Plugin
{
    public function install(InstallContext $context): void
    {
        parent::install($context);
        $this->createDatabaseTable();
    }

    public function uninstall(UninstallContext $context): void
    {
        if (!$context->keepUserData()) {
            $connection = $this->container->get(Connection::class);
            $connection->executeStatement('DROP TABLE IF EXISTS notifyifavail_plugin_notification');
        }
        parent::uninstall($context);
    }

    private function createDatabaseTable(): void
    {
        $connection = $this->container->get(Connection::class);
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS notifyifavail_plugin_notification (
                id BINARY(16) NOT NULL,
                product_id BINARY(16) NOT NULL,
                email VARCHAR(255) NOT NULL,
                created_at DATETIME(3) NOT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
}
