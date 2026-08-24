<x-guest-layout :title="'Verify Email'">
    <!-- Logo/Brand -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Lead CRM</h1>
        <p class="text-gray-600 mt-2">Automation Platform</p>
    </div>

    <!-- Verify Email Card -->
    <x-card title="Verify Email Address">
        <p class="text-gray-600 mb-6">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>

        @if (session('status') == 'verification-link-sent')
            <x-alert type="success" class="mb-6">
                A new verification link has been sent to the email address you provided during registration.
            </x-alert>
        @endif

        <div class="flex gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Log Out
                </button>
            </form>
        </div>
    </x-card>
</x-guest-layout>
