<?php

namespace {
    $key = \cookie('user.key');
    $a = \cookie('user.token');
    $b = \content(\LOT . \D . 'user' . \D . $key . \D . 'token.data');
    $user = $a && $b && $a === $b ? '@' . $key : false;
    \Is::_('user', function($key = null) use($user) {
        if (\is_string($key)) {
            $key = \ltrim($key, '@');
            return $user && '@' . $key === $user ? $user : false;
        }
        if (\is_int($key) && false !== $user) {
            $user = \ltrim($user, '@');
            $user = new \User(\LOT . \D . 'user' . \D . $user . '.page');
            return $user->exist && $key === $user->status;
        }
        return false !== $user ? $user : false;
    });
    \State::set('is.enter', $user = \Is::user());
    $folder = \LOT . \D . 'user';
    $GLOBALS['user'] = $user = \User::from($user ? $folder . \D . \substr($user, 1) . '.page' : null);
    $GLOBALS['users'] = $users = \Users::from($folder);
    if (!\is_file(\LOT . \D . 'layout' . \D . 'user.php')) {
        \Layout::set('user', __DIR__ . \D . '..' . \D . 'lot' . \D . 'layout' . \D . 'user.php');
    }
    if (!\is_file(\LOT . \D . 'layout' . \D . 'form' . \D . 'user.php')) {
        \Layout::set('form/user', __DIR__ . \D . '..' . \D . 'lot' . \D . 'layout' . \D . 'form' . \D . 'user.php');
    }
}

namespace x\user {
    function hook($id, array $lot = [], $join = "") {
        $tasks = \Hook::fire($id, $lot);
        \array_shift($lot); // Remove the raw task(s)
        return \implode($join, \x\user\tasks($tasks, $lot));
    }
    function tasks(array $in, array $lot = []) {
        $out = [];
        foreach ($in as $k => $v) {
            if (false === $v || null === $v) {
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

namespace x\user\hook {
    require __DIR__ . \D . 'engine' . \D . 'fire.php';
    function author($author) {
        if ($author && \is_string($author) && 0 === \strpos($author, '@')) {
            return new \User(\LOT . \D . 'user' . \D . \substr($author, 1) . '.page');
        }
        return $author;
    }
    function avatar($avatar, array $lot = []) {
        if ($avatar) {
            return $avatar;
        }
        $w = $lot[0] ?? 72;
        $h = $lot[1] ?? $w;
        \extract($GLOBALS, \EXTR_SKIP);
        return \sprintf($state->x->user->avatar ?? "", \md5($this['email']), $w, $h);
    }
    function content($content) {
        if ($content && \is_string($content) && false !== \strpos($content, '@')) {
            $out = "";
            $parts = \preg_split('/(<pre(?:\s[^>]*)?>[\s\S]*?<\/pre>|<code(?:\s[^>]*)?>[\s\S]*?<\/code>|<kbd(?:\s[^>]*)?>[\s\S]*?<\/kbd>|<script(?:\s[^>]*)?>[\s\S]*?<\/script>|<style(?:\s[^>]*)?>[\s\S]*?<\/style>|<textarea(?:\s[^>]*)?>[\s\S]*?<\/textarea>|<[^>]+>)/i', $content, null, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
            foreach ($parts as $v) {
                if (0 === \strpos($v, '<') && '>' === \substr($v, -1)) {
                    $out .= $v; // Is a HTML tag
                } else {
                    $out .= false !== \strpos($v, '@') ? \preg_replace_callback('/@[a-z\d-]+/', static function($m) {
                        if (\is_file($file = \LOT . \D . 'user' . \D . \substr($m[0], 1) . '.page')) {
                            $user = new \User($file);
                            return '<a href="' . $user->url . '" target="_blank" title="' . $user->user . '">' . $user . '</a>';
                        }
                        return $m[0];
                    }, $v) : $v; // Is a plain text
                }
            }
            return $out;
        }
        return $content;
    }
    \Hook::set('page.author', __NAMESPACE__ . "\\author", 2);
    \Hook::set('user.avatar', __NAMESPACE__ . "\\avatar", 0);
    \Hook::set([
        'page.content',
        'page.description',
        'page.title'
    ], __NAMESPACE__ . "\\content", 2);
}