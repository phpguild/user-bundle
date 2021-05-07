<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Model\PasswordRecovery;

/**
 * Interface PasswordRecoveryInterface
 */
interface PasswordRecoveryInterface
{
    public const PASSWORD_RECOVERY_AT_FIELD_NAME = 'passwordRecoveryAt';
    public const PASSWORD_RECOVERY_TOKEN_FIELD_NAME = 'passwordRecoveryToken';

    /**
     * getPasswordRecoveryAt
     *
     * @return \DateTime|null
     */
    public function getPasswordRecoveryAt(): ?\DateTime;

    /**
     * setPasswordRecoveryAt
     *
     * @param \DateTime|null $passwordRecoveryAt
     *
     * @return PasswordRecoveryInterface
     */
    public function setPasswordRecoveryAt(?\DateTime $passwordRecoveryAt): PasswordRecoveryInterface;

    /**
     * getPasswordRecoveryToken
     *
     * @return string|null
     */
    public function getPasswordRecoveryToken(): ?string;

    /**
     * setPasswordRecoveryToken
     *
     * @param string|null $passwordRecoveryToken
     *
     * @return PasswordRecoveryInterface
     */
    public function setPasswordRecoveryToken(?string $passwordRecoveryToken): PasswordRecoveryInterface;
}
