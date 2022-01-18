<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\EventSubscriber;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use PhpGuild\UserBundle\Model\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserSubscriber
 */
final class UserSubscriber implements EventSubscriber
{
    /** @var UserPasswordHasherInterface $encoder */
    private $encoder;

    /**
     * UserSubscriber constructor.
     *
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * prePersist
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof UserInterface) {
            return;
        }

        $this->resolveObject($entity);
    }

    /**
     * preUpdate
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof UserInterface) {
            return;
        }

        $this->resolveObject($entity);
    }

    /**
     * resolveObject
     *
     * @param UserInterface $entity
     *
     * @throws \Exception
     */
    public function resolveObject(UserInterface $entity): void
    {
        if (!$entity->getSalt()) {
            $entity->setSalt(hash_hmac('sha256', random_bytes(128), random_bytes(128)));
        }

        if (!$entity->getUsername()) {
            $entity->setUsername($entity->getEmail());
        }

        if ($entity->getPlainPassword()) {
            $entity->setPassword($this->encoder->encodePassword($entity, $entity->getPlainPassword()));
            $entity->eraseCredentials();
        }

        if (!$entity->getPassword()) {
            $entity->setPassword(hash_hmac('sha256', random_bytes(128), random_bytes(128)));
        }
    }
}
