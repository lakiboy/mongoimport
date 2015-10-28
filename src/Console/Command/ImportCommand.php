<?php

namespace Devmachine\MongoImport\Console\Command;

use Devmachine\MongoImport\ImporterBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('import')
            ->setDescription('Imports content from an Extended JSON created by mongoexport.')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'Hostname for the mongod.', null)
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, 'TCP port mongod instance listens.', null)
            ->addOption('db', 'd', InputOption::VALUE_REQUIRED, 'The name of the database on which to run.', 'test')
            ->addOption('drop', null, InputOption::VALUE_NONE, 'Drop the collection before importing the data.')
            ->addOption(
                'collection',
                'c',
                InputArgument::OPTIONAL,
                'The collection to import. If not specified, then the collection name is taken from the input file.'
            )
            ->addArgument('file', InputArgument::REQUIRED, 'Path to file to import.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if (!file_exists($file)) {
            $output->writeln(sprintf('<error>File "%s" not found.</error>', $file));

            return 1;
        }

        $host = $input->getOption('host');
        $port = $input->getOption('port');
        $col  = $input->getOption('collection');
        $db   = $input->getOption('db');
        $drop = $input->hasOption('drop');

        if (!$host) {
            $host = getenv('MONGO_PORT_27017_TCP_ADDR') ?: 'localhost';
        }
        if (!$port) {
            $port = getenv('MONGO_PORT_27017_TCP_PORT') ?: 27017;
        }

        if (!$col) {
            $col = pathinfo($file, PATHINFO_BASENAME);
            if (false !== ($pos = strrpos($col, '.'))) {
                $col = substr($col, 0, $pos);
            }
        }

        $total = (new ImporterBuilder())
            ->setHost($host)
            ->setPort($port)
            ->setDrop($drop)
            ->getImporter()
            ->importCollection($db, $col, $file)
        ;

        $output->writeln(sprintf('<info>Docs inserted: %d</info>', $total));
    }
}
