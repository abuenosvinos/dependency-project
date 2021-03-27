<?php

namespace App\Infrastructure\UI\Command;

use App\Domain\Entity\Project;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Envelope;
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
        $projects = $this->processEnvelope($this->queryBus->dispatch(new \App\Application\Show\ShowQuery()));
        $this->printProjects($projects, 0);

        return Command::SUCCESS;
    }

    private function printProjects(array $projects, int $level)
    {
        /** @var Project $project */
        foreach ($projects as $project) {
            //$this->io->section()
            $this->io->section(sprintf('Dependencias del proyecto: %s:%s', $project->name(), $project->version()));

            $this->printLevels($level);
            if ($project->sons()->count() > 0) {
                $this->printSons($project->sons(), $level + 1);
            } else {
                $this->io->writeln('No tiene ninguna dependencia');
            }

            $this->io->writeln('');
            $this->io->writeln('');
            $this->io->writeln('');
        }
    }

    private function printSons(Collection $projects, int $level)
    {
        /** @var Project $project */
        foreach ($projects as $project) {
            $this->printLevels($level);
            $this->io->writeln($project->name());
            if ($project->sons()->count() > 0) {
                $this->printSons($project->sons(), $level + 1);
            }
        }
    }

    private function printLevels(int $level)
    {
        for ($i=0; $i<$level; $i++) {
            $this->io->write('-');
        }
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
The <info>%command.name%</info> show the dependencies between projects
HELP;
    }
}
