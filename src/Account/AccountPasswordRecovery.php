<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Account;

use Doctrine\ORM\EntityManagerInterface;
use PhpGuild\UserBundle\Model\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AccountPasswordRecovery
 */
class AccountPasswordRecovery
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var ValidatorInterface $validator */
    private $validator;

    /** @var UserPasswordHasherInterface $encoder */
    private $encoder;

    /**
     * AccountPasswordRecovery constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param TranslatorInterface          $translator
     * @param ValidatorInterface           $validator
     * @param UserPasswordHasherInterface  $encoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $encoder
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->validator = $validator;
        $this->encoder = $encoder;
    }

    /**
     * request
     *
     * @param string $userClassName
     * @param string $email
     *
     * @return UserInterface
     *
     * @throws AccountConfirmationException
     * @throws \Exception
     */
    public function request(string $userClassName, string $email): UserInterface
    {
        $userRepository = $this->entityManager->getRepository($userClassName);

        /** @var UserInterface $user */
        $user = $userRepository->findOneBy([
            'email' => $email,
        ]);

        if (!$user) {
            throw new AccountConfirmationException(
                $this->translator->trans('user_account_password_recovery_unknown_email')
            );
        }

        if (!$user->isActive()) {
            throw new AccountConfirmationException(
                $this->translator->trans('user_account_is_disable')
            );
        }

        $expiredAt = (new \DateTime())->modify('-24 hours')->format('Y-m-d H:i:s');
        $passwordRecoveryAt = $user->getPasswordRecoveryAt() instanceof \DateTime
            ? $user->getPasswordRecoveryAt()->format('Y-m-d H:i:s')
            : null;

        if ($passwordRecoveryAt && $passwordRecoveryAt > $expiredAt) {
            throw new AccountConfirmationException(
                $this->translator->trans('user_account_password_recovery_already_send')
            );
        }

        $user->setPasswordRecoveryAt(new \DateTime());
        $user->setPasswordRecoveryToken(hash('sha256', random_bytes(32)));
        $this->entityManager->flush();

        return $user;
    }

    /**
     * validate
     *
     * @param string $userClassName
     * @param string $token
     * @param string $password
     *
     * @return UserInterface
     *
     * @throws AccountPasswordRecoveryException
     * @throws AccountPasswordRecoveryViolationException
     */
    public function validate(string $userClassName, string $token, string $password): UserInterface
    {
        if ('' === $token) {
            throw new AccountPasswordRecoveryException(
                $this->translator->trans('user_account_password_reset_token_invalid')
            );
        }

        $userRepository = $this->entityManager->getRepository($userClassName);

        /** @var UserInterface $user */
        $user = $userRepository->findOneBy([
            'passwordRecoveryToken' => $token,
        ]);

        if (!$user) {
            throw new AccountPasswordRecoveryException(
                $this->translator->trans('user_account_confirmation_token_expired')
            );
        }

        if (!$user->isActive()) {
            throw new AccountPasswordRecoveryException(
                $this->translator->trans('user_account_is_disable')
            );
        }

        $expiredAt = (new \DateTime())->modify('-24 hours')->format('Y-m-d H:i:s');
        $passwordRecoveryAt = $user->getPasswordRecoveryAt() instanceof \DateTime
            ? $user->getPasswordRecoveryAt()->format('Y-m-d H:i:s')
            : null;

        if (!$passwordRecoveryAt || $passwordRecoveryAt < $expiredAt) {
            throw new AccountPasswordRecoveryException(
                $this->translator->trans('user_account_confirmation_token_expired')
            );
        }

        $user->setPassword(null);
        $user->setPlainPassword($password);

        $errors = $this->validator->validate($user);
        if (\count($errors)) {
            $messages = '';
            foreach ($errors as $error) {
                $messages .= $error->getMessage();
            }
            throw new AccountPasswordRecoveryViolationException($messages);
        }

        $user->setPassword($this->encoder->encodePassword($user, $user->getPlainPassword()));
        $user->setPasswordRecoveryToken(null);
        $user->setPasswordRecoveryAt(null);

        $this->entityManager->flush();

        return $user;
    }
}
