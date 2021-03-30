<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\EventSubscriber;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use PhpGuild\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserSubscriber
 */
final class UserSubscriber implements EventSubscriber
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /**
     * UserSubscriber constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
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
     */
    public function resolveObject(UserInterface $entity): void
    {
        if (!$entity->getSalt()) {
            $entity->setSalt(hash_hmac('sha256', uniqid('', true), microtime()));
        }

        if (!$entity->getUsername()) {
            $entity->setUsername($entity->getEmail());
        }

        if ($entity->getPlainPassword()) {
            $entity->setPassword($this->encoder->encodePassword($entity, $entity->getPlainPassword()));
        }

        if (!$entity->getPassword()) {
            $entity->setPassword(hash_hmac('sha256', uniqid('', true), microtime()));
        }
    }
}
