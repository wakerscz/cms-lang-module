<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Repository;


use Nette\FileNotFoundException;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Wakers\LangModule\Database\Lang;
use Wakers\LangModule\Database\LangSystem;
use Wakers\LangModule\Database\LangSystemQuery;


class LangSystemRepository
{
    /**
     * Překlady v JS
     */
    const FILE_I18N_SYSTEM = __DIR__ . '/../../../../../i18n/system.js';


    /**
     * Defaultní jazyk systému
     */
    const DEFAULT_SYS_LANG = 'en';


    /**
     * @var LangSystem[]
     */
    protected $all;


    /**
     * @var LangSystem[]
     */
    protected $byPaginate;


    /**
     * @var int
     */
    protected $count;


    /**
     * @return int
     */
    public function count() : int
    {
        if (!$this->count)
        {
            $this->count = LangSystemQuery::create()
                ->count();
        }

        return $this->count;
    }


    /**
     * @param bool $refresh
     * @return ObjectCollection|LangSystem[]
     */
    public function findAll(bool $refresh = FALSE) : ObjectCollection
    {
        if (!$this->all || $refresh)
        {
            $this->all = LangSystemQuery::create()
                ->joinWithLangSystemI18n()
                ->find();
        }

        return $this->all;
    }


    /**
     * @param int $offset
     * @param int $limit
     * @return ObjectCollection|LangSystem[]
     */
    public function findByPaginate(int $offset, int $limit) : ObjectCollection
    {
        if (!$this->byPaginate)
        {
            $langs = LangSystemQuery::create()
                ->offset($offset)
                ->limit($limit)
                ->find();

            $this->byPaginate = LangSystemQuery::create()
                ->joinWithLangSystemI18n()
                ->filterById(array_keys($langs->toKeyIndex()), Criteria::IN)
                ->find();
        }

        return $this->byPaginate;
    }


    /**
     * @param int $id
     * @return LangSystem|NULL
     */
    public function findOneById(int $id) : ?LangSystem
    {
        return LangSystemQuery::create()
            ->joinWithLangSystemI18n()
            ->filterById($id)
            ->find()
            ->getFirst();
    }


    /**
     * @return array
     */
    public function getStaticTranslations() : array
    {
        if (!file_exists(self::FILE_I18N_SYSTEM))
        {
            throw new FileNotFoundException(self::FILE_I18N_SYSTEM);
        }

        $content = file_get_contents('nette.safe://' . self::FILE_I18N_SYSTEM);
        $matches = NULL;

        preg_match_all('/[^\/\/]\$\.i18nSet\(\'(.*)\'\,\s\'(.*)\',\s\'(.*)\'\)\;/', $content, $matches);

        $translations = [];

        foreach ($matches[2] as $key => $slug)
        {
            $lang = $matches[1][$key];
            $message = $matches[3][$key];

            $translations[$slug][$lang] = $message;
        }

        return $translations;
    }


    /**
     * @param ObjectCollection|Lang[] $langs
     * @return array
     */
    public function getStaticTranslationsCorrected(ObjectCollection $langs) : array
    {
        $staticTranslations = [];
        $translations = $this->getStaticTranslations();

        foreach ($translations as $slug => $translation)
        {
            if (isset($translation['all']))
            {
                $result = new \StdClass;
                $result->slug = $slug;
                $result->lang = 'all';
                $result->message = $translation['all'];

                $staticTranslations[] = $result;
            }
            else
            {
                foreach ($langs as $lang)
                {
                    $result = new \StdClass;
                    $result->slug = $slug;
                    $result->lang = $lang->getName();
                    $result->message = NULL;

                    if (isset($translation[$lang->getName()]))
                    {
                        $result->message = $translation[$lang->getName()];
                    }

                    $staticTranslations[] = $result;
                }
            }
        }

        return $staticTranslations;
    }
}