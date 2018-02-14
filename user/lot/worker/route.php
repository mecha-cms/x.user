<?php

Route::lot('%*%', function($path) use($site) {
    $s = explode('/', $path);
    if (count($s) === 2 && $s[0] === Extend::state('user', 'path')) {
        if (!$f = File::exist([
            USER . DS . $s[1] . '.page',
            USER . DS . $s[1] . '.archive'
        ])) {
            Shield::abort();
        }
        $page = new Page($f);
        if ($t = $page->{'$'}) {
            $page->title = $t;
        }
        Config::set('page.title', new Anemon([$t . ' (@' . $s[1] . ')', $site->title], ' &#x00B7; '));
        Lot::set([
            'page' => $page,
            'pages' => [],
            'pager' => new Elevator([], 1, 0, true)
        ]);
        Shield::attach('page/' . $path);
    }
});