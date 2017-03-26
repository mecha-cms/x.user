<?php

class User extends Genome {

    const ID = '@';

    public $page = null;
    public $key = "";

    public function __construct($id, $lot = [], $NS = 'user') {
        $this->key = $id;
        global $url;
        $folder = ENGINE . DS . 'log' . DS . 'user';
        if ($path = File::exist($folder . DS . $id . '.page')) {
            if (!array_key_exists('key', $lot)) {
                $lot['key'] = $id;
            }
            $page = new Page($path, $lot, $NS);
            $s = Path::F($path, $folder);
            $page->url = $url . '/' . Extend::state(Path::D(__DIR__, 2), 'path', 'user') . '/' . ($s ? '/' . $s : "");
            $this->page = $page;
        }
        self::$__instance__[] = $this;
    }

    public function __call($key, $lot) {
        $fail = array_shift($lot);
        $fail_alt = array_shift($lot);
        $x = $this->__get($key);
        if (is_string($fail) && strpos($fail, '~') === 0) {
            return call_user_func(substr($fail, 1), $x !== null ? $x : $fail_alt);
        } else if ($fail instanceof \Closure) {
            return call_user_func($fail, $x !== null ? $x : $fail_alt);
        }
        return $x !== null ? $x : $fail;
    }

    public function __set($key, $value = null) {
        return $this->page->{$key} = $value;
    }

    public function __get($key) {
        return $this->page->{$key};
    }

    public function __toString() {
        $page = $this->page;
        $key = $this->key;
        return $page->author ? $page->author : self::ID . ($page->key ?: $key);
    }

}