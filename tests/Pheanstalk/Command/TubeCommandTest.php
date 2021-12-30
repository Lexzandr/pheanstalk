<?php

declare(strict_types=1);

namespace Pheanstalk\Tests\Command;

use Pheanstalk\Command\TubeCommand;
use Pheanstalk\Exception\TubeNotFoundException;
use Pheanstalk\RawResponse;
use Pheanstalk\ResponseType;
use Pheanstalk\TubeName;
use PHPUnit\Framework\Assert;

abstract class TubeCommandTest extends CommandTest
{
    abstract protected function getSubject(TubeName $tube = null): TubeCommand;

    public function testInterpretNotFound(): void
    {
        if (!in_array(ResponseType::NotFound, $this->getSupportedResponses(), true)) {
            Assert::markTestSkipped("Not applicable");
        }
        $command = $this->getSubject();

        $this->expectException(TubeNotFoundException::class);
        $command->interpret(new RawResponse(ResponseType::NotFound));
    }


    /**
     * @phpstan-return iterable<array<string>>
     */
    public function tubeNameProvider(): iterable
    {
        yield ["5"];
        yield ["12345678901234562222222323112312312312312312312312312312312312321312312313212378900"];
        yield ["00001123"];
        yield ["ab-cdef"];
        yield ["\$abc"];
        yield ["ab(14\$--_.4"];
    }

    /**
     * @dataProvider tubeNameProvider
     */
    public function testCommandLineIncludesTubeName(string $tubeName): void
    {
        $commandLine = $this->getSubject(new TubeName($tubeName))->getCommandLine();
        Assert::assertStringContainsString($tubeName, $commandLine);
        Assert::assertMatchesRegularExpression('/^[a-z\-]+\s+.+(\s+.+)?$/', $commandLine);
    }
}
