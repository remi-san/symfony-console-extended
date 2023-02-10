<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Command\Listener;

use Symfony\Component\Console\Event\ConsoleCommandEvent;

interface ConsoleCommandEventListener
{
    public function __invoke(ConsoleCommandEvent $event): void;
}
