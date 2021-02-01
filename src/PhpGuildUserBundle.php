<?php

declare(strict_types=1);

namespace PhpGuild\UserBundle;

use PhpGuild\UserBundle\DependencyInjection\PhpGuildUserExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class PhpGuildUserBundle
 */
class PhpGuildUserBundle extends Bundle
{
    /**
     * getContainerExtension
     *
     * @return ExtensionInterface
     */
    public function getContainerExtension(): ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new PhpGuildUserExtension();
        }

        return $this->extension;
    }
}
