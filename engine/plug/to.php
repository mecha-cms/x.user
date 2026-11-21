<?php

To::_('user', static function ($id) {
    if (!is_int($id)) {
        return null;
    }
    static $cache = [];
    if (isset($cache[$id])) {
        return $cache[$id];
    }
    $folder = LOT . D . 'user' . D . '*';
    // Search for external `id` data
    foreach (glob($folder . D . '+' . D . 'id.{' . ($x = x\page\x()) . '}', GLOB_BRACE | GLOB_NOSORT) as $v) {
        if (is_file($v) && $id === (int) trim((string) fgets(fopen($v, 'r')))) {
            $name = basename(dirname($v, 2));
            if ('~' === ($name[0] ?? 0)) {
                return null;
            }
            return ($cache[$id] = '#' === ($name[0] ?? 0) ? substr($name, 1) : $name);
        }
    }
    // Search for internal `id` data
    foreach (glob($folder . '.{' . $x . '}', GLOB_BRACE | GLOB_NOSORT) as $v) {
        if (is_file($v) && $id === (From::page(file_get_contents($v))->id ?? null)) {
            $name = pathinfo($v, PATHINFO_FILENAME);
            if ('~' === ($name[0] ?? 0)) {
                return null;
            }
            return ($cache[$id] = '#' === ($name[0] ?? 0) ? substr($name, 1) : $name);
        }
    }
    return null;
});