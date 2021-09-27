<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $randomPass = str_random(8);
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make($randomPass),
        ]);

        $this->command->alert('Your Random Password is generated.');
        $this->command->info('Your credential is: ');
        $this->command->info('Login: Admin@admin.com');
        $this->command->info('Password: ' .$randomPass);
        $this->command->alert('Please do not forget to copy and note the password.!');
    }
}
