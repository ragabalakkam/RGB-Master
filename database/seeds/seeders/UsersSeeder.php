<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

# models
use App\Models\User;

class UsersSeeder extends Seeder
{
  public static function run($COUNT = 4)
  {
    if (User::all()->count()) return;
    
    $users = [
      [
        'name'      => 'Master',
        'role'      => 'employee',
        'password'  => 'PASS#WORD',
      ],
      [
        'name'      => 'Eng. Ragab Al-Akkam',
        'role'      => 'employee',
        'email'     => "ragabalakkam@gmail.com",
        'password'  => 'PASS#WORD',
      ],
      [
        'name'      => 'Tareq Ashraf',
        'role'      => 'employee',
        'email'     => "ta1232@fayoum.edu.eg",
        'password'  => 'PASS#WORD',
      ],
      [
        'name'      => 'Mina Alfy',
        'role'      => 'employee',
        'email'     => "minaalfy8@gmail.com",
        'password'  => 'PASS#WORD',
      ],
    ];

    foreach ($users as $user)
    {
      $username = str_replace(' ', '', strtolower($user['name']));
      User::create([
        'name'              => $user['name'],
        'username'          => $username,
        'email'             => $user['email'] ?? "$username@rgbksa.com",
        'email_verified_at' => now(),
        'phone'             => $user['phone'] ?? null,
        'phone_verified_at' => now(),
        'password'          => Hash::make($user['password'] ?? 'passw&rd'),
        'role'              => $user['role'] ?? 'client',
        'created_at'        => now(),
        'updated_at'        => now(),
      ]);
      
      $COUNT--;
    }

    # more users using user factory
    User::factory()->count($COUNT)->create();
  }
}
