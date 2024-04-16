<?php

namespace App\Console\Commands;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categories = Category::where('time', '<=', Carbon::now())->where('synchronized', 0)->get();
        
        foreach ($categories as $category) {
            $response = Http::post('http://project1.test/api/categories', [
                'uuid' => $category->uuid,
                'name' => $category->name,
                'time' => $category->time,
                'synchronized' => 1,
            ]);

            if ($response->successful()) {
                
                $category->synchronized = true;
                $category->save();
                
                $this->info("Category {$category->name} pushed successfully.");
            } else {
                $this->error("Failed to push category {$category->name}.");
            }
        }
    }
}
