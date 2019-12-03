<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tremby\LaravelGitVersion\GitVersionHelper;

class VersionFileGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a version file into base path';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = VersionFileGenerator::versionFile();
        if (file_exists($file)) {
            unlink($file);
        }
        file_put_contents($file, GitVersionHelper::getVersion());
    }

    private static function versionFile()
    {
        return base_path() . '/version';
    }
}
