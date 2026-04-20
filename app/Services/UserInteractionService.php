<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInteraction;

class UserInteractionService
{
	/**
	 * Store a user interaction with standardized weight.
	 */
	public function log(User $user, int $productId, string $type): void
	{
		if (! $user->isBuyer()) {
			return;
		}

		$weight = match ($type) {
			'view' => 1.0,
			'cart' => 2.0,
			'purchase' => 3.0,
			default => 1.0,
		};

		UserInteraction::create([
			'user_id' => $user->id,
			'product_id' => $productId,
			'interaction_type' => $type,
			'weight' => $weight,
		]);
	}
}
