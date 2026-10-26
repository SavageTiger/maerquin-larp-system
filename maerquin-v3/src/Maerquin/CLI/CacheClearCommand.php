<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\CLI;

use Override;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends Command
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
    ) {
        parent::__construct();
    }

    #[Override]
    protected function configure(): void
    {
        $this
            ->setName('maerquin:cache:clear')
            ->setDescription('Clears the application cache (Doctrine metadata/queries and Twig templates)');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->cache->clear() === false) {
            $output->writeln('<error>Failed to clear the cache</error>');

            return self::FAILURE;
        }

        $output->writeln('<info>Cache cleared</info>');

        return self::SUCCESS;
    }
}
