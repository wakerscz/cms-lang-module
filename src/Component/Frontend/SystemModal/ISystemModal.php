<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Component\Frontend\SystemModal;


interface ISystemModal
{
    /**
     * @return SystemModal
     */
    public function create() : SystemModal;
}