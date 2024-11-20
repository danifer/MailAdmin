<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\HttpBasicAuthenticator;

class BasicAuthenticator extends HttpBasicAuthenticator
{
}