<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Manager;


use Wakers\BaseModule\Database\DatabaseException;
use Wakers\LangModule\Database\Lang;
use Wakers\LangModule\Repository\LangRepository;


class LangManager
{
    /**
     * @var LangRepository
     */
    protected $langRespository;


    /**
     * LangManager constructor.
     * @param LangRepository $langRepository
     */
    public function __construct(LangRepository $langRepository)
    {
        $this->langRespository = $langRepository;
    }


    /**
     * @param string $name
     * @return Lang
     */
    public function add(string $name) : Lang
    {
        if ($this->langRespository->findOneByName($name))
        {
            throw new DatabaseException("Jazyk '{$name}' již existuje.");
        }

        $lang = new Lang;
        $lang->setName($name);
        $lang->save();

        return $lang;
    }
}