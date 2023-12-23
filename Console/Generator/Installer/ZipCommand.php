<?php

namespace Modules\MartvillKit\Console\Generator\Installer;

use Modules\MartvillKit\Console\Generator\Process;

class ZipCommand
{
    use Process;

    /**
     * The 7zip command.
     *
     * @var string
     */
    public function command($zipFileName, $sourceDir = '*') : array
    {
        return array_merge(['7z', 'a', '-tzip', '-r', '-bsp1', $zipFileName, $sourceDir], $this->shouldExclude());
    }

    /**
     * The files to exclude.
     *
     * @return array
     */
    public function shouldExclude() : array
    {
        $excludedFiles = [
            '.git',
            'node_modules',
            'public/uploads',
            'public/contents',
            'storage/logs',
            'design',
            '.idea',
            '.vscode',
            '.history',
            'nbproject',
            '.vagrant',
            'Homestead.json',
            'Homestead.yaml',
            'npm-debug.log',
            'yarn-error.log',
            'robots.txt',
            'terminate',
            '.phpunit.result.cache',
            '.rnd',
            'tests',
            'database/seeds/EmailConfigurationsTableSeeder.php'
        ];

        // Exclude the extra modules, which are not in the remote repository.
        $excludedFiles = array_merge($excludedFiles, $this->getLocalChangesFiles());

        $this->line('  <info>Excluded files or directories:</info> ');

        // add '-xr!' of each excluded file and then return the array
        return array_map(function ($file) {
            $this->line('  <comment>-</comment> ' . $file);
            return '-xr!' . $file;
        }, $excludedFiles);
    }

    /**
     * Exclude the extra modules, which are not in the remote repository.
     * 
     * @param string $filePath
     *
     * @return array
     */
    private function getLocalModulesPath($filePath = 'Modules/modules.json'): array
    {
        // Prepare the git show command
        $pathCommand = sprintf('HEAD:%s', $filePath);

        // Create a new Process instance
        $process = $this->runProcess(['git', 'show',  $pathCommand], true);

        // Run the command
        $process->run();

        // Get the output
        if ($process->isSuccessful()) {
            $gitRemoteModulesJson = $process->getOutput();
        } else {
            echo $process->getErrorOutput();
            return [];
        }

        $gitRemoteModules = json_decode($gitRemoteModulesJson, true);
        $localProjectModules = json_decode(file_get_contents($filePath), true);

        // Extract keys that exist in $localProjectModules but not in $gitRemoteModules
        $differentKeysInFile2 = array_diff_key($localProjectModules, $gitRemoteModules);

        // get $differentKeysInFile2 keys only, not values
        return array_keys($differentKeysInFile2);
    }

    /**
     * Exclude the extra modules, which are not in the remote repository.
     * 
     * @return array
     */
    private function getLocalChangesFiles(): array
    {
        $process = $this->runProcess(['git', 'status', '--porcelain'], true);

        $modifiedFiles = [];

        if ($process->isSuccessful()) {
            $gitDiffFiles = $process->getOutput();
            $lines = explode("\n", $gitDiffFiles);

            foreach ($lines as $line) {
                // Trim the line to remove extra spaces or characters
                $line = trim($line);

                // Check for empty lines or composer.lock
                if (empty($line) || strpos($line, 'composer.lock') !== false) {
                    continue;
                }

                // Use regex to extract file paths efficiently
                preg_match('/^(?:\s*M|\?\?)\s*(.*)/', $line, $matches);

                // Check if a match was found
                if (!empty($matches[1])) {
                    $modifiedFiles[] = rtrim($matches[1], '/');
                }
            }
        } else {
            echo $process->getErrorOutput();
        }

        return $modifiedFiles;
    }


}