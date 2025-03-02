<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;

class UserRoleSyncSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->syncRoles($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->syncRoles($args);
    }

    private function syncRoles(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof User) {
            return;
        }

        if (in_array('ROLE_ADMIN', $entity->getRoles())) {
            $entity->setAdminVerified(true);
        } else {
            $entity->setAdminVerified(false);
        }
    }
}
