<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Tests\Option\Configuration;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Extended\Option\Configuration\ApplicationOptionConfiguration;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

final class ApplicationOptionConfigurationTest extends TestCase
{
    private ApplicationOptionConfiguration $configuration;

    protected function setUp(): void
    {
        $this->configuration = new ApplicationOptionConfiguration(
            'my-option',
            ['m', 'p'],
            InputOption::VALUE_REQUIRED,
            'description',
            'allowed1',
            ['allowed1', 'allowed2']
        );
    }

    #[Test]
    public function it_should_retrieve_the_option(): void
    {
        self::assertEquals('allowed1', $this->configuration->getValue(new StringInput('')));

        self::assertEquals('allowed1', $this->configuration->getValue(new StringInput('--my-option=allowed1')));
        self::assertEquals('allowed1', $this->configuration->getValue(new StringInput('-mallowed1')));
        self::assertEquals('allowed1', $this->configuration->getValue(new StringInput('-pallowed1')));

        self::assertEquals('allowed2', $this->configuration->getValue(new StringInput('--my-option=allowed2')));
        self::assertEquals('allowed2', $this->configuration->getValue(new StringInput('-mallowed2')));
        self::assertEquals('allowed2', $this->configuration->getValue(new StringInput('-pallowed2')));
    }

    #[Test]
    public function it_should_fail_retrieving_a_missing_option(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->configuration->getValue(new StringInput('--my-option'));
    }

    #[Test]
    public function it_should_fail_retrieving_an_invalid_option(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->configuration->getValue(new StringInput('--my-option=invalid'));
    }

    #[Test]
    public function it_should_make_the_option_available_in_the_application(): void
    {
        $application = new Application('TEST', '1.0.0');
        $this->configuration->register($application);
        $output = new NullOutput();

        self::assertEquals(0, $application->doRun(new StringInput('--my-option=allowed1'), $output));

        $this->expectException(\RuntimeException::class);
        $application->doRun(new StringInput('--non-existing-option'), $output);
    }
}
