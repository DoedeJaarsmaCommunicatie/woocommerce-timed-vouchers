<?php

namespace WooCommerceTimedVouchers\DBO;

interface DBO
{
    public function findOn($column, $value);

    public function find($id);

    public function findMany(...$ids): array;

    public function findManyOn($column, $operator, $value = null);

    public function hasOn($column, $value);

    public function hasMapAny(array $map);

    public function hasMapAll(array $map);

    public function has($value);

    /**
     * @return self[]
     */
    public function all(): array;

    public function create();

    public function store(array $data);
}
