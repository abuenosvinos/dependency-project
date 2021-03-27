<?php

namespace App\Infrastructure\UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class ShowCommand extends Command
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
            ->setDescription('Show the dependencies')
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
        $envelope = $this->queryBus->dispatch(new \App\Application\Show\ShowQuery());

        /** @var HandledStamp $stamp */
        $stamp = $envelope->last(HandledStamp::class);
        dump($stamp->getResult());

        return Command::SUCCESS;
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> show the dependencies between projects
HELP;
    }
}
