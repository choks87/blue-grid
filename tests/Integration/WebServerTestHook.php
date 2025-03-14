<?php

declare(strict_types=1);

namespace BlueGrid\Tests\Integration;

use Symfony\Component\Process\Process;

final class WebServerTestHook
{
    private Process $swooleProcess;

    public function __construct(
        private readonly string $hostname,
        private readonly int    $port,
        private readonly int    $timeout,
        private readonly string $pidFile,
    ) {
    }

    public function beforeTest(): void
    {
        $this->killExistingProcess();

        $symfonyConsole   = './bin/console';
        $defaultArguments = ['--env=test', '--no-debug', '--no-interaction'];
        $commands         = [
            ['cache:clear', []],
            ['doctrine:database:drop', ['--force', '--if-exists']],
            ['doctrine:database:create', []],
            ['doctrine:schema:update', ['--force']],
            ['doctrine:fixture:load', ['--no-interaction']],
        ];

        foreach ($commands as [$command, $arguments]) {

            if ((getenv('BOOTSTRAP') ?? 'true') === 'false') {
                continue;
            }

            $process = new Process(\array_merge([$symfonyConsole], [$command], $arguments, $defaultArguments));
            $exitCode = $process->run();

            if ($exitCode !== 0) {
                $message = \sprintf(
                    "Command %s ended with exit code %s (%s). %s. %s",
                    $process->getCommandLine(),
                    $process->getExitCode(),
                    $process->getExitCodeText(),
                    $process->getErrorOutput(),
                    $process->getOutput(),
                );

                throw new \RuntimeException($message);
            }
        }

        $this->swooleProcess = new Process(
            \array_merge(
                [$symfonyConsole],
                ['swoole:server:run'],
                $defaultArguments
            )
        );

        $this->swooleProcess->start();
        $this->writePidToFile();
        $this->waitForServerToCreateSocket();
    }

    final public function afterTest(): void
    {
        $this->kill();
    }

    private function kill(): void
    {
        if (!$this->swooleProcess->isRunning()) {
            return;
        }

        $this->swooleProcess->signal(SIGKILL);
        $this->removePidFile();
    }

    private function writePidToFile(): void
    {
        file_put_contents($this->pidFile, $this->swooleProcess->getPid());
    }

    private function killExistingProcess(): void
    {
        $pid = (int)@file_get_contents($this->pidFile);

        if (empty($pid)) {
            return;
        }

        $killProcess = new Process(['kill', $pid]);
        $killProcess->run();

        $this->removePidFile();
    }

    private function removePidFile(): void
    {
        @unlink($this->pidFile);
    }

    private function waitForServerToCreateSocket(): void
    {
        $startTime = time();

        while (false === is_resource(@fsockopen($this->hostname, $this->port))) {
            usleep(50000); // Sleeping for 50ms

            if (time() - $startTime > $this->timeout) {
                $this->swooleProcess->signal(SIGKILL);
                trigger_error('Timeout for waiting test server to start is reached');
                die();
            }
        }
    }
}
