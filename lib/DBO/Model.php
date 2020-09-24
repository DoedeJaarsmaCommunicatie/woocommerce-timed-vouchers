<?php

namespace WooCommerceTimedVouchers\DBO;

abstract class Model implements DBO
{
    protected static $call_cache = [];

    protected $attributes = [];
    protected $casts = [];

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    public function __get($name)
    {
        return $this->attributes[$name];
    }

    protected $table_name;
    protected $key;

    /**
     * @var \wpdb
     */
    public $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @param $id
     *
     * @return false|object
     */
    public function find($id)
    {
        $row = $this->findOn($this->key, $id);

        return $row ?? false;
    }

    public function findOn($column, $value)
    {
        $value = sanitize_text_field($value);
        $row = $this->db->get_row("SELECT * FROM {$this->get_table_name()} WHERE {$column} = \"{$value}\";", ARRAY_A);

        if (null !== $row) {
            return $this->map($row);
        }

        return false;
    }

    public function map(array $data): Model
    {
        $self = new static();
        if (isset($data['id'], static::$call_cache[(new \ReflectionClass($self))->getShortName()][$data['id']])) {
            return static::$call_cache[(new \ReflectionClass($self))->getShortName()][$data['id']];
        }

        foreach ($data as $key => $value) {
            if (isset($this->casts[$key])) {
                settype($value, $this->casts[$key]);
            }

            $self->{$key} = $value;
        }

        if (property_exists($self, 'id')) {
            static::$call_cache [(new \ReflectionClass($self))->getShortName()] [$self->id]= $self;
        }

        return $self;
    }

    public function findMany(...$ids): array
    {
        return $this->findManyOn($this->key, 'in', $ids);
    }

    public function findManyOn($column, $operator, $value = null) {
        if (func_num_args() === 2) {
            $results = $this->db->get_results(
                "SELECT * FROM {$this->get_table_name()} WHERE {$column} = \"{$operator}\";",
                ARRAY_A
            );

            return array_map([$this, 'map'], $results);
        }

        if (strtolower($operator) === 'in') {
            $value = implode('","', (array) $value);

            $results = $this->db->get_results(
                "SELECT * FROM {$this->get_table_name()} WHERE {$column} IN({$value});",
                ARRAY_A
            );

            return array_map([$this, 'map'], $results);
        }

        if (strtolower($operator) === 'like') {
            $value = '%' . $value . '%';
        }

        $results = $this->db->get_results(
            "SELECT * FROM {$this->get_table_name()} WHERE {$column} ${operator} \"$value\";",
            ARRAY_A
        );

        return array_map([$this, 'map'], $results);
    }

    public function has($value): bool
    {
        return $this->hasOn($this->key, $value);
    }

    public function hasMapAny(array $map)
    {
        foreach ($map as $column => $value) {
            if ($this->hasOn($column, $value)) {
                return true;
            }
        }

        return false;
    }

    public function hasMapAll(array $map)
    {
        foreach($map as $column => $value) {
            if (!$this->hasOn($column, $value)) {
                return false;
            }
        }

        return true;
    }

    public function hasOn($column, $value): bool
    {
        return (bool) $this->findOn($column, $value);
    }

    public function all(): array
    {
        return array_map(
            [$this, 'map'],
            $this->db->get_results(
                "SELECT * FROM {$this->get_table_name()};",
                ARRAY_A
            )
        );
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public static function make(array $data = [])
    {
        $self = new static();

        foreach ($data as $key => $value) {
            $self->{$key} = $value;
        }

        return $self;
    }

    public function create(): void
    {
        $res = $this->db->insert(
            $this->get_table_name(),
            $this->attributes
        );

        if (!$res) {
            var_dump($this->db->last_error);
        }
    }

    public function store(array $data): void
    {
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }

        $this->create();
    }

    public function patch(): void
    {
        $this->db->update(
            $this->get_table_name(),
            $this->attributes,
            [(string)$this->key => $this->attributes[$this->key]]
        );
    }

    public function put(array $data): void
    {
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }

        $this->patch();
    }

    protected function get_table_name()
    {
        return $this->db->prefix . $this->table_name;
    }
}
