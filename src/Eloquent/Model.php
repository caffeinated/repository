<?php
namespace Caffeinated\Repository\Eloquent;

use Illuminate\Config\Repository as Config;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Create a new Model instance.
     *
     * @param Config  $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $className = class_basename($this);
        $config    = implode('.', ['relationship', $className, $method]);

        if (Config::has($config)) {
            $function = Config::get($config);

            return $function($this);
        }

        return parent::__call($method, $parameters);
    }
}