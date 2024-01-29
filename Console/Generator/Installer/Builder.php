<?php

namespace Modules\MartvillKit\Console\Generator\Installer;

use Illuminate\Support\Facades\Artisan;
use Modules\MartvillKit\Console\Generator\ProcessRequirements;
use Modules\MartvillKit\Console\Generator\Installer\Checks\ManualRequirements;
use Modules\MartvillKit\Console\Generator\Process;
use Modules\MartvillKit\Console\Generator\Installer\ZipCommand;

class Builder
{
    use Process;

    /**
     * The console instance.
     *
     * @var \Illuminate\Console\Command
     */
    public $console;

    /**
     * The original .env file content.
     *
     * @var string
     */
    private $originalEnvContent;

    /**
     * The installer zip file name.
     *
     * @var string
     */
    private $installerZipFileName = 'martvill-installer';

    /**
     * Build the installer.
     *
     * @param \Illuminate\Console\Command $console
     * @return void
     */
    public function build($console)
    {
        $this->console = $console;

        (new ProcessRequirements())->check();
        (new ManualRequirements())->check($this->console);

        $this->installerZipFileName = $this->console->ask('Enter the installer zip file name', $this->installerZipFileName);

        $this->step('Switching to staging branch and pulling latest code...');
        $this->switchToStagingBranch();

        $this->step('Clearing cache...');
        $this->clearCache();

        $this->step('Refreshing composer install...');
        $this->refreshComposerInstall();

        $this->step('Creating the project zip file...');
        $this->makeProjectZip();
        
        $this->line("  <question>Successfully {$this->installerZipFileName}.zip generated</question>");
    }

    /**
     * Switch to staging branch and pull latest code.
     *
     * @return void
     */
    protected function switchToStagingBranch()
    {
        $this->runProcess(['git', 'checkout', 'staging']);
        $this->runProcess(['git', 'pull']);
    }

    /**
     * Clear the cache.
     *
     * @return void
     */
    protected function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
    }

    /**
     * Refresh the composer install.
     *
     * @return void
     */
    protected function refreshComposerInstall()
    {
        $this->runProcess(['composer', 'config', '--no-plugins', 'allow-plugins.joshbrw/laravel-module-installer', 'true']);

        $this->line("   <comment>Updating modules...</comment>");
        $this->runProcess(['php', 'artisan', 'module:update']);

        $this->line("   <comment>Removing vendor folder...</comment>");
        $this->runProcess(['rm', '-rf', 'vendor']);

        $this->line("   <comment>Removing node_modules folder...</comment>");
        $this->runProcess(['rm', '-rf', 'node_modules']);

        $this->line("   <comment>Removing storage/logs/*...</comment>");
        $this->runProcess(['rm', '-rf', 'storage/logs/*']);

        $this->line("   <comment>Removing composer.lock...</comment>");
        $this->runProcess(['rm', '-rf', 'composer.lock']);
        
        $this->line("   <comment>Installing composer...</comment>");
        $this->runProcess(['composer', 'install', '--no-dev']);
    }

    /**
     * Make the project zip.
     *
     * @return void
     */
    protected function makeProjectZip()
    {
        $this->runProcess((new ZipCommand())->command($this->installerZipFileName));
    }
}