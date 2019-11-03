<?php

declare(strict_types=1);

namespace App\Commands;

use Illuminate\Support\Facades\Cache;
use Phinx\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClear extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName('cache:clear')
             ->setDescription('Flush the application cache');
    }

    /**
     * Cache cleared
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        Cache::flush();

        $output->writeln('<info>Cache cleared successfully.</info>');
    }
}
