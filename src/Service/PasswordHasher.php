<?php

namespace App\Service;

class PasswordHasher
{
    public function hashPassword(string $password): string
    {
        $salt = substr(sha1(random_bytes(32)), 0, 16);
        return crypt($password, '$6$' . $salt);
    }
}
