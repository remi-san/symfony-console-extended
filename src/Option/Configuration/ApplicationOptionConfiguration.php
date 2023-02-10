<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Option\Configuration;

use Assert\Assert;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

final readonly class ApplicationOptionConfiguration
{
    /**
     * @param array<string>                             $optionShortcuts
     * @param array<mixed>|bool|float|int|string        $defaultValue
     * @param array<array<mixed>|bool|float|int|string> $allowedValues
     */
    public function __construct(
        private string $optionName,
        private array $optionShortcuts,
        private ?int $mode,
        private string $description,
        private array|bool|float|int|string $defaultValue,
        private array $allowedValues
    ) {
    }

    public function register(
        Application $application
    ): void {
        $application
            ->getDefinition()
            ->addOption(
                new InputOption(
                    $this->optionName,
                    $this->optionShortcuts,
                    $this->mode,
                    $this->description,
                    $this->defaultValue,
                    $this->allowedValues
                )
            );
    }

    public function getValue(InputInterface $input): mixed
    {
        $formattedShortcuts = array_map(
            static fn (string $shortcut): string => '-'.$shortcut,
            $this->optionShortcuts
        );
        $options = ['--'.$this->optionName, ...$formattedShortcuts];

        $value = $input->getParameterOption($options, $this->defaultValue);
        Assert::that($value)->inArray($this->allowedValues, sprintf('You must provide a valid value for %s.', $this->optionName));

        return $value;
    }
}
