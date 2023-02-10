<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Command\Listener;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

abstract class ProtectiveConsoleCommandEventListener implements ConsoleCommandEventListener
{
    /**
     * @var array<Command>
     */
    private array $protectedCommands = [];

    protected function __construct(private readonly string $message)
    {
    }

    final public function __invoke(ConsoleCommandEvent $event): void
    {
        if (\in_array($event->getCommand(), $this->protectedCommands, true) && $this->mustPreventCommandExecution($event)) {
            $event->disableCommand();

            $event->getOutput()->writeln('<error>'.$this->message.'</error>');
        }
    }

    final public function protect(Command ...$commands): void
    {
        array_walk($commands, fn (Command $command) => $this->configureCommand($command));

        $this->protectedCommands = [...$this->protectedCommands, ...$commands];
    }

    abstract protected function configureCommand(Command $command): void;

    abstract protected function mustPreventCommandExecution(ConsoleCommandEvent $event): bool;
}
