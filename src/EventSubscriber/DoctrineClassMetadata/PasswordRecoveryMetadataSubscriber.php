<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\EventSubscriber\DoctrineClassMetadata;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\MappingException;
use PhpGuild\UserBundle\Model\PasswordRecovery\PasswordRecoveryInterface;

/**
 * Class PasswordRecoveryMetadataSubscriber
 */
final class PasswordRecoveryMetadataSubscriber implements EventSubscriber
{
    /**
     * getSubscribedEvents
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [ Events::loadClassMetadata ];
    }

    /**
     * loadClassMetadata
     *
     * @param LoadClassMetadataEventArgs $loadClassMetadataEventArgs
     *
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $loadClassMetadataEventArgs): void
    {
        $classMetadata = $loadClassMetadataEventArgs->getClassMetadata();

        if (
            true === $classMetadata->isMappedSuperclass
            || null === $classMetadata->reflClass
            || !is_a($classMetadata->reflClass->getName(), PasswordRecoveryInterface::class, true)
        ) {
            return;
        }

        $classMetadata->mapField([
            'nullable' => true,
            'type' => Types::DATETIME_MUTABLE,
            'fieldName' => PasswordRecoveryInterface::PASSWORD_RECOVERY_AT_FIELD_NAME,
        ]);

        $classMetadata->mapField([
            'nullable' => true,
            'unique' => true,
            'type' => Types::STRING,
            'fieldName' => PasswordRecoveryInterface::PASSWORD_RECOVERY_TOKEN_FIELD_NAME,
        ]);
    }
}
