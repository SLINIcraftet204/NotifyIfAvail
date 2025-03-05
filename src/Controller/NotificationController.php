<?php

namespace NotifyIfAvail\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use NotifyIfAvail\Service\NotificationService;
use Psr\Log\LoggerInterface;

class NotificationController
{
    private NotificationService $notificationService;
    private LoggerInterface $logger;

    public function __construct(NotificationService $notificationService, LoggerInterface $logger)
    {
        $this->notificationService = $notificationService;
        $this->logger = $logger;
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

        try {
            $this->notificationService->saveNotification($email, $productId);
            return new JsonResponse(['message' => 'Successfully subscribed']);
        } catch (\Exception $e) {
            $this->logger->error('Fehler beim Speichern der Benachrichtigung: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred'], 500);
        }
    }
}
