<?php

namespace Pterodactyl\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoSuspension extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:billing:auto-suspension';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically checks if there are servers that have to be suspended.';

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
        $servers = DB::table('servers')->get();

         foreach ($servers as $server) {
              if ($server->suspended !== 1) {
                   if ($server->renewal_date < date("Y-m-d h:m:s")) {
                      DB::table('servers')->where('id', '=', $server->id)->update([
                        'suspended' => 1
                      ]);
                   }
              }
         }
    }
}
