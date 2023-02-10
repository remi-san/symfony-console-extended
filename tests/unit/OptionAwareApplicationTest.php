<?php

declare(strict_types=1);

namespace Symfony\Component\Console\Extended\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Extended\Option\ApplicationOption;
use Symfony\Component\Console\Extended\OptionAwareApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

final class OptionAwareApplicationTest extends TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
    }

    /**
     * @throws \Throwable
     */
    #[Test]
    public function it_should_register_an_option(): void
    {
        $application = new OptionAwareApplication('TEST', '1.0.0');

        $option = \Mockery::spy(ApplicationOption::class);
        $application->registerOption($option);

        $input  = new StringInput('');
        $output = new NullOutput();

        $return = $application->doRun($input, $output);

        self::assertEquals(0, $return);
        $option->shouldHaveReceived('registerOption')->with($application)->once();
        $option->shouldHaveReceived('handleOptionValue')->with($input, $output)->once();
    }
}
