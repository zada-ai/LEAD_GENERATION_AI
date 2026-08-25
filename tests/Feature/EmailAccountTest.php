<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EmailAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_email_accounts_index(): void
    {
        $user = User::factory()->create();
        EmailAccount::create([
            'user_id' => $user->id,
            'provider' => 'gmail',
            'email' => 'user@gmail.com',
            'status' => 'connected',
        ]);

        $response = $this->actingAs($user)->get(route('email-accounts.index'));

        $response->assertStatus(200);
        $response->assertSee('user@gmail.com');
    }

    public function test_user_can_view_their_email_account(): void
    {
        $user = User::factory()->create();
        $account = EmailAccount::create([
            'user_id' => $user->id,
            'provider' => 'gmail',
            'email' => 'user@gmail.com',
            'status' => 'connected',
        ]);

        $response = $this->actingAs($user)->get(route('email-accounts.show', $account));

        $response->assertStatus(200);
        $response->assertSee('user@gmail.com');
    }

    public function test_user_cannot_view_another_users_email_account(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $account = EmailAccount::create([
            'user_id' => $user2->id,
            'provider' => 'gmail',
            'email' => 'other@gmail.com',
            'status' => 'connected',
        ]);

        $response = $this->actingAs($user1)->get(route('email-accounts.show', $account));

        $response->assertStatus(403);
    }

    public function test_user_can_disconnect_email_account(): void
    {
        $user = User::factory()->create();
        $account = EmailAccount::create([
            'user_id' => $user->id,
            'provider' => 'gmail',
            'email' => 'user@gmail.com',
            'access_token' => 'dummy_token',
            'status' => 'connected',
        ]);

        $response = $this->actingAs($user)->post(route('email-accounts.disconnect', $account));

        $response->assertRedirect(route('email-accounts.index'));
        $account->refresh();
        $this->assertEquals('disconnected', $account->status);
        $this->assertNull($account->access_token);
    }
}
