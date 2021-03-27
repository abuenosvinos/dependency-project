<?php

namespace App\Infrastructure\UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class DependenciesCommand extends Command
{
    protected static $defaultName = 'app:show';

    private MessageBusInterface $queryBus;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(MessageBusInterface $queryBus)
    {
        parent::__construct();

        $this->queryBus = $queryBus;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('project', InputArgument::REQUIRED, 'Project to find dependencies')
            ->setDescription('Show the projects afected by the change of a project')
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
        $this->queryBus->dispatch(new \App\Application\Dependencies\DependenciesQuery());

        return Command::SUCCESS;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> show the projects afected by the change of a project
HELP;
    }
}
