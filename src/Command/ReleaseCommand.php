<?php

declare(strict_types=1);

namespace Pheanstalk\Command;

use Pheanstalk\Contract\JobIdInterface;
use Pheanstalk\Exception;
use Pheanstalk\Exception\UnsupportedResponseException;
use Pheanstalk\RawResponse;
use Pheanstalk\ResponseType;
use Pheanstalk\Success;

/**
 * The 'release' command.
 *
 * Releases a reserved job back onto the ready queue.
 */
final class ReleaseCommand extends JobCommand
{
    public function __construct(JobIdInterface $job, private readonly int $priority, private readonly int $delay)
    {
        parent::__construct($job);
    }

    public function interpret(RawResponse $response): Success
    {
        return match ($response->type) {
            ResponseType::NotFound => throw new Exception\JobNotFoundException(),
            ResponseType::Released => new Success(),
            default => throw new UnsupportedResponseException($response->type)
        };
    }

    protected function getCommandTemplate(): string
    {
        return "release {id} {$this->priority} {$this->delay}";
    }
}
