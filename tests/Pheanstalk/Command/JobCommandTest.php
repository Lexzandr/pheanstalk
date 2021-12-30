<?php

declare(strict_types=1);

namespace Pheanstalk\Tests\Command;

use Pheanstalk\Command\JobCommand;
use Pheanstalk\Contract\JobIdInterface;
use Pheanstalk\Exception\JobNotFoundException;
use Pheanstalk\JobId;
use Pheanstalk\RawResponse;
use Pheanstalk\ResponseType;
use PHPUnit\Framework\Assert;

abstract class JobCommandTest extends CommandTest
{
    abstract protected function getSubject(JobIdInterface $jobId = null): JobCommand;

    public function testInterpretNotFound(): void
    {
        $command = $this->getSubject();

        $this->expectException(JobNotFoundException::class);
        $command->interpret(new RawResponse(ResponseType::NotFound));
    }


    /**
     * @phpstan-return iterable<array{0: string}>
     */
    public function jobIdProvider(): iterable
    {
        yield ["5"];
        yield ["12345678901234562222222323112312312312312312312312312312312312321312312313212378900"];
        yield ["00001123"];
    }

    /**
     * @dataProvider jobIdProvider
     */
    public function testCommandLineIncludesId(string $id): void
    {
        $commandLine = $this->getSubject(new JobId($id))->getCommandLine();
        Assert::assertStringContainsString($id, $commandLine);
        Assert::assertMatchesRegularExpression('/^[a-z\-]+\s+\d+(\s+.+)?$/', $commandLine);
    }
}
