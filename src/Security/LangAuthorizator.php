<?php
/**
 * Copyright (c) 2019 Wakers.cz
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 */


namespace Wakers\LangModule\Security;


use Wakers\BaseModule\Builder\AclBuilder\AuthorizatorBuilder;
use Wakers\UserModule\Security\UserAuthorizator;


class LangAuthorizator extends AuthorizatorBuilder
{
    const
        RES_MODULE = 'LANG_RES_MODULE',         // Celý modul
        RES_SUMMARY = 'LANG_RES_SUMMARY',       // Přehled překladů
        RES_FORM = 'LANG_RES_FORM'              // Formulář pro uložení překladu
    ;


    public function create() : array
    {
        /*
         * Resources
         */
        $this->addResource(self::RES_MODULE);
        $this->addResource(self::RES_SUMMARY);
        $this->addResource(self::RES_FORM);


        /*
         * Privileges
         */
        $this->allow([
            UserAuthorizator::ROLE_EDITOR
        ], [
            self::RES_MODULE,
            self::RES_SUMMARY,
            self::RES_FORM
        ]);


        return parent::create();
    }
}