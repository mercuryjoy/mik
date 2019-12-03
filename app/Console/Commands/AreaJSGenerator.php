<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class AreaJSGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'areajs:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $areasJson = File::get(base_path() . "/database/seeds/area.json");
        $areas = json_decode($areasJson, true);

        $rootAreas = [];
        $areaTrees = [];

        foreach($areas as $area) {
            if (isset($area['parent_id'])) {
                $tree = isset($areaTrees[$area['parent_id']]) ? $areaTrees[$area['parent_id']] : [];
                $tree[] = ["id" => $area["id"], "name" => $area["name"]];
                $areaTrees[$area['parent_id']] = $tree;
            } else {
                $rootAreas[] = ["id" => $area["id"], "name" => $area["name"]];
            }
        }

        $rootAreasJson = json_encode($rootAreas);
        $areaTreesJson = json_encode($areaTrees);

        $stub = File::get($this->getStub());
        $stub = str_replace("%%ROOT_AREA%%", $rootAreasJson, $stub);
        $stub = str_replace("%%AREA_TREE%%", $areaTreesJson, $stub);

        $path = $this->getPath();
        $this->makeDirectory($path);
        File::put($path, $stub);

        $this->info('area.js generated successfully.');
    }

    protected function getStub()
    {
        return __DIR__.'/stubs/area.js.stub';
    }

    protected function getPath() {
        return $this->laravel['path']. '/../resources/assets/javascript/area.js';
    }

    protected function makeDirectory($path)
    {
        if (! File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0777, true, true);
        }
    }
}
