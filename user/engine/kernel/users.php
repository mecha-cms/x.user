<?php

class Users extends Anemon {

    public function getIterator() {
        $users = [];
        foreach ($this->value as $v) {
            $users[] = new User($v);
        }
        return new \ArrayIterator($users);
    }

    public function sort($sort = 1, $preserve_key = false) {
        if (is_array($sort)) {
            $value = [];
            foreach ($this->value as $v) {
                $value[$v] = (new User($v))[$sort[1]];
            }
            $sort[0] === -1 ? arsort($value) : asort($value);
            $this->value = array_keys($value);
        } else {
            $value = $this->value;
            if ($preserve_key) {
                $sort === -1 ? arsort($value) : asort($value);
            } else {
                $sort === -1 ? rsort($value) : sort($value);
            }
            $this->value = $value;
        }
        return $this;
    }

    public function take(string $key, $or = null) {
        $value = [];
        foreach ($this->value as $v) {
            $value[] = (new User($v))[$key] ?? $or;
        }
        return $value;
    }

}