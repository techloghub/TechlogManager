<?php

namespace Manager\UserBundle;

use Manager\UserBundle\DependencyInjection\Security\Factory\ManagerUserFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ManagerUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ManagerUserFactory());
    }
}
