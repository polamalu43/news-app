<?php

namespace Core;

abstract class Model
{
    protected array $attributes = [];

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function __unset(string $key): void
    {
        unset($this->attributes[$key]);
    }

    public static function fromArray(array $data): static
    {
        $model = new static();
        $model->attributes = $data;
        return $model;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
