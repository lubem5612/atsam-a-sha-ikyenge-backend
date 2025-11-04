<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use function Nette\Utils\bytesToChars;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::query()->where('role', 'admin')->doesntExist()) {
            User::query()->updateOrCreate([
                'email' => 'admin@nkstchurch.org',
                'role' => 'admin',
            ], [
                'name' => 'Atsam Admin',
                'phone' => '+2348038602189',
                'password' => bcrypt('password'),
                'email_verified_at' => Carbon::now()
            ]);
        }
    }
}
