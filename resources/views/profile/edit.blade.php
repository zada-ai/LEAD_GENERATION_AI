<x-app-layout :title="'Profile Settings'">
    <div class="max-w-2xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="text-gray-600 mt-1">Manage your account information</p>
        </div>

        <!-- Update Profile -->
        <x-card title="Update Profile Information">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <x-form-field
                    name="name"
                    label="Full Name"
                    placeholder="Enter your full name"
                    :value="$user->name"
                    required
                />

                <x-form-field
                    name="email"
                    label="Email Address"
                    type="email"
                    placeholder="Enter your email"
                    :value="$user->email"
                    required
                />

                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Save Changes
                    </button>
                    <x-link-button type="secondary" href="{{ route('dashboard') }}">
                        Cancel
                    </x-link-button>
                </div>
            </form>
        </x-card>

        <!-- Delete Account -->
        <x-card title="Danger Zone" class="mt-8">
            <div class="border border-red-200 rounded-lg p-6 bg-red-50">
                <h3 class="text-lg font-semibold text-red-900 mb-2">Delete Account</h3>
                <p class="text-red-800 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                
                <button type="button" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium" onclick="document.getElementById('deleteForm').classList.toggle('hidden')">
                    Delete Account
                </button>

                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteForm" class="hidden mt-6 p-6 bg-white rounded-lg border border-red-200">
                    @csrf
                    @method('DELETE')

                    <p class="text-gray-700 mb-4">Please enter your password to confirm you want to delete your account:</p>

                    <x-form-field
                        name="password"
                        label="Password"
                        type="password"
                        placeholder="Enter your password"
                        required
                    />

                    <div class="flex gap-4 mt-6">
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            Yes, Delete My Account
                        </button>
                        <button type="button" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium" onclick="document.getElementById('deleteForm').classList.toggle('hidden')">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </x-card>
    </div>
</x-app-layout>
