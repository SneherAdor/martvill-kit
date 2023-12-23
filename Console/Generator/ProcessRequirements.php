<?php 

namespace Modules\MartvillKit\Console\Generator;

use Modules\MartvillKit\Console\Generator\Process;

class ProcessRequirements
{
    use Process;

    /**
     * Check if the process requirements are valid.
     *
     * @return void
     */
    public function check(): void
    {
        $this->step('Checking if process requirements are valid...', ' - ');

        $this->isGitDirectory();
        $this->isGitInstalled();
        $this->isComposerInstalled();
        $this->is7zipInstalled();
    }

    /**
     * Check if the current directory is a git directory.
     *
     * @return void
     */
    private function isGitDirectory(): void
    {
        $process = $this->runProcess(['git', 'rev-parse', '--is-inside-work-tree'], true);

        if (!$process->isSuccessful()) {
            $this->line("  <error>ERROR:</error> The current directory is not a git directory.");
            exit;
        }

        $this->line("  <info>OK:</info> The current directory is a git directory.");
    }

    /**
     * Check if Git is installed.
     *
     * @return void
     */
    private function isGitInstalled(): void
    {
        $process = $this->runProcess(['git', '--version'], true);

        if (!$process->isSuccessful()) {
            $this->line("  <error>ERROR:</error> Git is not installed. Please install git and try again.");
            exit;
        }

        $this->line("  <info>OK:</info> Git is installed.");
    }

    /**
     * Check if 7zip is installed.
     *
     * @return void
     */
    private function is7zipInstalled(): void
    {
        $process = $this->runProcess(['7z', '-h'], true);

        if (!$process->isSuccessful()) {
            $this->line("  <error>ERROR:</error> 7zip is not installed. Please install 7zip and try again.");
            exit;
        }

        $this->line("  <info>OK:</info> 7zip is installed.");
    }

    /**
     * Check if Composer is installed.
     *
     * @return void
     */
    private function isComposerInstalled(): void
    {
        $process = $this->runProcess(['composer', '--version'], true);

        if (!$process->isSuccessful()) {
            $this->line("  <error>ERROR:</error> Composer is not installed. Please install composer and try again.");
            exit;
        }

        $this->line("  <info>OK:</info> Composer is installed.");
    }
}
