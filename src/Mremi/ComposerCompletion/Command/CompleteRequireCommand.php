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

use Packagist\Api\Client;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Completes "composer require"
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class CompleteRequireCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('complete:require')
            ->setDescription('Writes the packages matching the given search by calling the Packagist API')
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

        $cache   = new Cache;
        $vendors = $cache->find($search);

        if ($vendors) {
            foreach ($vendors as $vendor) {
                $output->write(file_get_contents($vendor));
            }

            return;
        }

        $client = new Client;

        foreach ($client->search($search) as $result) {
            $output->write(sprintf('%s ', $result->getName()));

            list($vendor, $package) = explode('/', $result->getName());

            $cache->write($vendor, $result->getName());
        }
    }
}
