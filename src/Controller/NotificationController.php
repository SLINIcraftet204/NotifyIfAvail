<?php

namespace NotifyIfAvail\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;

class NotificationController
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @Route("/notification/subscribe", name="frontend.notification.subscribe", methods={"POST"})
     */
    public function subscribe(Request $request): JsonResponse
    {
        $email = $request->request->get('email');
        $productId = $request->request->get('productId');

        if (!$email || !$productId) {
            return new JsonResponse(['message' => 'Invalid data'], 400);
        }

        $this->connection->insert('notifyifavail_plugin_notification', [
            'id' => Uuid::randomBytes(),
            'product_id' => Uuid::fromHexToBytes($productId),
            'email' => $email,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);

        return new JsonResponse(['message' => 'Successfully subscribed']);
    }
}
