<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
	/**
	 * Seed the default admin and buyer accounts.
	 */
	public function run(): void
	{
		User::updateOrCreate(
			['email' => 'admin@gaminggear.com'],
			[
				'name' => 'Admin Gaming Gear',
				'role' => 'admin',
				'password' => Hash::make('password'),
			]
		);

		User::updateOrCreate(
			['email' => 'buyer@gaminggear.com'],
			[
				'name' => 'Buyer Gaming Gear',
				'role' => 'buyer',
				'password' => Hash::make('password'),
			]
		);
	}
}
