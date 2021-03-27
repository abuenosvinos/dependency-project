<?php

namespace App\Infrastructure\UI\Command;

use App\Domain\Entity\Project;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class DependenciesCommand extends Command
{
    protected static $defaultName = 'app:dependencies';

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
        $project = $input->getArgument('project');

        $projects = $this->processEnvelope($this->queryBus->dispatch(new \App\Application\Dependencies\DependenciesQuery($project)));

        $this->io->section(sprintf('Proyectos a actualizar a partir del cambio del proyecto %s', $project));

        if (count($projects) > 0) {
            /** @var Project $project */
            foreach ($projects as $project) {
                $this->io->writeln(sprintf('- %s:%s', $project->name(), $project->version()));
            }

        } else {
            $this->io->writeln('No hay ningún proyecto a actualizar');

        }

        return Command::SUCCESS;
    }

    // TODO; Habría que Encapsular el symfony/messenger con clases propias
    private function processEnvelope(Envelope $envelope)
    {
        /** @var HandledStamp $stamp */
        $stamp = $envelope->last(HandledStamp::class);
        return $stamp->getResult();
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> show the projects afected by the change of a project
HELP;
    }
}
