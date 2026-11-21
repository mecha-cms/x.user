<?php

From::_('user', static function ($name) {
    if (!is_string($name)) {
        return null;
    }
    static $cache = [];
    if (isset($cache[$name])) {
        return $cache[$name];
    }
    $folder = LOT . D . 'user' . D . '{#,}' . $name;
    // Get from external `id` data
    if ($file = exist($folder . D . '+' . D . 'id.{' . ($x = x\page\x()) . '}', 1)) {
        if (0 === filesize($file)) {
            return null;
        }
        $id = trim((string) fgets(fopen($file, 'r')));
        return ($cache[$name] = is_numeric($id) ? (int) $id : $id);
    }
    if ($file = exist($folder . '.{' . $x . '}', 1)) {
        // Get from internal `id` data
        if (null !== ($id = From::page(file_get_contents($file))->id ?? null)) {
            return ($cache[$name] = $id);
        }
    }
    return null;
});