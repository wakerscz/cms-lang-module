<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Repository;


use Propel\Runtime\Collection\ObjectCollection;
use Wakers\LangModule\Database\Lang;
use Wakers\LangModule\Database\LangQuery;


class LangRepository
{
    /**
     * Aktuálně nastavený jazyk pro frontend (entita Lang)
     * Nastavuje se v presenteru podle aktuální Page
     * @var Lang
     */
    protected $activeLang;


    /**
     * Defaultní název jazyka pro administraci (string)
     * @var string
     */
    protected $adminLang;



    /**
     * Všechny definované jazyky
     * @var ObjectCollection
     */
    protected $langs;


    /**
     * LangRepository constructor.
     * @param string $adminLang
     */
    public function __construct(string $adminLang)
    {
        $this->adminLang = $adminLang;
    }


    /**
     * @param string $name
     * @return Lang|NULL
     */
    public function findOneByName(string $name) : ?Lang
    {
        return LangQuery::create()
            ->findOneByName($name);
    }


    /**
     * Nastavuje výchozí jazyk - volá se v presenteru
     * @param Lang $activeLang
     */
    public function setActiveLang(Lang $activeLang)
    {
        $activeLang->clearAllReferences();
        $this->activeLang = $activeLang;
    }


    /**
     * Vrací aktuálně nastavený jazyk
     * @return Lang
     */
    public function getActiveLang() : Lang
    {
        return $this->activeLang;
    }


    /**
     * Vrací všechny definované jazyky
     * @param bool $refresh
     * @return ObjectCollection|Lang[]
     */
    public function getLangs(bool $refresh = FALSE) : ObjectCollection
    {
        if (!$this->langs || $refresh)
        {
            $this->langs = LangQuery::create()
                ->find();
        }

        return $this->langs;
    }


    /**
     * @return Lang
     * @throws \Exception
     */
    public function getAdminLang() : Lang
    {
        if (!$this->adminLang)
        {
            throw new \Exception("Missing parameter 'adminLang' in config file.");
        }

        $lang = LangQuery::create()
            ->findOneByName($this->adminLang);

        if (!$lang)
        {
            throw new \Exception("Default admin lang: '{$this->adminLang}', is not initialized in DB.");
        }

        return $lang;
    }
}