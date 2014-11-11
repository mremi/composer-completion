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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Completes "composer update"
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class CompleteUpdateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('complete:update')
            ->setDescription('Writes the packages matching the given search by looking your composer.json')
            ->addArgument('search', InputArgument::REQUIRED, 'Name of the searched package');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $search = $input->getArgument('search');

        if (strlen($search) < 3) {
            return;
        }

        $composerFile = sprintf('%s/composer.json', getcwd());

        if (!is_file($composerFile)) {
            return;
        }

        $config = json_decode(file_get_contents($composerFile), true);

        foreach (array('require-dev', 'require') as $require) {
            if (!array_key_exists($require, $config)) {
                continue;
            }

            foreach (array_keys($config[$require]) as $package) {
                if (preg_match(sprintf('#^%s#', $search), $package)) {
                    $output->write(sprintf('%s ', $package));
                }
            }
        }
    }
}
