<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Database\Factories\UserFactory;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        // $user = UserFactory::new()->create();
        $users = UserFactory::new()->count(80)->create();
    }
}