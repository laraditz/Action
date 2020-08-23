<?php

namespace Laraditz\Action\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use ReflectionMethod;
use ReflectionParameter;

trait Resolvable
{
    protected $validator;

    protected function resolveConstructorAttributes(...$arguments)
    {
        return $this->fill(Arr::get($arguments, 0, []));
    }

    protected function resolveRules()
    {
        if (method_exists($this, 'rules')) {
            $this->validator = Validator::make($this->all(), $this->rules());
            $this->validator->validate();
        }
    }

    protected function resolveMethod($instance, $method, $extras = [])
    {
        $parameters = $this->resolveMethodDependencies($instance, $method, $extras);

        return $instance->{$method}(...$parameters);
    }

    protected function resolveMethodDependencies($instance, $method, $extras = []): array
    {
        if (!method_exists($instance, $method)) {
            return [];
        }

        $reflector = new ReflectionMethod($instance, $method);

        $handler = function ($parameter) use ($extras) {
            return $this->resolveMethodDependency($parameter, $extras);
        };

        return array_map($handler, $reflector->getParameters());
    }

    protected function resolveMethodDependency(ReflectionParameter $parameter, $extras = [])
    {
        [$key, $value] = $this->findAttributeFromParameter($parameter->name, $extras);

        $class = $parameter->getClass();

        if ($key && (!$class || $value instanceof $class->name)) {
            return $value;
        }

        if ($class && !$parameter->allowsNull()) {
            return $this->resolveContainerDependency($class->name, $key);
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
    }

    protected function findAttributeFromParameter($name, $extras = []): array
    {
        $attributes = array_merge($this->attributes, $extras);

        if (array_key_exists($name, $attributes)) {
            return [$name, $attributes[$name]];
        }

        if (array_key_exists($snakedName = Str::snake($name), $attributes)) {
            return [$snakedName, $attributes[$snakedName]];
        }

        return [null, null];
    }

    protected function resolveContainerDependency($class, $key)
    {
        $instance = app($class);

        if ($key) {
            $this->updateAttributeWithResolvedInstance($key, $instance);
        }

        return $instance;
    }

    protected function updateAttributeWithResolvedInstance($key, $instance): void
    {
        if ($this->request->has($key)) {
            return;
        }

        $this->attributes[$key] = $instance;
    }
}
