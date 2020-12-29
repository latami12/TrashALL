<?php

use App\User;
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
        $password = Hash::make('password');

        $admin = User::create([
                'name' => 'Latami',
                'email' => 'ppib14latami@gmail.com',
                'email_verified_at' => now(),
                'password' => $password,
                'phone' => '082233413048',
                'foto' => 'https://via.placeholder.com/150/000000/FFFFFF/?text=User',
                'role_id' => 5
        ]);

        $bendahara = User::create([
                'name' => 'Bendahara',
                'email' => 'bendahara@gmail.com',
                'email_verified_at' => now(),
                'password' => $password,
                'phone' => '081282951055',
                'foto' => 'https://via.placeholder.com/150/000000/FFFFFF/?text=User/',
                'role_id' => 4
        ]);

        $pengurus1 = User::create([
                'name' => 'Pengurus1',
                'email' => 'pengurus1@gmail.com',
                'email_verified_at' => now(),
                'password' => $password,
                'phone' => '081345670922',
                'foto' => 'https://via.placeholder.com/150/000000/FFFFFF/?text=User/',
                'role_id' => 2
        ]);

        $pengurus2 = User::create([
                'name' => 'Pengurus2',
                'email' => 'pengurus2@gmail.com',
                'email_verified_at' => now(),
                'password' => $password,
                'phone' => '082245670891',
                'foto' => 'https://via.placeholder.com/150/000000/FFFFFF/?text=User/',
                'role_id' => 3
        ]);

        
    }
}
