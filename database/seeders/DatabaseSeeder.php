<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Factories\HostFactory;
use Database\Factories\ShowFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Dev',
            'email' => 'dev@example.com',
            'password' => bcrypt('dev'),
        ]);

        $show = ShowFactory::new()->for($user)->create([
            'name' => 'Dev Show',
            'description' => 'Dev Show Description',
        ]);

        $host = HostFactory::new()->for($user)->for($show)->create([
            'name' => 'Dev Host',
        ]);

        $user->update(['current_host_id' => $host->id]);
    }
}
