<?php

function fn_replace_author_html_text($__html, $__lot) {
    if (!isset($__lot[2]['name']) || $__lot[2]['name'] !== 'author') {
        return $__html;
    }
    $__authors = [];
    $__select = $__lot[2]['value'];
    foreach (g(ENGINE . DS . 'log' . DS . 'user', 'page') as $__v) {
        $__v = new User(Path::N($__v));
        $__k = User::ID . $__v->key;
        $__authors[($__v->status !== 1 ? '.' : "") . $__k] = $__v->author;
        if ($__select === $__v->author) {
            $__select = $__k;
        }
    }
    return Form::select('author', $__authors, $__select, [
        'classes' => ['select', 'block'],
        'id' => 'f-author'
    ]);
}

Hook::set('h-t-m-l.unit.input', 'fn_replace_author_html_text');