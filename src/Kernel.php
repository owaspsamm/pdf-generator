<?php
declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Yaml\Yaml;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $container->import('../config/{services}.yaml');
            $container->import('../config/{services}_'.$this->environment.'.yaml');
        } else {
            $container->import('../config/{services}.php');
        }

        $parameters = $container->parameters();
        $defaultConfigPath = \dirname(__DIR__).'/config/instances/default.yaml';
        if (isset($_SERVER['APP_INSTANCE']) && file_exists(\dirname(__DIR__).'/config/instances/'.$_SERVER['APP_INSTANCE'].'.yaml')) {
            $defaultConfig = Yaml::parseFile($defaultConfigPath);
            $config = Yaml::parseFile(\dirname(__DIR__).'/config/instances/'.$_SERVER['APP_INSTANCE'].'.yaml');
            $configs = array_replace_recursive($defaultConfig, $config);

            foreach ($configs['parameters'] as $key => $value) {
                $parameters->set($key, $value);
            }


            foreach ($configs as $namespace => $values) {
                if (in_array($namespace, ['imports', 'parameters', 'services'])) {
                    continue;
                }

                if (!\is_array($values) && null !== $values) {
                    $values = [];
                }
                $container->extension($namespace, $values);
            }
        } else {
            $container->import($defaultConfigPath);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/routes.yaml')) {
            $routes->import('../config/{routes}.yaml');
        } else {
            $routes->import('../config/{routes}.php');
        }
    }
}
