<?php
declare(strict_types=1);

namespace App\Controller\Application;

use App\Translator\Translator;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractController extends SymfonyAbstractController
{
    protected LoggerInterface $logger;
    protected TranslatorInterface $translator;
    protected ValidatorInterface $validator;
    protected EventDispatcherInterface $eventDispatcher;
    protected ManagerRegistry $managerRegistry;
    protected RequestStack $requestStack;

    /**
     * AbstractController constructor.
     * @param RequestStack $requestStack
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param ValidatorInterface $validator
     * @param EventDispatcherInterface $eventDispatcher
     * @param Translator $customTranslator
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(
        RequestStack $requestStack,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher,
        Translator $customTranslator,
        ManagerRegistry $managerRegistry
    ) {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
        $this->managerRegistry = $managerRegistry;
        $this->requestStack = $requestStack;
        $customTranslator->setDefaultDomain('application');
    }

    /**
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}