<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Model;

use PhpGuild\DoctrineExtraBundle\Model\Enabled\EnabledInterface;
use PhpGuild\DoctrineExtraBundle\Model\IdInterface;
use PhpGuild\DoctrineExtraBundle\Model\Confirmed\ConfirmedInterface;
use PhpGuild\UserBundle\Model\PasswordRecovery\PasswordRecoveryInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * Interface UserInterface
 */
interface UserInterface extends
    IdInterface,
    EnabledInterface,
    ConfirmedInterface,
    PasswordRecoveryInterface,
    BaseUserInterface,
    EquatableInterface
{
    /**
     * getEmail
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * setEmail
     *
     * @param string|null $email
     *
     * @return UserInterface
     */
    public function setEmail(?string $email): UserInterface;

    /**
     * setUsername
     *
     * @param string|null $username
     *
     * @return UserInterface
     */
    public function setUsername(?string $username): UserInterface;

    /**
     * setRoles
     *
     * @param array $roles
     *
     * @return UserInterface
     */
    public function setRoles(array $roles): UserInterface;

    /**
     * setSalt
     *
     * @param string|null $salt
     *
     * @return UserInterface
     */
    public function setSalt(?string $salt): UserInterface;

    /**
     * setPassword
     *
     * @param string|null $password
     *
     * @return UserInterface
     */
    public function setPassword(?string $password): UserInterface;

    /**
     * getPlainPassword
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string;

    /**
     * setPlainPassword
     *
     * @param string|null $plainPassword
     *
     * @return UserInterface
     */
    public function setPlainPassword(?string $plainPassword): UserInterface;
}
