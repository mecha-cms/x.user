<?php

class User extends Genome {

    const ID = '@';

    public $page = null;
    public $id = "";

    public function __construct($id, $lot = [], $NS = 'user') {
        $this->id = $id;
        extract(Lot::get(null, []));
        $folder = ENGINE . DS . 'log' . DS . 'user';
        $state = Extend::state(Path::D(__DIR__, 2));
        if ($path = File::exist($folder . DS . $this->id . '.page')) {
            $page = new Page($path, $lot, $NS);
            $s = To::url(str_replace([$folder . DS, $folder], "", Path::D($path)));
            $page->url = $url . '/' . $state['slug'] . '/' . ($s ? '/' . $s : "") . $this->id;
            $this->page = $page;
        }
    }

    public function __call($key, $lot) {
        $fail = array_shift($lot) ?: false;
        $fail_alt = array_shift($lot) ?: false;
        if (is_string($fail) && strpos($fail, '~') === 0) {
            return call_user_func(substr($fail, 1), array_key_exists($key, $this->lot) ? o($this->lot[$key]) : $fail_alt);
        } else if ($fail instanceof \Closure) {
            return call_user_func($fail, array_key_exists($key, $this->lot) ? o($this->lot[$key]) : $fail_alt);
        }
        return array_key_exists($key, $this->lot) ? o($this->lot[$key]) : $fail;
    }

    public function __set($key, $value = null) {
        return $this->page->{$key} = $value;
    }

    public function __get($key) {
        return $this->page->{$key};
    }

    public function __toString() {
        $page = $this->page;
        $id = $this->id;
        return $page->author ? $page->author : self::ID . ($page->id ?: $id);
    }

}