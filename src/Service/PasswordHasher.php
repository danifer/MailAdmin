<?php

namespace App\Service;

class PasswordHasher
{
    public function hashPassword(string $password, string $salt = null): string
    {
        // Generate a random value and hash it using SHA-1
        if (null === $salt) {
            $salt = uniqid(mt_rand(), true);
        }

        // Construct the salt with the SHA-512 prefix ($6$)
        $salt = '$6$' . substr(sha1($salt), -16);

        // Use crypt to encrypt the password with the generated salt
        return crypt($password, $salt);
    }
}
