<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard_with_stats(): void
    {
        $user = User::factory()->create();

        Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'new',
        ]);

        Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'contacted',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Total Leads');
        $response->assertViewHas('stats');
        $response->assertViewHas('recentLeads');
    }
}
