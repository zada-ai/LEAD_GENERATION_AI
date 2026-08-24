<x-guest-layout :title="'Login'">
    <!-- Logo/Brand -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Lead CRM</h1>
        <p class="text-gray-600 mt-2">Automation Platform</p>
    </div>

    <!-- Login Card -->
    <x-card title="Sign In">
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

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

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 cursor-pointer" />
                    <span class="text-sm text-gray-700">Remember me</span>
                </label>
                <x-link-button type="secondary" href="{{ route('password.request') }}" class="text-sm !px-0">
                    Forgot password?
                </x-link-button>
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Sign In
            </button>

            <div class="text-center text-sm text-gray-600">
                Don't have an account?
                <x-link-button type="primary" href="{{ route('register') }}" class="!px-1">
                    Sign up here
                </x-link-button>
            </div>
        </form>
    </x-card>

    <!-- Demo Credentials -->
    <x-alert type="info" class="mt-6">
        <strong>Demo Account:</strong> Use email: <code class="font-mono">demo@example.com</code> and password: <code class="font-mono">password123</code>
    </x-alert>
</x-guest-layout>
