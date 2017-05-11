<?php

namespace StefanoRuth\WindowsHostsManager;

use StefanoRuth\WindowsHostsManager\HostFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddHostCommand extends Command
{
    public static $file = 'C:\Windows\System32\drivers\etc\hosts';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('add')
            ->setDescription('Add a new domain.')
            ->addArgument('ip', InputArgument::REQUIRED)
            ->addArgument('domain', InputArgument::REQUIRED);
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        HostFile::load()
                ->add($input->getArgument('ip'), $input->getArgument('domain'))
                ->save()
                ->show($output);
    }
}
