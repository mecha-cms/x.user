<?php namespace _\lot\x\user;

function a($a) {
    if ($a && \is_string($a) && \strpos($a, '@') !== false) {
        $out = "";
        $parts = \preg_split('/(<pre(?:\s[^>]*)?>[\s\S]*?<\/pre>|<code(?:\s[^>]*)?>[\s\S]*?<\/code>|<kbd(?:\s[^>]*)?>[\s\S]*?<\/kbd>|<script(?:\s[^>]*)?>[\s\S]*?<\/script>|<style(?:\s[^>]*)?>[\s\S]*?<\/style>|<textarea(?:\s[^>]*)?>[\s\S]*?<\/textarea>|<[^>]+>)/i', $a, null, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
        foreach ($parts as $v) {
            if (\strpos($v, '<') === 0 && \substr($v, -1) === '>') {
                $out .= $v; // Is a HTML tag
            } else {
                $out .= \strpos($v, '@') !== false ? \preg_replace_callback('/@[a-z\d-]+/', function($m) {
                    if (\is_file($f = \USER . \DS . \substr($m[0], 1) . '.page')) {
                        $f = new \User($f);
                        return '<a href="' . $f->url . '" target="_blank" title="' . $f->user . '">' . $f . '</a>';
                    }
                    return $m[0];
                }, $v) : $v; // Is a plain text
            }
        }
        return $out;
    }
    return $a;
}

function author($author = "") {
    if ($author && \is_string($author) && \strpos($author, '@') === 0) {
        return new \User(\USER . \DS . \substr($author, 1) . '.page');
    }
    return $author;
}

function avatar($avatar, array $lot = []) {
    if ($avatar) {
        return $avatar;
    }
    $w = $lot[0] ?? 72;
    $h = $lot[1] ?? $w;
    $d = $lot[2] ?? 'monsterid';
    return $GLOBALS['url']->protocol . 'www.gravatar.com/avatar/' . \md5($this['email']) . '?s=' . $w . '&d=' . $d;
}

\Hook::set([
    'page.content',
    'page.description',
    'page.excerpt', // `.\lot\x\excerpt`
    'page.title'
], __NAMESPACE__ . "\\a", 2);

\Hook::set('page.author', __NAMESPACE__ . "\\author", 2);
\Hook::set('user.avatar', __NAMESPACE__ . "\\avatar", 0);