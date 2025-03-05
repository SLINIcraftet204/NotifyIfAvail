<?php

namespace NotifyIfAvail\Entity;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(Notification $entity)
 * @method void set(string $key, Notification $entity)
 * @method Notification[] getIterator()
 * @method Notification[] getElements()
 * @method Notification|null get(string $key)
 * @method Notification|null first()
 * @method Notification|null last()
 */
class NotificationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return Notification::class;
    }
}
