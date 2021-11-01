<?php

namespace App\Console\Commands;

use App\Models\Doodle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DoodleCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doodle:crawler';

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
        $time = Carbon::createFromFormat('Y-m', '1998-01');

        do {
            $month = $time->format('n');
            $year = $time->format('Y');

            echo "Start: " . $year . "-" . $month . "\n";

            $response = Http::get('https://www.google.com/doodles/json/'. $year .'/' . $month . '?hl=en');
            $json = $response->json();
            foreach ($json as $doodle) {
                $obj = new Doodle();
                $obj->name = $doodle['name'];
                $obj->title = $doodle['title'];
                $obj->url = $doodle['url'];
                $obj->alternate_url = $doodle['alternate_url'];
                $obj->high_res_url = $doodle['high_res_url'];
                $obj->high_res_width = $doodle['high_res_width'];
                $obj->high_res_height = $doodle['high_res_height'];
                $obj->run_date = $doodle['run_date_array'][0] . '-' . $doodle['run_date_array'][1]  . '-' . $doodle['run_date_array'][2];
                $obj->translations = json_encode($doodle['translations']);
                $obj->query = $doodle['query'];
                $obj->share_text = $doodle['share_text'];
                $obj->save();
            }

            $time = $time->addMonth();
            sleep(2);
        } while (Carbon::now()->greaterThanOrEqualTo($time));

        return Command::SUCCESS;
    }
}
