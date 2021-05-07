<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Security;

use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use PhpGuild\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserChecker
 */
class UserChecker implements UserCheckerInterface
{
    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * UserChecker constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * checkPreAuth
     *
     * @param BaseUserInterface $user
     */
    public function checkPreAuth(BaseUserInterface $user): void
    {
    }

    /**
     * checkPostAuth
     *
     * @param BaseUserInterface $user
     */
    public function checkPostAuth(BaseUserInterface $user): void
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        if ($user instanceof SoftDeletableInterface && $user->isDeleted()) {
            throw new CustomUserMessageAccountStatusException(
                $this->translator->trans('user_account_is_deleted')
            );
        }

        if (!$user->isConfirmed()) {
            throw new CustomUserMessageAccountStatusException(
                $this->translator->trans('user_account_is_not_confirmed')
            );
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException(
                $this->translator->trans('user_account_is_not_active')
            );
        }

        if (!$user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException(
                $this->translator->trans('user_account_is_disable')
            );
        }
    }
}
