<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Account;

use Doctrine\ORM\EntityManagerInterface;
use PhpGuild\UserBundle\Model\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AccountConfirmation
 */
class AccountConfirmation
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * AccountConfirmation constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * request
     *
     * @param UserInterface $user
     *
     * @throws \Exception
     */
    public function request(UserInterface $user): void
    {
        if ($user->getConfirmedAt()) {
            throw new AccountConfirmationException(
                $this->translator->trans('user_account_confirmation_already_confirmed')
            );
        }

        $user->setConfirmedToken(hash('sha256', random_bytes(32)));
        $this->entityManager->flush();
    }

    /**
     * validate
     *
     * @param string $userClassName
     * @param string $token
     *
     * @return UserInterface
     *
     * @throws AccountConfirmationException
     */
    public function validate(string $userClassName, string $token): UserInterface
    {
        if ('' === $token) {
            throw new AccountConfirmationException(
                $this->translator->trans('user_account_confirmation_token_invalid')
            );
        }

        $userRepository = $this->entityManager->getRepository($userClassName);

        /** @var UserInterface $user */
        $user = $userRepository->findOneBy([
            'confirmedToken' => $token,
        ]);

        if (!$user) {
            throw new AccountConfirmationException(
                $this->translator->trans('user_account_confirmation_token_expired')
            );
        }

        $user->setConfirmedToken(null);

        if ($user->getConfirmedAt()) {
            $this->entityManager->flush();
            throw new AccountConfirmationException(
                $this->translator->trans('user_account_confirmation_already_confirmed')
            );
        }

        $user->setConfirmedAt(new \DateTime());
        $this->entityManager->flush();

        return $user;
    }
}
