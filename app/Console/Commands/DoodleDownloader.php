<?php

namespace App\Console\Commands;

use App\Models\Doodle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DoodleDownloader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doodle:downloader';

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
     * @return int
     */
    public function handle()
    {
        $data = Doodle::orderBy('run_date')->get();
        $count = [];
        foreach ($data as $row) {
            $url = 'https:' . $row->url;
            $extension = preg_replace("#\?.*#", "", pathinfo($url, PATHINFO_EXTENSION));

            if (empty($count[$row->run_date])) $count[$row->run_date] = 1;
            $name = $row->run_date . '_' . $count[$row->run_date] . '.' . $extension;

            $contents = file_get_contents($url);
            Storage::put($name, $contents);
            $count[$row->run_date] += 1;
            echo $name . "\n";
            usleep(300000);
        }

        return Command::SUCCESS;
    }
}
