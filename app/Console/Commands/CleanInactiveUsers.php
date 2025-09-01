<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;

class CleanInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:clean-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark users as offline if they have been inactive for more than 10 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inactiveUsers = Usuario::where('is_online', true)
            ->where('last_seen', '<', now()->subMinutes(10))
            ->get();

        foreach ($inactiveUsers as $user) {
            $user->setOnlineStatus(false);
        }

        $count = $inactiveUsers->count();
        
        $this->info("Marked {$count} users as offline due to inactivity.");
        
        return Command::SUCCESS;
    }
}
