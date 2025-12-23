<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculatePercentages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:percentages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate all percentages for tasks, moashermkmfs, mubadaras, moasheradastrategies, and hadafstrategies';

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
        $this->info('Starting percentage calculations...');
        
        // Calculate regular percentages
        $this->info('Calculating regular percentages...');
        calculatePercentages();
        $this->info('✓ Regular percentages calculated successfully.');
   
        
        return 0;
    }
}
