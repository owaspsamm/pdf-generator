<?php
declare(strict_types=1);


namespace App\EventSubscriber;


use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{

    /**
     * ErrorSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param UrlGeneratorInterface $urlGenerator
     * @param Security $security
     * @param TranslatorInterface $translator
     * @param RequestStack $requestStack
     */
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UrlGeneratorInterface $urlGenerator,
        private Security $security,
        private TranslatorInterface $translator,
        private RequestStack $requestStack
    ) {
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => [['onRequest']]];
    }

    public function onRequest(ExceptionEvent $event)
    {
        if (!$event->isMainRequest() || $event->getRequest()->isXmlHttpRequest()) {
            return;
        }
        $token = $this->tokenStorage->getToken();
        if (!$token || ($token->getUser() === null)) {
            return;
        }
        if ($event->getThrowable() instanceof HttpException && $event->getThrowable()->getStatusCode() == Response::HTTP_FORBIDDEN) {
            $this->handle403($event);
        }
    }

    /**
     * @param ExceptionEvent $event
     */
    private function handle403(ExceptionEvent $event)
    {
        $request = $event->getRequest();
        if (str_starts_with($request->attributes->get("_route"), "app_") && $this->security->isGranted('ROLE_ADMIN')) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('warning', $this->translator->trans('admin.general.imitate_error'));
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('admin_index_index')));
        }
        if (str_starts_with($request->attributes->get("_route"), "admin_") && $this->security->isGranted('ROLE_USER')) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_index')));
        }

        if ($request->attributes->get("_route") == '2fa_login' || $request->attributes->get("_route") == '2fa_login_check') {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('admin_index_index')));
        }
        if ($request->attributes->get("_route") == '2fa_front_login' || $request->attributes->get("_route") == '2fa_front_login_check') {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_index')));
        }
    }
}