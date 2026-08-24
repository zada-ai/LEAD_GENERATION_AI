<x-guest-layout :title="'Register'">
    <!-- Logo/Brand -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Lead CRM</h1>
        <p class="text-gray-600 mt-2">Automation Platform</p>
    </div>

    <!-- Register Card -->
    <x-card title="Create Account">
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <x-form-field
                name="name"
                label="Full Name"
                placeholder="John Doe"
                :value="old('name')"
                required
            />

            <x-form-field
                name="email"
                label="Email Address"
                type="email"
                placeholder="you@example.com"
                :value="old('email')"
                required
            />

            <x-form-field
                name="password"
                label="Password"
                type="password"
                placeholder="••••••••"
                required
            />

            <x-form-field
                name="password_confirmation"
                label="Confirm Password"
                type="password"
                placeholder="••••••••"
                required
            />

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Create Account
            </button>

            <div class="text-center text-sm text-gray-600">
                Already have an account?
                <x-link-button type="primary" href="{{ route('login') }}" class="!px-1">
                    Sign in here
                </x-link-button>
            </div>
        </form>
    </x-card>
</x-guest-layout>
