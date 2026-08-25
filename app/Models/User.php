<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the leads created by this user.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    /**
     * Get the email accounts for this user.
     */
    public function emailAccounts(): HasMany
    {
        return $this->hasMany(EmailAccount::class);
    }

    /**
     * Get lead sources for this user.
     */
    public function leadSources(): HasMany
    {
        return $this->hasMany(LeadSource::class);
    }

    /**
     * Get campaigns for this user.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get follow ups for this user.
     */
    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get lead suppressions for this user.
     */
    public function suppressions(): HasMany
    {
        return $this->hasMany(LeadSuppression::class);
    }

    /**
     * Get activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get user settings.
     */
    public function setting(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get or create user settings.
     */
    public function getOrCreateSetting(): UserSetting
    {
        return $this->setting()->firstOrCreate([
            'user_id' => $this->id,
        ], [
            'gemini_model' => 'gemini-2.5-flash',
            'enable_ai_analysis' => true,
            'enable_ai_personalization' => true,
            'default_daily_limit' => 50,
            'default_follow_up_days' => 3,
            'default_approval_required' => true,
        ]);
    }
}
