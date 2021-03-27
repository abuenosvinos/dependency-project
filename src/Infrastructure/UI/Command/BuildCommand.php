<?php

namespace App\Infrastructure\UI\Command;

use App\Domain\Entity\Repo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BuildCommand extends Command
{
    protected static $defaultName = 'app:build';

    private MessageBusInterface $commandBus;
    private ContainerBagInterface $params;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(MessageBusInterface $commandBus, ContainerBagInterface $params)
    {
        parent::__construct();

        $this->commandBus = $commandBus;
        $this->params = $params;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('paths', InputArgument::IS_ARRAY, 'Paths to process')
            ->setDescription('Build the dependencies')
            ->setHelp($this->getCommandHelp())
        ;
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathRepositories = $this->params->get('path_repositories');
        $paths = $input->getArgument('paths');

        $listRepos = [];
        foreach ($paths as $path) {
            $pathRepo = $pathRepositories . $path;
            $listRepos[] = Repo::fromPrimitives($pathRepo);
        }

        $this->commandBus->dispatch(new \App\Application\Build\BuildCommand($listRepos));

        $this->io->success('Se ha generado la estructura de dependencias');

        return Command::SUCCESS;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> build the dependencies between projects
HELP;
    }
}
