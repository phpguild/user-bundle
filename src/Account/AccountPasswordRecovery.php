<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle\Account;

use Doctrine\ORM\EntityManagerInterface;
use PhpGuild\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
    
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /**
     * AccountPasswordRecovery constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param TranslatorInterface          $translator
     * @param ValidatorInterface           $validator
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder
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

        if (
            $user->getPasswordRecoveryAt() instanceof \DateTime
            && $user->getPasswordRecoveryAt()->format('Y-m-d H:i:s') > (new \DateTime())->modify('-24 hours')->format('Y-m-d H:i:s')
        ) {
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

        if (!$user || !$user->isActive()) {
            throw new AccountPasswordRecoveryException(
                $this->translator->trans('user_account_is_disable')
            );
        }

        if (!$user->getPasswordRecoveryAt() instanceof \DateTime
            || $user->getPasswordRecoveryAt()->format('Y-m-d H:i:s') < (new \DateTime())->modify('-24 hours')->format('Y-m-d H:i:s')
        ) {
            throw new AccountPasswordRecoveryException(
                $this->translator->trans('user_account_confirmation_token_expired')
            );
        }

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
        $this->entityManager->flush();

        return $user;
    }
}
