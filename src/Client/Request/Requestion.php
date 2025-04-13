<?php

namespace Hascamp\Client\Request;

use Closure;
use Hascamp\Client\Contracts\DataModel;
use Hascamp\Client\Contracts\Modelable;
use Hascamp\Direction\Contracts\Accessible;

final class Requestion
{
    /** @var \Hascamp\Direction\Contracts\Accessible */
    protected $accessible;

    /** @var string */
    private static $__rootModel;

    /** @var string */
    private $model;

    /** @var string */
    private $method;

    /** @var string */
    private $modelRequest = "";

    public function __construct(
        array $config = []
    )
    {
        $this->setConfig($config);
    }

    public function setConfig(array $config): void
    {
        static::$__rootModel = $config['resource_namespace'] ?? "";
    }

    public function setAccessible(Accessible $accessible): void
    {
        $this->accessible = $accessible;
    }

    public function asHeaders(): Closure
    {
        return function () {
            return [
                'User-Agent' => $this->accessible->app()->userAgent(),
                'X-App-ID' => $this->accessible->app()->id(),
                'X-Request-ID' => $this->accessible->getFactory()->requestId(),
                'X-Trace-ID' => $this->accessible->getFactory()->traceId(),
                // 'Authorization' => "Bearer 7e4e544f-1575-440e-a7fd-2655f715b0e2",
            ];
        };
    }

    public function model(): ?string
    {
        return $this->model;
    }

    public function method(): ?string
    {
        return $this->method;
    }

    protected function filterToCase(string $str, string $separator = ""): string
    {
        $value = str_replace(['-', '_'], ' ', $str);
        $value = ucwords($value);
        return str_replace(' ', $separator, $value);
    }

    public function getMethodable(string $name): string
    {
        $value = $this->filterToCase($name);
        return lcfirst($value);
    }

    public function getModelable(string $name): string
    {
        $modelable = "";
        if (str_contains($name, '.')) {
            foreach (explode(".", $name) as $value) {
                $modelable .= '\\' . $this->filterToCase($value);
            }
        }
        else {
            $modelable = $this->filterToCase($name);
        }

        $modelable = static::$__rootModel . $modelable;
        
        if (empty($modelable) || ! class_exists($modelable)) {
            throw new \Exception("Invalid Event: \"{$modelable}\" modelable or class name not found.");
        }

        return $modelable;
    }

    private function event_Convertion(string $event): ?array
    {
        $explode = explode(':', strtolower($event));

        if (count($explode) >= 2) {

            return [
                'className' => $this->getModelable($explode[0]),
                'methodName' => $this->getMethodable($explode[1]),
            ];

        }

        return null;
    }

    public function process(string $event): static
    {
        $_convert = $this->event_Convertion($event);

        if ($_convert === null || empty($_convert)) {
            throw new \Exception("Error Processing Event: invalid \"{$event}\".");
        }

        $model = $_convert['className'];
        $method = $_convert['methodName'];

        if (empty($event) || ! class_exists($model) || ! method_exists($model, $method)) {
            throw new \Exception("Error Processing Request: invalid {$event} or {$model} not found.");
        }

        $this->modelRequest = $event;
        $this->model = $model;
        $this->method = $method;
        return $this;
    }

    public function request(array $data): Modelable
    {
        try {

            $instance = new $this->model();
            if (! $instance instanceof DataModel) {
                throw new \Exception("Error Processing Request: {$this->model()} invalid model.");
            }

            $headers = function (Closure|array $headers) {
                if ($headers instanceof Closure) {
                    $headers = $headers();
                }
                return $headers;
            };

            $instance->requestion($this->modelRequest, $data, $headers($this->asHeaders()));
            return $instance->{$this->method()}();
            
        } finally {
            $this->__reset();
        }
    }

    protected function __reset(): void
    {
        $this->model = null;
        $this->method = null;
    }
}