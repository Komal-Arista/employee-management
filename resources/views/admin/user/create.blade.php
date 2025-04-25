<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Employee
            </h2>

            <a href="{{ route('admin.users.index') }}"
               class="px-3 py-2 text-sm font-medium text-white transition rounded-md bg-slate-700 hover:bg-slate-800">
                Employees List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-lg font-medium text-gray-700">
                                    Employee Name
                                </label>
                                <input id="name"
                                       type="text"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Enter Employee Name"
                                       autocomplete="off"
                                       class="w-full mt-2 border-gray-300 rounded-lg shadow-sm focus:border-slate-600 focus:ring-slate-600" />

                                @error('name')
                                    <p class="mt-1 text-sm font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label for="email" class="block text-lg font-medium text-gray-700">
                                    Employee email
                                </label>

                                <input id="email"
                                       type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="Enter Employee Email"
                                       autocomplete="off"
                                       class="w-full mt-2 border-gray-300 rounded-lg shadow-sm focus:border-slate-600 focus:ring-slate-600" />


                                @error('email')
                                    <p class="mt-1 text-sm font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="password" class="block text-lg font-medium text-gray-700">
                                    Password
                                </label>
                                <input id="password"
                                       type="password"
                                       name="password"
                                       value=""
                                       placeholder="Enter Employee Password"
                                       autocomplete="off"
                                       class="w-full mt-2 border-gray-300 rounded-lg shadow-sm focus:border-slate-600 focus:ring-slate-600" />

                                @error('password')
                                    <p class="mt-1 text-sm font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="confirm_password" class="block text-lg font-medium text-gray-700">
                                    Confirm Password
                                </label>
                                <input id="confirm_password"
                                       type="password"
                                       name="confirm_password"
                                       value=""
                                       placeholder="Enter Employee Confirm Password"
                                       autocomplete="off"
                                       class="w-full mt-2 border-gray-300 rounded-lg shadow-sm focus:border-slate-600 focus:ring-slate-600" />

                                @error('confirm_password')
                                    <p class="mt-1 text-sm font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="phone" class="block text-lg font-medium text-gray-700">
                                    Employee Phone Number
                                </label>
                                <input id="phone"
                                       type="text"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       placeholder="Enter Employee phone"
                                       autocomplete="off"
                                       class="w-full mt-2 border-gray-300 rounded-lg shadow-sm focus:border-slate-600 focus:ring-slate-600" />

                                @error('phone')
                                    <p class="mt-1 text-sm font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="joining_date" class="block text-lg font-medium text-gray-700">
                                    Employee Joining Date
                                </label>
                                <input id="joining_date"
                                       type="text"
                                       name="joining_date"
                                       value="{{ old('joining_date') }}"
                                       placeholder="Enter Employee Joining Date"
                                       autocomplete="off"
                                       class="w-full mt-2 border-gray-300 rounded-lg shadow-sm focus:border-slate-600 focus:ring-slate-600" />

                                @error('joining_date')
                                    <p class="mt-1 text-sm font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="department" class="block text-lg font-medium text-gray-700">
                                    Select Employee Department
                                </label>
                                <select id="department"
                                        name="department"
                                        class="w-full py-2 pl-3 pr-10 mt-2 bg-white border-gray-300 rounded-lg shadow-sm appearance-none focus:border-slate-600 focus:ring-slate-600">
                                    <option value="" disabled selected>Select Option</option>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>

                                @error('department')
                                    <p class="mt-1 text-sm font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="profile_photo" class="block text-lg font-medium text-gray-700">
                                    Employee Profile Photo
                                </label>
                                <input id="profile_photo"
                                       type="file"
                                       name="profile_photo"
                                       value="{{ old('profile_photo') }}"
                                       autocomplete="off"
                                       class="w-full mt-2 border-gray-300 rounded-lg shadow-sm focus:border-slate-600 focus:ring-slate-600" />

                                @error('profile_photo')
                                    <p class="mt-1 text-sm font-medium text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white rounded-md shadow-sm bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
