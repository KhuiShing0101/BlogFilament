<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Post::factory(50)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $adminUser = User::factory()->create(
            [
                "email" => "superadmin@gmail.com",
                "name"  => "Admin",
                "password" => bcrypt('password'), 
            ]
            );
        $adminRole = Role::create(['name'=>'admin']);
        $adminUser->assignRole($adminRole);
    }
}
