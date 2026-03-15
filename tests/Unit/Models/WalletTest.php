<?php

namespace Tests\Unit\Models;

use App\Models\Cook;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function wallet_has_correct_state_constants(): void
    {
        $this->assertEquals(1, Wallet::LIMITED);
        $this->assertEquals(2, Wallet::FULL);
        $this->assertEquals(3, Wallet::BLOCKED);
    }

    #[Test]
    public function wallet_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $wallet = Wallet::create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::LIMITED,
        ]);

        $this->assertInstanceOf(User::class, $wallet->user);
        $this->assertEquals($user->uuid, $wallet->user->uuid);
    }

    #[Test]
    public function wallet_is_accessed_through_user(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $wallet = Wallet::create([
            'user_uuid' => $user->uuid,
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::LIMITED,
        ]);

        // Wallet is accessed through User, not directly from Cook
        $this->assertInstanceOf(Wallet::class, $user->wallet);
        $this->assertEquals($wallet->uuid, $user->wallet->uuid);
    }

    #[Test]
    public function wallet_uses_uuid_as_primary_key(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $wallet = Wallet::create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::LIMITED,
        ]);

        $this->assertNotNull($wallet->uuid);
        $this->assertIsString($wallet->uuid);
        $this->assertEquals(36, strlen($wallet->uuid));
    }

    #[Test]
    public function wallet_tracks_available_balance(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $wallet = Wallet::create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'total_available' => 100.50,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::FULL,
        ]);

        $this->assertEquals(100.50, $wallet->total_available);
    }

    #[Test]
    public function wallet_tracks_processing_balance(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $wallet = Wallet::create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'total_available' => 0,
            'total_processing' => 50.00,
            'total_paid' => 0,
            'state' => Wallet::LIMITED,
        ]);

        $this->assertEquals(50.00, $wallet->total_processing);
    }

    #[Test]
    public function wallet_tracks_total_paid(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $wallet = Wallet::create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 500.00,
            'state' => Wallet::FULL,
        ]);

        $this->assertEquals(500.00, $wallet->total_paid);
    }

    #[Test]
    public function wallet_can_be_in_different_states(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $walletLimited = Wallet::create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::LIMITED,
        ]);
        $this->assertEquals(Wallet::LIMITED, $walletLimited->state);

        $walletLimited->update(['state' => Wallet::FULL]);
        $this->assertEquals(Wallet::FULL, $walletLimited->fresh()->state);

        $walletLimited->update(['state' => Wallet::BLOCKED]);
        $this->assertEquals(Wallet::BLOCKED, $walletLimited->fresh()->state);
    }
}
