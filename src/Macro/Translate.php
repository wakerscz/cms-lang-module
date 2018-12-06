<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Macro;


use Latte;
use Nette\Bridges\ApplicationLatte\UIMacros;


class Translate extends UIMacros
{
    /**
     * @param Latte\Compiler $compiler
     */
    public static function install(Latte\Compiler $compiler) : void
    {
        $set = new static($compiler);
        $set->addMacro('translate', [$set, 'classicMacroTranslate'], NULL, NULL);
    }


    /**
     * @param Latte\MacroNode $node
     * @param Latte\PhpWriter $writer
     * @return string
     */
    public function classicMacroTranslate(Latte\MacroNode $node, Latte\PhpWriter $writer) : string
    {
        return $writer->write('
            $wakers_translate = %node.array;
            
            if (!isset($wakers_translate[1])) {
                $wakers_translate[1] = [];
            }
            
            echo $this->global->uiPresenter->context->getByType(\Wakers\LangModule\Translator\Translate::class)->translate($wakers_translate[0], $wakers_translate[1]);
        ');
    }
}