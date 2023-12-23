<?php

namespace Modules\MartvillKit\Console\Generator;

use Symfony\Component\Process\Process as BaseProcess;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Trait Process
 *
 * This trait provides methods to run processes and display steps.
 */
trait Process
{
    use InteractsWithIO;

    /**
     * The output instance.
     *
     * @var \Symfony\Component\Console\Output\ConsoleOutput
     */
    protected $output;

    /**
     * Constructor for Process trait.
     */
    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    /**
     * Run a process.
     *
     * @param array $command The command to execute
     * @throws ProcessFailedException When the process fails
     */
    public function runProcess(array $command, $isMain = false)
    {
        $process = new BaseProcess($command);

        if ($isMain) {
            $process->run();
            return $process;
        }

        $process->setTimeout(600);

        $process->start(function ($type, $buffer) {
            if (BaseProcess::ERR === $type) {
                echo $buffer;
            } else {
                echo $buffer;
            }
            flush();
        });

        // Wait for the process to finish
        $process->wait();

        $this->line("\n");

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->line($process->getOutput());

        $this->line("\n");
    }

    /**
     * Display a step message.
     *
     * @param string $message The message to display as a step
     */
    public function step(string $message, string $stepLabel = 'STEP:'): void
    {
        $this->line("\n  <question>{$stepLabel}</question> {$message}\n");
    }
}
