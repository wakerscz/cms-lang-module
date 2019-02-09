<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Component\Frontend\SystemModal;


use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Utils\Paginator;
use Wakers\BaseModule\Component\Frontend\BaseControl;
use Wakers\BaseModule\Util\AjaxValidate;
use Wakers\LangModule\Manager\LangSystemManager;
use Wakers\LangModule\Repository\LangRepository;
use Wakers\LangModule\Repository\LangSystemRepository;


class SystemModal extends BaseControl
{
    use AjaxValidate;


    /**
     * Počet položek na stránku
     */
    const ITEMS_PER_PAGE = 10;


    /**
     * @var LangSystemRepository
     */
    protected $langSystemRepository;


    /**
     * @var LangRepository
     */
    protected $langRepository;


    /**
     * @var LangSystemManager
     */
    protected $langSystemManager;


    /**
     * @var Paginator
     */
    protected $paginator;


    /**
     * @var callable
     */
    public $onSave = [];


    /**
     * @var callable
     */
    public $onPaginate = [];


    /**
     * @var int
     * @persistent
     */
    public $page = 1;


    /**
     * SystemModal constructor.
     * @param LangSystemRepository $langSystemRepository
     * @param LangRepository $langRepository
     * @param LangSystemManager $langSystemManager
     */
    public function __construct(
        LangSystemRepository $langSystemRepository,
        LangRepository $langRepository,
        LangSystemManager $langSystemManager
    ) {
        $this->langSystemRepository = $langSystemRepository;
        $this->langRepository = $langRepository;
        $this->langSystemManager = $langSystemManager;
    }


    /**
     * Render
     */
    public function render() : void
    {
        $this->initPaginator();

        $langs = $this->langRepository->getLangs();
        $langSystems = $this->langSystemRepository->findByPaginate($this->paginator->getOffset(), $this->paginator->getLength()); // Offset
        $staticTranslations = $this->langSystemRepository->getStaticTranslationsCorrected($langs);

        foreach ($langSystems as $l)
        {
            $message = $l->getLangSystemI18ns()->toKeyIndex('Locale')[LangSystemRepository::DEFAULT_SYS_LANG]->getMessage();
            $l->setVirtualColumn('DefaultMessage', $message);
        }

        $this->template->paginator = $this->paginator;
        $this->template->langSystemManager = $this->langSystemManager;

        $this->template->count = $this->langSystemRepository->count();
        $this->template->langs = $langs;
        $this->template->staticTranslations = $staticTranslations;
        $this->template->langSystems = $langSystems;

        $this->template->setFile(__DIR__ . '/templates/systemModal.latte');
        $this->template->render();
    }


    /**
     * @param int $langSystemId
     * @return Form
     */
    private function form(int $langSystemId) : Form
    {
        $this->initPaginator();

        $langSystems = $this->langSystemRepository->findByPaginate($this->paginator->getOffset(), $this->paginator->getLength())->toKeyIndex(); // Offset
        $langs = $this->langRepository->getLangs();
        $i18ns = $langSystems[$langSystemId]->getLangSystemI18ns()->toKeyIndex('Locale');

        $form = new Form;

        foreach ($langs as $lang)
        {
            $value = isset($i18ns[$lang->getName()]) ? $i18ns[$lang->getName()]->getMessage() : NULL;

            $form->addText($lang->getName(), $lang->getName())
                ->setRequired("Jazyk: {$lang->getName()} je povinný.")
                ->setDefaultValue($value);

            $params = $langSystems[$langSystemId]->getParams();

            if ($params)
            {
                foreach (explode(' | ', $params) as $param)
                {
                    $form[$lang->getName()]->addRule(Form::PATTERN, "Musíte použít tyto '{$param}' proměnné.", ".*{$param}.*");
                }
            }
        }

        $form->addHidden('langSystemId', $langSystemId);
        $form->addSubmit('save');

        $form->onValidate[] = function (Form $form) { $this->validate($form); };
        $form->onSuccess[] = function (Form $form) { $this->success($form); };

        return $form;
    }


    /**
     * @param Form $form
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function success(Form $form) : void
    {
        if ($this->presenter->isAjax())
        {
            $values = $form->getValues(TRUE);

            $i18ns = [];

            foreach ($this->langRepository->getLangs() as $lang)
            {
                $i18ns[$lang->getName()] = $values[$lang->getName()];
            }

            $langSystem = $this->langSystemRepository->findOneById($values['langSystemId']);
            $this->langSystemManager->save($langSystem, $i18ns);

            $this->presenter->notificationAjax(
                'Překlad uložen',
                'Překlad byl úspěšně uložen',
                'success',
                FALSE
            );

            $this->onSave();
        }
    }


    /**
     * @return Multiplier
     */
    protected function createComponentTranslateForm() : Multiplier
    {
        return new Multiplier(function (int $langSystemId)
        {
            return self::form($langSystemId);
        });
    }


    public function handlePaginate(int $page) : void
    {
        if ($this->presenter->isAjax())
        {
            $this->page = $page;
            $this->onPaginate();
        }
    }


    protected function initPaginator() : void
    {
        $this->paginator = new Paginator;
        $this->paginator->setPage($this->page);
        $this->paginator->setItemsPerPage(self::ITEMS_PER_PAGE);
        $this->paginator->setItemCount($this->langSystemRepository->count());
    }
}