<?php

class Users extends Pages {

    public function page(string $path) {
        return new User($path);
    }

}