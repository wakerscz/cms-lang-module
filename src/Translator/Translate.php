<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Translator;


use Propel\Runtime\Collection\ObjectCollection;
use Wakers\LangModule\Database\LangSystem;
use Wakers\LangModule\Manager\LangSystemManager;
use Wakers\LangModule\Repository\LangRepository;
use Wakers\LangModule\Repository\LangSystemRepository;


class Translate
{
    /**
     * @var LangRepository
     */
    protected $langRepository;


    /**
     * @var LangSystemRepository
     */
    protected $langSystemRepository;


    /**
     * @var LangSystemManager
     */
    protected $langSystemManager;


    /**
     * @var ObjectCollection|LangSystem[]
     */
    protected $translations;


    /**
     * Translate constructor.
     * @param LangRepository $langRepository
     * @param LangSystemRepository $langSystemRepository
     * @param LangSystemManager $langSystemManager
     */
    public function __construct(LangRepository $langRepository, LangSystemRepository $langSystemRepository, LangSystemManager $langSystemManager)
    {
        $this->langRepository = $langRepository;
        $this->langSystemRepository = $langSystemRepository;
        $this->langSystemManager = $langSystemManager;
    }


    /**
     * Pokud překlad neexistuje, uloží jej jako výchozí (pro EN sekci).
     * Pokud neexistuje překlad z EN do jiného jazyka, vypíše EN překlad.
     * Pokud existuje překlad pro aktuální jazyk, vypíše jej.
     * @param string $en_message
     * @param array $params
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function translate(string $en_message, array $params = []) : string
    {
        $langName = $this->langRepository->getActiveLang()->getName();
        $slug = $this->langSystemManager->generateSlug($en_message);

        if (!$this->translations)
        {
            $this->translations = $this->langSystemRepository->findAll()->toKeyIndex('slug');
        }

        if (!key_exists($slug, $this->translations))
        {
            $this->translations[$slug] = $this->langSystemManager->saveDefault(NULL, $en_message);;
        }


        $langSystem = $this->translations[$slug];

        $langSystemI18ns = $langSystem->getLangSystemI18ns()->toKeyIndex('locale');

        if (key_exists($langName, $langSystemI18ns))
        {
            $langSystem = $langSystemI18ns[$langName];
        }

        $message = $langSystem->getMessage();
        $message = $this->langSystemManager->replaceMessageParams($message, $params);

        return $message;
    }
}