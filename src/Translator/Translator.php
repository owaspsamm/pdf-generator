<?php


namespace App\Translator;


use JetBrains\PhpStorm\Pure;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class Translator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    private string $defaultDomain = 'messages';
    private string $applicationDomain = 'application';
    private string $adminDomain = 'admin';

    /**
     * Translator constructor.
     * @param BaseTranslator $translator
     */
    public function __construct(private BaseTranslator $translator)
    {
    }

    /**
     * @return string
     */
    public function getDefaultDomain(): string
    {
        return $this->defaultDomain;
    }

    /**
     * @param string $defaultDomain
     * @return $this
     */
    public function setDefaultDomain(string $defaultDomain): Translator
    {
        $this->defaultDomain = $defaultDomain;

        return $this;
    }

    /**
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        if ($domain === null) {
            $domain = $this->defaultDomain;
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($domain === $this->applicationDomain) {
            $domain = $this->getMatchingDomain($id, $domain, $locale, $this->applicationDomain);
        } else {
            $domain = $this->getMatchingDomain($id, $domain, $locale, $this->adminDomain);
        }

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    /**
     * @param string $id
     * @param string $domain
     * @param string $locale
     * @param string $moduleDomain
     * @return string
     */
    private function getMatchingDomain(string $id, string $domain, string $locale, string $moduleDomain): string
    {
        $catalogue = $this->getCatalogue($locale);
        if ($catalogue->getFallbackCatalogue() != null && $catalogue->getFallbackCatalogue()->defines($id, $moduleDomain)) {
            return $moduleDomain;
        } elseif ($domain != $this->defaultDomain) {
            return $domain;
        } else {
            return $this->defaultDomain;
        }
    }

    /**
     * @param string|null $locale
     * @return MessageCatalogueInterface
     */
    public function getCatalogue(string $locale = null): MessageCatalogueInterface
    {
        return $this->translator->getCatalogue($locale);
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale)
    {
        $this->translator->setLocale($locale);
    }

    /**
     * @return array
     */
    #[Pure] public function getCatalogues(): array
    {
        return $this->translator->getCatalogues();
    }
}