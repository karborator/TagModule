<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikolayyotsov
 * Date: 2/11/17
 * Time: 11:41 PM
 */

namespace Tag\Service;


use Tag\V1\Rest\Tag\Tag;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TagServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(EntityManager::class)->getRepository(Tag::class);

        $conf       = $container->get('Config');
        $router     = $container->get('router');

        return new TagService($repository, $conf, $router);
    }

}
