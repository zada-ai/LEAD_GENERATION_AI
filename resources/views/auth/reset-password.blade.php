<x-guest-layout :title="'Reset Password'">
    <!-- Logo/Brand -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Lead CRM</h1>
        <p class="text-gray-600 mt-2">Automation Platform</p>
    </div>

    <!-- Reset Password Card -->
    <x-card title="Reset Password">
        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}" />

            <x-form-field
                name="email"
                label="Email Address"
                type="email"
                placeholder="you@example.com"
                :value="old('email', $request->email)"
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
                Reset Password
            </button>

            <div class="text-center text-sm text-gray-600">
                <x-link-button type="secondary" href="{{ route('login') }}" class="!px-1">
                    Back to login
                </x-link-button>
            </div>
        </form>
    </x-card>
</x-guest-layout>
