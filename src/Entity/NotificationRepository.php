<?php

namespace NotifyIfAvail\Repository;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class NotificationRepository
{
    private EntityRepository $repository;
    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(EntityRepository $repository, Connection $connection, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function saveNotification(string $email, string $productId): void
    {
        try {
            $notification = [
                'id' => Uuid::randomHex(),
                'email' => $email,
                'productId' => $productId,
                'createdAt' => (new \DateTime())->format(DATE_ATOM),
            ];

            $this->repository->create([$notification], \Shopware\Core\Framework\Context::createDefaultContext());
        } catch (\Exception $e) {
            $this->logger->error('Fehler beim Speichern der Benachrichtigung: ' . $e->getMessage());
        }
    }

    public function deleteNotification(string $productId): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));

        $result = $this->repository->search($criteria, \Shopware\Core\Framework\Context::createDefaultContext());

        $idsToDelete = [];
        foreach ($result->getEntities() as $entity) {
            $idsToDelete[] = ['id' => $entity->getId()];
        }

        if (!empty($idsToDelete)) {
            $this->repository->delete($idsToDelete, \Shopware\Core\Framework\Context::createDefaultContext());
        }
    }

    public function getNotificationsForProduct(string $productId): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));

        $notifications = $this->repository->search($criteria, \Shopware\Core\Framework\Context::createDefaultContext());

        return array_map(fn ($entity) => $entity->getEmail(), $notifications->getEntities()->getElements());
    }
}
