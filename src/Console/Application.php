<?php

namespace Devmachine\MongoImport\Console;

use Devmachine\MongoImport\Console\Command\ImportCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('mongoimport', 'dev');
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Dmitri Lakachauskis</comment>';
    }

    /**
     * This is a a single command application.
     *
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'import';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new ImportCommand();

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
