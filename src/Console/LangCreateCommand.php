<?php
/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 */


namespace Wakers\LangModule\Console;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wakers\BaseModule\Database\DatabaseException;
use Wakers\LangModule\Manager\LangManager;


class LangCreateCommand extends Command
{
    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('wakers:lang-create')
            ->setDescription('Creating new language')
            ->addArgument('name', InputArgument::REQUIRED, 'Name');
        ;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var LangManager $langManager */
        $langManager = $this->getHelper('container')->getByType(LangManager::class);

        $name = $input->getArgument('name');

        try
        {
            $langManager->add($name);
            $output->writeln("<info>Jazyk '{$name}' byl vytvořen.</info>");
        }
        catch (DatabaseException $exception)
        {
            $output->writeln("<error>{$exception->getMessage()}</error>");
        }
    }
}