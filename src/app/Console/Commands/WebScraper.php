<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class WebScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:web-scraper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run web scrapping scripts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define the path to your Python script
        $scriptPaths = [
            base_path('webnews_scrapper.py'),
            base_path('bbcnews_scrapper.py'),
            base_path('theguardian_scrapper.py'),
        ];

        foreach ($scriptPaths as $scriptPath) {
            $this->runPythonScript($scriptPath);
        }
        $this->info('Data scraping completed successfully.');
    }

    protected function runPythonScript($scriptPath)
    {
        $process = new Process(['python3', $scriptPath]);

        $process->setTimeout(null);

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if ($process->isSuccessful()) {
            $this->info('Python script executed successfully: ' . $scriptPath);
        } else {
            $this->error('Python script encountered an error: ' . $scriptPath);
        }
    }
}
