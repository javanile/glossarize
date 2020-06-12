<?php

namespace Javanile\Glossar\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommand extends BaseCommand
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('default')
            ->setDescription('Run glossary analysis as standard PHP package');
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->credits($output);

        $config = $this->getApplication()->getConfig();

        $config->bootstrap();

        $source = $config->getSource();

        $source->scan('src')->glossaryAnalysis();

        return 0;
    }
}
