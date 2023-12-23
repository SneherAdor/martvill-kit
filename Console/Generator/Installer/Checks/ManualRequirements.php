<?php 

namespace Modules\MartvillKit\Console\Generator\Installer\Checks;

use Modules\MartvillKit\Console\Generator\Process;

class ManualRequirements
{
    use Process;

    /**
     * The console instance.
     *
     * @var \Illuminate\Console\Command
     */
    public $console;

    /**
     * Check if the process requirements are valid.
     *
     * @return void
     */
    public function check($console): void
    {
        $this->console = $console;

        $this->step('Checking manual requirements...', ' - ');

        $this->isEnvDotExampleUpdated();
        $this->isAppVersionUpdatedOnConfigFile();
    }

    /**
     * Check if the .env.example file is updated.
     *
     * @return void
     */
    private function isEnvDotExampleUpdated(): void
    {
        $answer = $this->console->ask("Did you update .env.exmaple file? <fg=#b148aa>Last updated: " . $this->runProcess(['git', 'log', '-1', '--format=%cd', '.env.example'], true)->getOutput() . '</>', 'y|n');

        // show error if the answer is not yes or y
        if (strtolower($answer) !== 'y') {
            $this->console->error("Please update .env.example file and try again.");
            exit;
        }
    }

    /**
     * Check if the app version is updated on config file.
     *
     * @return void
     */
    private function isAppVersionUpdatedOnConfigFile(): void
    {
        $answer = $this->console->ask('Did you update martvill version on config file? <fg=#b148aa>Current version: ' . config('martvill.file_version') . '</>', 'y|n');

        if (strtolower($answer) !== 'y') {
            $this->console->error("Please update martvill version on config file and try again.");
            exit;
        }
    }
}
