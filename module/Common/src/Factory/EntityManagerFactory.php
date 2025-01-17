<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Common\Factory;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\Mapping\Driver\PHPDriver;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Interop\Container\ContainerInterface;
use Shlinkio\Shlink\Common\Type\ChronosDateTimeType;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class EntityManagerFactory implements FactoryInterface
{
    /**
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when creating a service.
     * @throws ORMException
     * @throws DBALException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $globalConfig = $container->get('config');
        $isDevMode = isset($globalConfig['debug']) ? ((bool) $globalConfig['debug']) : false;
        $cache = $container->has(Cache::class) ? $container->get(Cache::class) : new ArrayCache();
        $emConfig = $globalConfig['entity_manager'] ?? [];
        $connectionConfig = $emConfig['connection'] ?? [];
        $ormConfig = $emConfig['orm'] ?? [];

        if (! Type::hasType(ChronosDateTimeType::CHRONOS_DATETIME)) {
            Type::addType(ChronosDateTimeType::CHRONOS_DATETIME, ChronosDateTimeType::class);
        }

        $config = Setup::createConfiguration($isDevMode, $ormConfig['proxies_dir'] ?? null, $cache);
        $config->setMetadataDriverImpl(new PHPDriver($ormConfig['entities_mappings'] ?? []));

        return EntityManager::create($connectionConfig, $config);
    }
}
