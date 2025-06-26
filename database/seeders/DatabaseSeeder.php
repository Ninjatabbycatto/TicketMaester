<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Clinic;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'firstname' => 'jobert',
            'lastname' => 'estrabilla',
            'name' => 'jobert',
            'email' => 'jobert@gmail.com',
            'password' => Hash::make('jobert'),
            'user_type' => 'admin'
        ]);

        
        Clinic::factory()->count(5)->create();
        Ticket::factory()->count(5)->create();

        User::factory()->create([
            'firstname' => 'client1',
            'lastname' => 'de la cruz',
            'name' => 'client1',
            'email' => 'client@gmail.com',
            'password' => Hash::make('client1'),
            'user_type' => 'client',
            'clinic_id' => Clinic::inRandomOrder()->value('id')
        ]);
        
        User::factory()->create([
            'firstname' => 'client2',
            'lastname' => 'de la cruz',
            'name' => 'client',
            'email' => 'client2@gmail.com',
            'password' => Hash::make('client2'),
            'user_type' => 'client',
            'clinic_id' => Clinic::inRandomOrder()->value('id')
        ]);



        Ticket::factory()->count(30)->create();

    }
}
