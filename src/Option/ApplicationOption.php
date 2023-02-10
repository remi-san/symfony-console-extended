<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Option;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface ApplicationOption
{
    public function registerOption(Application $application): void;

    public function handleOptionValue(InputInterface $input, OutputInterface $output): void;
}
