<x-app-layout :title="'Edit Lead'">
    <div class="max-w-4xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Lead</h1>
            <p class="text-gray-600 mt-1">Update the lead information below</p>
        </div>

        <!-- Form -->
        <x-card>
            <form method="POST" action="{{ route('leads.update', $lead) }}" class="space-y-8">
                @csrf
                @method('PATCH')

                <!-- Business Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-4 border-b">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-field
                            name="business_name"
                            label="Business Name"
                            placeholder="Enter business name"
                            :value="$lead->business_name"
                            required
                        />
                        <x-form-field
                            name="category"
                            label="Category"
                            type="select"
                            required
                        >
                            @php
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
                                    'Other',
                                ];
                            @endphp
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ $lead->category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </x-form-field>
                        <x-form-field
                            name="website"
                            label="Website"
                            type="url"
                            placeholder="https://example.com"
                            :value="$lead->website"
                        />
                        <x-form-field
                            name="facebook_url"
                            label="Facebook URL"
                            type="url"
                            placeholder="https://facebook.com/..."
                            :value="$lead->facebook_url"
                        />
                        <x-form-field
                            name="instagram_url"
                            label="Instagram URL"
                            type="url"
                            placeholder="https://instagram.com/..."
                            :value="$lead->instagram_url"
                        />
                        <x-form-field
                            name="linkedin_url"
                            label="LinkedIn URL"
                            type="url"
                            placeholder="https://linkedin.com/..."
                            :value="$lead->linkedin_url"
                        />
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-4 border-b">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-field
                            name="contact_name"
                            label="Contact Name"
                            placeholder="Enter contact name"
                            :value="$lead->contact_name"
                            required
                        />
                        <x-form-field
                            name="email"
                            label="Email Address"
                            type="email"
                            placeholder="example@email.com"
                            :value="$lead->email"
                            required
                        />
                        <x-form-field
                            name="phone"
                            label="Phone Number"
                            placeholder="+91 XXXXX XXXXX"
                            :value="$lead->phone"
                        />
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-4 border-b">Location</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-field
                            name="country"
                            label="Country"
                            placeholder="India"
                            :value="$lead->country"
                            required
                        />
                        <x-form-field
                            name="state"
                            label="State/Province"
                            placeholder="e.g., Maharashtra"
                            :value="$lead->state"
                        />
                        <x-form-field
                            name="city"
                            label="City"
                            placeholder="e.g., Mumbai"
                            :value="$lead->city"
                        />
                        <x-form-field
                            name="area"
                            label="Area/Address"
                            placeholder="e.g., Bandra"
                            :value="$lead->area"
                        />
                    </div>
                </div>

                <!-- Sales Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-4 border-b">Sales Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-field
                            name="service_interest"
                            label="Service Interest"
                            type="select"
                            required
                        >
                            @php
                                $services = [
                                    'AI Automation',
                                    'AI Chatbot',
                                    'Website Development',
                                    'CRM Development',
                                    'App Development',
                                    'SEO',
                                    'Other',
                                ];
                            @endphp
                            @foreach ($services as $service)
                                <option value="{{ $service }}" {{ $lead->service_interest === $service ? 'selected' : '' }}>{{ $service }}</option>
                            @endforeach
                        </x-form-field>
                        <x-form-field
                            name="source"
                            label="Lead Source"
                            type="select"
                            required
                        >
                            @php
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
                            @endphp
                            @foreach ($sources as $source)
                                <option value="{{ $source }}" {{ $lead->source === $source ? 'selected' : '' }}>{{ $source }}</option>
                            @endforeach
                        </x-form-field>
                        <x-form-field
                            name="status"
                            label="Status"
                            type="select"
                            required
                        >
                            @php
                                $statuses = [
                                    'new' => 'New',
                                    'contacted' => 'Contacted',
                                    'interested' => 'Interested',
                                    'follow_up' => 'Follow-up',
                                    'converted' => 'Converted',
                                    'not_interested' => 'Not Interested',
                                    'do_not_contact' => 'Do Not Contact',
                                ];
                            @endphp
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" {{ $lead->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-form-field>
                        <x-form-field
                            name="priority"
                            label="Priority"
                            type="select"
                            required
                        >
                            @php
                                $priorities = [
                                    'low' => 'Low',
                                    'medium' => 'Medium',
                                    'high' => 'High',
                                ];
                            @endphp
                            @foreach ($priorities as $key => $label)
                                <option value="{{ $key }}" {{ $lead->priority === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-form-field>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-4 border-b">Additional Information</h3>
                    <x-form-field
                        name="notes"
                        label="Notes"
                        type="textarea"
                        placeholder="Add any additional notes or comments about this lead..."
                        :value="$lead->notes"
                    />
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Update Lead
                    </button>
                    <x-link-button type="secondary" href="{{ route('leads.show', $lead) }}">
                        Cancel
                    </x-link-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
