<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Manager;


use Nette\Utils\Strings;
use Wakers\BaseModule\Util\Validator;
use Wakers\LangModule\Database\LangSystem;
use Wakers\LangModule\Database\LangSystemI18n;


class LangSystemManager
{
    /**
     * Pattern pro vyhledání parametrů
     */
    const PARAM_PATTERN = '/(\%[a-zA-Z0-9\-\_\\^\%]+\%)/';


    /**
     * Defultní systémový jazyk hlášek
     */
    const DEFAULT_LOCALE = 'en';


    /**
     * @param LangSystem|NULL $langSystem
     * @param string $en_message
     * @return LangSystem
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function saveDefault(?LangSystem $langSystem, string $en_message) : LangSystem
    {
        $params = $this->implodeParams($en_message);
        $params = Validator::isStringEmpty($params) ? NULL : $params;

        if (!$langSystem)
        {
            $langSystem = new LangSystem();
            $langSystem->setLocale(self::DEFAULT_LOCALE);
            $langSystem->setSlug($this->generateSlug($en_message));
        }

        $langSystem->setParams($params);
        $langSystem->setMessage($en_message);

        $langSystem->save();

        return $langSystem;
    }


    /**
     * @param LangSystem $langSystem
     * @param array $i18ns
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function save(LangSystem $langSystem, array $i18ns) : void
    {
        $dbI18ns = $langSystem->getLangSystemI18ns()->toKeyIndex('Locale');

        foreach ($i18ns as $locale => $message)
        {
            if (!key_exists($locale, $dbI18ns))
            {
                $i18n = new LangSystemI18n;
                $i18n->setLangSystem($langSystem);
                $i18n->setLocale($locale);
            }
            else
            {
                $i18n = $dbI18ns[$locale];
            }

            $i18n->setMessage($message);
            $i18n->save();
        }
    }


    /**
     * @param string $message
     * @return array
     */
    public function searchParams(string $message) : array
    {
        $params = [];

        preg_match_all(self::PARAM_PATTERN, $message,$params);

        $params = isset($params[0]) ? $params[0] : [];

        return $params;
    }


    /**
     * @param string $message
     * @return string
     */
    public function implodeParams(string $message) : string
    {
        $params = implode(' | ', $this->searchParams($message));

        return $params;
    }


    /**
     * @param string $en_message
     * @return string
     */
    public function generateSlug(string $en_message)
    {
        return Strings::webalize($en_message, '%');
    }


    /**
     * @param string $message
     * @param array $params
     * @return string
     */
    public function replaceMessageParams(string $message, array $params) : string
    {
        foreach ($params as $key => $param)
        {
            $message = str_replace("%{$key}%", $param, $message);
        }

        return $message;
    }
}