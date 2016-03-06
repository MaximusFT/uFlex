<?php
/**
 * Created by PhpStorm.
 * User: Pablo
 * Date: 3/5/2016
 * Time: 10:41 PM
 */

namespace ptejada\uFlex\Classes;


use ptejada\uFlex\AbstractSingleton;

class Registry extends AbstractSingleton
{
    const SERVICE_LOG     = 'log';
    const SERVICE_COOKIE  = 'cookie';
    const SERVICE_SESSION = 'session';
    const SERVICE_AUTH    = 'auth';

    /** @var Collection */
    protected $book;

    public function __construct(){
        $this->book = new Collection();

        // Register default services
        $this->registerService(self::SERVICE_LOG, 'ptejada\uFlex\Log');
        $this->registerService(self::SERVICE_COOKIE, 'ptejada\uFlex\Service\Cookie');
        $this->registerService(self::SERVICE_SESSION, 'ptejada\uFlex\Service\Session');
        $this->registerService(self::SERVICE_AUTH, 'ptejada\uFlex\Service\Authenticator');
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    protected function getClassPath($name)
    {
        return "class.{$name}";
    }

    protected function getInstancePath($name)
    {
        return "instance.{$name}";
    }

    /**
     * Register a new service, a class to only keep an instance for
     *
     * @param string $serviceName The name to register the service with
     * @param callable $serviceClass A callable constructor
     *
     * @throws \Exception If the service class does not exists
     */
    public function registerService($serviceName, $serviceClass)
    {
        if (class_exists($serviceClass)) {
            throw new \Exception("Can not register service '{$serviceName}', class does not exist: {$serviceClass}");
        }

        $classPath = $this->getClassPath($serviceName);
        // If a class has already been registered then it must inherit the known parent
        $expectedParent = $this->book->get($classPath);

        if ($expectedParent && ! is_subclass_of($classPath, $expectedParent)) {
            throw new \Exception("Class '{$classPath}' is expected to inherit from '{$expectedParent}'.");
        }

        // Registers the service constructor
        $this->book->set($classPath, $serviceClass);

        // Delete any existing instance
        $this->book->delete($this->getInstancePath($serviceName));
    }

    /**
     * Get an existing service
     *
     * @param string $serviceName The name of the service
     *
     * @return mixed
     */
    public function service($serviceName)
    {
        $instance = $this->book->get($this->getInstancePath($serviceName));

        if (!is_object($instance)) {
            $constructor = $this->book->get($this->getClassPath($serviceName));
            $instance = new $constructor;
        }

        return $instance;
    }
}
