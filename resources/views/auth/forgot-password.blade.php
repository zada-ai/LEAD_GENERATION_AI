<x-guest-layout :title="'Forgot Password'">
    <!-- Logo/Brand -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Lead CRM</h1>
        <p class="text-gray-600 mt-2">Automation Platform</p>
    </div>

    <!-- Password Reset Card -->
    <x-card title="Forgot Your Password?">
        <p class="text-gray-600 mb-6">No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>

        @if (session('status'))
            <x-alert type="success" class="mb-6">
                {{ session('status') }}
            </x-alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <x-form-field
                name="email"
                label="Email Address"
                type="email"
                placeholder="you@example.com"
                :value="old('email')"
                required
            />

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Email Password Reset Link
            </button>

            <div class="text-center text-sm text-gray-600">
                <x-link-button type="secondary" href="{{ route('login') }}" class="!px-1">
                    Back to login
                </x-link-button>
            </div>
        </form>
    </x-card>
</x-guest-layout>
