<?php

/*
 * This file is part of the mremi\composer-completion library.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\ComposerCompletion\Command;

use Mremi\ComposerCompletion\Cache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clears the cache
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class CacheClearCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clears the cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache = new Cache;
        $cache->clear();

        $output->writeln('<info>Done</info>');
    }
}
