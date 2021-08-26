<?php

namespace x\user {
    function hook($id, array $lot = [], $join = "") {
        $tasks = \Hook::fire($id, $lot);
        \array_shift($lot); // Remove the raw task(s)
        return \implode($join, \x\user\tasks($tasks, $lot));
    }
    function tasks(array $in, array $lot = []) {
        $out = [];
        foreach ($in as $k => $v) {
            if (null === $v || false === $v) {
                continue;
            }
            if (\is_array($v)) {
                $out[$k] = new \HTML(\array_replace([false, "", []], $v));
            } else if (\is_callable($v)) {
                $out[$k] = \fire($v, $lot);
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}