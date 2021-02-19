<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Model;

use PhpGuild\DoctrineExtraBundle\Model\IdInterface;

/**
 * Interface UserInterface
 */
interface UserInterface extends IdInterface, \Symfony\Component\Security\Core\User\UserInterface
{
}
