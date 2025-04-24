<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Department
            </h2>

            <a href="{{ route('admin.departments.index') }}"
               class="bg-slate-700 hover:bg-slate-800 transition text-sm font-medium rounded-md px-3 py-2 text-white">
                Departments List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Success Message --}}
                @if (session('success'))
                    <x-flash type="success">{{ session('success') }}</x-flash>
                @endif

                {{-- Error Message --}}
                @if (session('error'))
                    <x-flash type="error">{{ session('error') }}</x-flash>
                @endif

                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.departments.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-lg font-medium text-gray-700">
                                    Department Name
                                </label>
                                <input id="name"
                                       type="text"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Enter Department Name"
                                       autocomplete="off"
                                       class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-slate-600 focus:ring-slate-600" />

                                @error('name')
                                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label for="status" class="block text-lg font-medium text-gray-700">
                                    Department Status
                                </label>
                                <select id="status"
                                        name="status"
                                        class="mt-2 w-full appearance-none rounded-lg border-gray-300 bg-white
                                               pr-10 pl-3 py-2 shadow-sm focus:border-slate-600 focus:ring-slate-600">
                                    <option value="" disabled selected>Select Option</option>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>

                                @error('status')
                                    <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-slate-700
                                       px-6 py-3 text-sm font-medium text-white shadow-sm
                                       hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
