<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_settings_without_secret_leakage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertSee('AI Settings');
        $response->assertSee('User Profile');
        $response->assertDontSee('sk-');
        $response->assertDontSee('AIza');
    }

    public function test_user_can_update_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('settings.update'), [
            'name' => 'Updated User Name',
            'gemini_model' => 'gemini-2.5-pro',
            'enable_ai_analysis' => 1,
            'enable_ai_personalization' => 1,
            'default_daily_limit' => 100,
            'default_follow_up_days' => 5,
            'default_approval_required' => 1,
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('Updated User Name', $user->name);

        $setting = $user->setting;
        $this->assertEquals('gemini-2.5-pro', $setting->gemini_model);
        $this->assertEquals(100, $setting->default_daily_limit);
        $this->assertEquals(5, $setting->default_follow_up_days);
    }
}
