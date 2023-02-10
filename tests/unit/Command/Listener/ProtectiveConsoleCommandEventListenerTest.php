<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Tests\Command\Listener;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Extended\Command\Listener\ProtectiveConsoleCommandEventListener;
use Symfony\Component\Console\Extended\Tests\Helper\InMemoryOutput;
use Symfony\Component\Console\Input\StringInput;

final class ProtectiveConsoleCommandEventListenerTest extends TestCase
{
    private StringInput $input;
    private InMemoryOutput $output;
    private Command $command;

    protected function setUp(): void
    {
        $this->input   = new StringInput('');
        $this->output  = new InMemoryOutput();
        $this->command = new Command('test');
    }

    #[Test]
    public function it_prevents_executing_the_protected_command_if_it_must_be_protected(): void
    {
        $listener = $this->getConsoleCommandEventListener('Prevent', true);
        $listener->protect($this->command);

        $event = new ConsoleCommandEvent($this->command, $this->input, $this->output);
        $listener($event);

        self::assertCount(1, $this->output->lines);
        self::assertEquals('<error>Prevent</error>'.\PHP_EOL, $this->output->lines[0]);
        self::assertFalse($event->commandShouldRun());
    }

    #[Test]
    public function it_does_not_prevent_executing_a_protected_command_if_it_must_not_be_protected(): void
    {
        $listener = $this->getConsoleCommandEventListener('Prevent', false);
        $listener->protect($this->command);

        $event = new ConsoleCommandEvent($this->command, $this->input, $this->output);
        $listener($event);

        self::assertCount(0, $this->output->lines);
        self::assertTrue($event->commandShouldRun());
    }

    #[Test]
    public function it_does_not_prevent_executing_a_non_protected_command(): void
    {
        $listener = $this->getConsoleCommandEventListener('Prevent');

        $event = new ConsoleCommandEvent($this->command, $this->input, $this->output);
        $listener($event);

        self::assertCount(0, $this->output->lines);
        self::assertTrue($event->commandShouldRun());
    }

    public function getConsoleCommandEventListener(string $message, bool $prevent = true): ProtectiveConsoleCommandEventListener
    {
        return new class($message, $prevent) extends ProtectiveConsoleCommandEventListener {
            public function __construct(string $message, private readonly bool $prevent)
            {
                parent::__construct($message);
            }

            protected function configureCommand(Command $command): void
            {
            }

            protected function mustPreventCommandExecution(ConsoleCommandEvent $event): bool
            {
                return $this->prevent;
            }
        };
    }
}
