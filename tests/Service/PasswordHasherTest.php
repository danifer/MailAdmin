<?php

namespace App\Tests\Service;

use App\Service\PasswordHasher;
use PHPUnit\Framework\TestCase;

class PasswordHasherTest extends TestCase
{
    public function testPasswordHasher(): void
    {
        $passwordHasher = new PasswordHasher();
        $hashedPassword = $passwordHasher->hashPassword(
            'password',
            '0.4432873646200517'
        );

        $this->assertSame(
            '$6$f80eb43d902a58c6$mMSn7vKXphghX2buuak3OO6P0DmnnuWkK8Rq7f6vzkCfoXWSnt7bl1VPSeo0YTn9o5oHHqGfGlUpVRJP5qf9D1',
            $hashedPassword
        );
    }
}
