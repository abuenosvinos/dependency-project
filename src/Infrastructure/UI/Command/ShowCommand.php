<?php

namespace App\Infrastructure\UI\Command;

use App\Domain\Entity\Project;
use App\Shared\Domain\Bus\Query\QueryBus;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowCommand extends Command
{
    protected static $defaultName = 'app:show';

    private QueryBus $queryBus;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(QueryBus $queryBus)
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
        $projects = $this->queryBus->ask(new \App\Application\Show\ShowQuery());
        $this->printProjects($projects, 0);

        return Command::SUCCESS;
    }

    private function printProjects(array $projects, int $level)
    {
        /** @var Project $project */
        foreach ($projects as $project) {
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
            $this->io->writeln(sprintf(' %s:%s', $project->name(), $project->version()));
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

    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> show the dependencies between projects
HELP;
    }
}
