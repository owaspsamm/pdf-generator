<?php
declare(strict_types=1);

namespace App\Security\Application;

use _PHPStan_ecc307676\Nette\Neon\Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function __construct(
    ) {
    }


    public function supports(Request $request): bool
    {
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        throw new Exception();
    }

    /**
     * @param Passport $passport
     * @param string $firewallName
     * @return TokenInterface
     */
    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        throw new Exception();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        throw new Exception();
    }

    protected function getLoginUrl(Request $request): string
    {
        throw new Exception();
    }

    /**
     * @param Request $request
     * @return FormInterface
     */
    public function getCredentials(Request $request): FormInterface
    {
        throw new Exception();
    }
}
