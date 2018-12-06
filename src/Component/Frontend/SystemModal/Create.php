<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Component\Frontend\SystemModal;


trait Create
{
    /**
     * @var ISystemModal
     * @inject
     */
    public $ILang_SystemModal;


    /**
     * Přehled systémových překladů
     * @return object
     */
    protected function createComponentLangSystemModal() : object
    {
        $control = $this->ILang_SystemModal->create();

        $control->onSave[] = function () use ($control)
        {
            $control->redrawControl('langSystems');
        };

        $control->onPaginate[] = function () use ($control)
        {
            $control->redrawControl('paginator');
            $control->redrawControl('pagination');
            $control->redrawControl('langSystems');
        };

        return $control;
    }
}