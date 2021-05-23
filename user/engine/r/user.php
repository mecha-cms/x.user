<?php

function user(...$v) {
    return new User(...$v);
}

function users(...$v) {
    return Users::from(...$v);
}
