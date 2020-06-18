<?php

namespace Laraditz\Action\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

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
}
