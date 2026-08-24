<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Dental Clinic',
            'Real Estate',
            'Restaurant',
            'Salon',
            'Gym',
            'E-commerce',
            'Travel Agency',
            'Software Company',
            'Consulting',
            'Retail Store',
        ];

        $services = [
            'AI Automation',
            'AI Chatbot',
            'Website Development',
            'CRM Development',
            'App Development',
            'SEO',
        ];

        $sources = [
            'Website',
            'Facebook',
            'Instagram',
            'LinkedIn',
            'Google',
            'Manual',
            'Import',
            'Other',
        ];

        $statuses = [
            'new',
            'contacted',
            'interested',
            'follow_up',
            'converted',
            'not_interested',
        ];

        $priorities = ['low', 'medium', 'high'];

        return [
            'business_name' => $this->faker->company() . ' India',
            'contact_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'facebook_url' => 'https://facebook.com/' . $this->faker->userName(),
            'instagram_url' => 'https://instagram.com/' . $this->faker->userName(),
            'linkedin_url' => 'https://linkedin.com/in/' . $this->faker->userName(),
            'country' => 'India',
            'state' => $this->faker->state(),
            'city' => $this->faker->city(),
            'area' => $this->faker->streetAddress(),
            'category' => $this->faker->randomElement($categories),
            'service_interest' => $this->faker->randomElement($services),
            'source' => $this->faker->randomElement($sources),
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
            'notes' => $this->faker->paragraph(),
            'last_contacted_at' => $this->faker->optional(0.7)->dateTimeThisMonth(),
            'next_follow_up_at' => $this->faker->optional(0.5)->dateTimeBetween('now', '+30 days'),
        ];
    }
}
