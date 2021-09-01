<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Model;

use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use PhpGuild\DoctrineExtraBundle\Model\Enabled\EnabledTrait;
use PhpGuild\DoctrineExtraBundle\Model\Confirmed\ConfirmedTrait;
use PhpGuild\UserBundle\Model\PasswordRecovery\PasswordRecoveryTrait;

/**
 * Trait UserTrait
 */
trait UserTrait
{
    use EnabledTrait;
    use ConfirmedTrait;
    use PasswordRecoveryTrait;

    /**
     * isActive
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isConfirmed()
            && $this->isEnabled()
            && (!$this instanceof SoftDeletableInterface || !$this->isDeleted());
    }

    /**
     * getEmail
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * setEmail
     *
     * @param string|null $email
     *
     * @return UserInterface
     */
    public function setEmail(?string $email): UserInterface
    {
        $this->email = $email;

        return $this;
    }

    /**
     * getUserIdentifier
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * getUsername
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * setUsername
     *
     * @param string|null $username
     *
     * @return UserInterface
     */
    public function setUsername(?string $username): UserInterface
    {
        $this->username = $username;

        return $this;
    }

    /**
     * getRoles
     *
     * @return array|string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * setRoles
     *
     * @param array $roles
     *
     * @return UserInterface
     */
    public function setRoles(array $roles): UserInterface
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * getSalt
     *
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * setSalt
     *
     * @param string|null $salt
     *
     * @return UserInterface
     */
    public function setSalt(?string $salt): UserInterface
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * getPassword
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * setPassword
     *
     * @param string|null $password
     *
     * @return UserInterface
     */
    public function setPassword(?string $password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    /**
     * getPlainPassword
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * setPlainPassword
     *
     * @param string|null $plainPassword
     *
     * @return UserInterface
     */
    public function setPlainPassword(?string $plainPassword): UserInterface
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * eraseCredentials
     *
     * @return UserInterface
     */
    public function eraseCredentials(): UserInterface
    {
        $this->plainPassword = null;

        return $this;
    }
}
