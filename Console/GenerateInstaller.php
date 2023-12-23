<?php

namespace Modules\MartvillKit\Console;

use Illuminate\Console\Command;
use Modules\MartvillKit\Console\Generator\Installer\Builder as InstallerBuilder;

class GenerateInstaller extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'martvill:generate-installer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make the installer zip';

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle()
    {
        try {
            (new InstallerBuilder())->build($this->output);
        } catch (\Exception $e) {
            // 
        }
    }
}
