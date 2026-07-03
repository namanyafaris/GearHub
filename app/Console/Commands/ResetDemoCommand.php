<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetDemoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database and seed demo data for thesis presentation (including TestSeeder)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting database reset...');

        // 1. Fresh migration
        $this->info('1. Running migrate:fresh...');
        Artisan::call('migrate:fresh', [], $this->output);

        // 2. Main DatabaseSeeder (Roles, Admin, Categories, Products)
        $this->info('2. Running DatabaseSeeder...');
        Artisan::call('db:seed', [], $this->output);

        // 3. TestSeeder (20 specific users for CF testing)
        $this->info('3. Running TestSeeder...');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\TestSeeder'], $this->output);

        // 4. Fix product images
        $this->info('4. Fixing product images to use category defaults...');
        $output = shell_exec('php fix-images.php');
        $this->line($output);

        $this->info('Demo environment successfully reset! Ready for White-Box and Black-Box testing.');

        return Command::SUCCESS;
    }
}
