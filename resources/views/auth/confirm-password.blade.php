<x-guest-layout :title="'Confirm Password'">
    <!-- Logo/Brand -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Lead CRM</h1>
        <p class="text-gray-600 mt-2">Automation Platform</p>
    </div>

    <!-- Confirm Password Card -->
    <x-card title="Confirm Your Password">
        <p class="text-gray-600 mb-6">For your security, please confirm your password before continuing.</p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
            @csrf

            <x-form-field
                name="password"
                label="Password"
                type="password"
                placeholder="••••••••"
                required
            />

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Confirm
            </button>
        </form>
    </x-card>
</x-guest-layout>
