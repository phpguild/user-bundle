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
     * confirmUser
     *
     * @param string $userClassName
     * @param string $token
     *
     * @return UserInterface
     *
     * @throws AccountConfirmationException
     */
    public function confirmUser(string $userClassName, string $token): UserInterface
    {
        $userRepository = $this->entityManager->getRepository($userClassName);

        /** @var UserInterface $user */
        $user = $userRepository->findOneBy([
            'confirmedToken' => $token,
        ]);

        if (!$user) {
            throw new AccountConfirmationException($this->translator->trans('user_account_confirmation_token_expired'));
        }

        $user->setConfirmedAt(new \DateTime());
        $user->setConfirmedToken(null);
        $this->entityManager->flush();

        return $user;
    }
}
