<?php

function fn_replace_author_html_text($html, $lot) {
    if (!isset($lot[2]['name']) || $lot[2]['name'] !== 'author') {
        return $html;
    }
    $__authors = [];
    $__select = $lot[2]['value'];
    foreach (g(ENGINE . DS . 'log' . DS . 'user', 'page') as $v) {
        $v = new User(Path::N($v));
        $k = User::ID . $v->key;
        $__authors[($v->status !== 1 ? '.' : "") . $k] = $v->author;
        if ($__select === $v->author) {
            $__select = $k;
        }
    }
    return Form::select('author', $__authors, $__select, [
        'classes' => ['select', 'block'],
        'id' => 'f-author'
    ]);
}

Hook::set('h-t-m-l.unit.input', 'fn_replace_author_html_text');