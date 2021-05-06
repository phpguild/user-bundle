<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Model\PasswordRecovery;

/**
 * Trait PasswordRecoveryPropertiesTrait
 */
trait PasswordRecoveryPropertiesTrait
{
    /** @var ?\DateTime $passwordRecoveryAt */
    protected $passwordRecoveryAt = null;

    /** @var string|null $passwordRecoveryToken */
    protected $passwordRecoveryToken = null;
}
