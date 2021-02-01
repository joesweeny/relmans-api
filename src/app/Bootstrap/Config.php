<?php

namespace Relmans\Bootstrap;

class Config
{
    private array $items;

    public function __construct(array $items = [])
    {
        $this->items = $this->getArrayItems($items);
    }

    /**
     * Checks if the given key exists in the provided array.
     *
     * @param  array      $array Array to validate
     * @param  int|string $key   The key to look for
     *
     * @return bool
     */
    private function exists($array, $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Return the value of a given key
     *
     * @param  int|string|null $key
     * @param  mixed           $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->items;
        }

        if ($this->exists($this->items, $key)) {
            return $this->items[$key];
        }

        if (strpos($key, '.') === false) {
            return $default;
        }

        $items = $this->items;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($items) || !$this->exists($items, $segment)) {
                return $default;
            }

            $items = &$items[$segment];
        }

        return $items;
    }

    /**
     * Return the given items as an array
     *
     * @param  mixed $items
     * @return array
     */
    private function getArrayItems($items): array
    {
        if (is_array($items)) {
            return $items;
        }

        if ($items instanceof self) {
            return $items->items;
        }

        return (array) $items;
    }

    /**
     * Check if a given key or keys exists
     *
     * @param  array|int|string $keys
     * @return bool
     */
    public function has($keys): bool
    {
        $keys = (array) $keys;

        if (!$this->items || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $items = $this->items;

            if ($this->exists($items, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (!is_array($items) || !$this->exists($items, $segment)) {
                    return false;
                }

                $items = $items[$segment];
            }
        }

        return true;
    }

    /**
     * Set a given key / value pair or pairs
     *
     * @param array|int|string $keys
     * @param mixed            $value
     */
    public function set($keys, $value = null): void
    {
        if (is_array($keys)) {
            foreach ($keys as $k => $v) {
                $this->set($k, $v);
            }

            return;
        }

        $items = &$this->items;

        foreach (explode('.', $keys) as $key) {
            if (!isset($items[$key]) || !is_array($items[$key])) {
                $items[$key] = [];
            }

            $items = &$items[$key];
        }

        $items = $value;
    }
}
