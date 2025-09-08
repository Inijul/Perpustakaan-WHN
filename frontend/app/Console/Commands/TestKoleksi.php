<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestKoleksi extends Command
{
    protected $signature = 'test:koleksi';
    protected $description = 'Test connection to koleksi API';

    public function handle()
    {
        $this->info('Testing connection to koleksi API...');

        try {
            $response = Http::timeout(30)->get('http://backend:5000/api/koleksi');
            
            $this->info('Status: ' . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                $this->info('Data count: ' . count($data));
                $this->info('Sample data: ' . json_encode(array_slice($data, 0, 2)));
            } else {
                $this->error('Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
        }
    }
}
