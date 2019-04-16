<?php
namespace App\Tests\Security;

use App\Security\TokenGenerator;
// use PHPUnit\Framework\TestCase;
use Symfony\Bundle\WebProfilerBundle\Tests\TestCase;

class TokenGeneratorTest extends TestCase
{
    public function testTokenGenerator()
    {
        $tokenGen = new TokenGenerator();
        $token = $tokenGen->getRandomSecureToken(30);
        // $token[15] = '*';
        // echo $token;

        $this->assertEquals(30, strlen($token));
        // $this->assertEquals(1, preg_match("/[A-Za-z0-9]/", $token));
        $this->assertTrue(ctype_alnum($token), 'This test contains incorrect character');
    }
}
