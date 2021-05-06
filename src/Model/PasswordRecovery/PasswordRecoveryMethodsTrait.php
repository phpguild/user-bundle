<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Model\PasswordRecovery;

/**
 * Trait PasswordRecoveryMethodsTrait
 */
trait PasswordRecoveryMethodsTrait
{
    /**
     * getPasswordRecoveryAt
     *
     * @return \DateTime|null
     */
    public function getPasswordRecoveryAt(): ?\DateTime
    {
        return $this->passwordRecoveryAt;
    }

    /**
     * setPasswordRecoveryAt
     *
     * @param \DateTime|null $passwordRecoveryAt
     *
     * @return PasswordRecoveryInterface
     */
    public function setPasswordRecoveryAt(?\DateTime $passwordRecoveryAt): PasswordRecoveryInterface
    {
        $this->passwordRecoveryAt = $passwordRecoveryAt;

        return $this;
    }

    /**
     * getPasswordRecoveryToken
     *
     * @return string|null
     */
    public function getPasswordRecoveryToken(): ?string
    {
        return $this->passwordRecoveryToken;
    }

    /**
     * setPasswordRecoveryToken
     *
     * @param string|null $passwordRecoveryToken
     *
     * @return PasswordRecoveryInterface
     */
    public function setPasswordRecoveryToken(?string $passwordRecoveryToken): PasswordRecoveryInterface
    {
        $this->passwordRecoveryToken = $passwordRecoveryToken;

        return $this;
    }
}
