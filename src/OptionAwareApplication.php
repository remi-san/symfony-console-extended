<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Extended\Option\ApplicationOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class OptionAwareApplication extends Application
{
    /** @var array<ApplicationOption> */
    private array $options = [];

    public function registerOption(ApplicationOption $option): void
    {
        $option->registerOption($this);

        $this->options[] = $option;
    }

    /**
     * @throws \Throwable
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->options as $option) {
            $option->handleOptionValue($input, $output);
        }

        return parent::doRun($input, $output);
    }
}
