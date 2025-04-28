<x-app-layout>
    @push('styles')
        <!-- Data Tables CSS -->
        <link href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css" rel="stylesheet">
        <!-- Flatpickr CSS -->
        <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
        <!-- Font Awesome CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
     @endpush

     <div class="users-reports">
        <div class="create-users edit-dlt-styles">
            <div class="py-12 table-topspace table-flexible">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                    {{-- Success Message --}}
                    @if (session('success'))
                        <x-flash type="success">{{ session('success') }}</x-flash>
                    @endif

                    {{-- Error Message --}}
                    @if (session('error'))
                        <x-flash type="error">{{ session('error') }}</x-flash>
                    @endif

                    @if(auth()->user()->role == 'user')
                        <!-- If normal user is logged in, show the welcome message -->
                        <div class="px-6 mb-4">
                            <div class="flex justify-between p-4 bg-gray-100 rounded-lg shadow-sm">
                                <div>
                                    <strong>Welcome, {{ auth()->user()->name }}!</strong>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->role == 'admin')
                        <!-- If admin is logged in, show the complete details -->
                        <div class="px-6 mb-4">
                            <!-- Total counts of employees and departments -->
                            <div class="flex justify-between p-4 bg-gray-100 rounded-lg shadow-sm">
                                <div>
                                    <strong>Total Employees:</strong> {{ $totalEmployees }}
                                </div>
                                <div>
                                    <strong>Total Departments:</strong> {{ $totalDepartments }}
                                </div>
                            </div>
                        </div>

                        <div class="px-6 overflow-x-auto table-row-responsive table-responsive">
                            <div style="display:none" class="p-4 mb-3 font-medium text-green-700 bg-green-200 border-green-600 rounded-sm shadow-sm success-delete"></div>
                            <table class="w-full" id="employeesTable">
                                <thead class="bg-gray-50">
                                    <tr class="border-b">
                                        <th class="px-6 py-3 text-left">Sr. No.</th>
                                        <th class="px-6 py-3 text-left">Name</th>
                                        <th class="px-6 py-3 text-left">Email</th>
                                        <th class="px-6 py-3 text-left">Phone</th>
                                        <th class="px-6 py-3 text-left">Joining Date</th>
                                        <th class="px-6 py-3 text-left">Department</th>
                                        <th class="px-6 py-3 text-left">Profile Photo</th>
                                        <th class="px-6 py-3 text-left" width="180">Created</th>
                                        <th class="px-6 py-3 text-center" width="180">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <!-- DataTables -->
                                </tbody>
                            </table>
                        </div>
                    @endif
                </

    @push('scripts')
        <!-- Jquery JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- Data Table JS -->
        <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            // Custom Log Script
            jQuery(document).ready(function ($) {

                // Initialize Flatpickr for start_date
                const startDatePicker = flatpickr('#start_date', {
                        dateFormat: "m-d-Y", // MM-DD-YYYY format
                        allowInput: false,   // Allow manual input
                        maxDate: "today",     // Restrict future dates
                        onChange: function(selectedDates) {
                            endDatePicker.set("minDate", selectedDates[0] || null);
                        }
                    });

                const endDatePicker = flatpickr('#end_date', {
                    dateFormat: "m-d-Y", // MM-DD-YYYY format
                    allowInput: false,   // Allow manual input
                    maxDate: "today",     // Restrict future dates
                    onChange: function(selectedDates) {
                        startDatePicker.set("maxDate", selectedDates[0] || null);
                    }
                });

                // Initialize DataTable
                const table = $('#employeesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    pageLength: 5,
                    lengthChange: false,
                    ajax: {
                        url: '{{ route('admin.employees.index') }}',
                        data: function(payload) {
                            //console.log(payload);
                            payload.start_date = $('#start_date').val();
                            payload.end_date = $('#end_date').val();
                            payload.name = $('#name').val();
                        },
                    },
                    columns: [
                        {
                            data: null,
                            name: 'serial_number',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + 1 + meta.settings._iDisplayStart;
                            }
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'email',
                            name: 'email',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'joining_date',
                            name: 'joining_date',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'department',
                            name: 'department',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'profile_photo',
                            name: 'profile_photo',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                });

                // Date Change Event
                $('#start_date, #end_date').on('change', function() {
                    const start_date = $('#start_date').val();
                    const end_date = $('#end_date').val();

                    if(start_date && end_date) {
                        table.draw();
                    }
                });

                // Email filter
                $('#name').on('change input', function() {
                    const name = $('#name').val().trim();

                    if(name.length >= 2 || name.length === 0) {
                        table.draw();
                    }
                });


                // Edit Record
                $('body').on('click', '.editButton', function () {
                    var departmentId = $(this).data('id');
                    var editUrl = "{{ route('admin.employees.edit', ':id') }}".replace(':id', departmentId);
                    if(departmentId) {  window.location.href = editUrl; }
                });

                // Delete Record
                $(document).on('click', '.deleteButton', function () {
                    const id  = $(this).data('id');
                    if (!id) return;

                    if (!confirm('Are you sure you want to delete this record?')) return;

                    const url = "{{ route('admin.employees.destroy', ':id') }}".replace(':id', id);

                    $.ajax({
                        url,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'json'
                    })
                    .done(({ status, message }) => {
                        if (status === 'success') {
                            flashToast('success', message);
                            table.ajax.reload(null, false);   // keep current page
                        } else {
                            flashToast('error',  message);
                        }
                    })
                    .fail(() => flashToast('error', 'Server error, please try again.'));
                });

                // Display Message
                function flashToast(type, text) {
                    // Pick colour palette – tweak to your framework
                    const bg  = type === 'success' ? 'bg-emerald-600' : 'bg-rose-600';

                    const $toast = $(`
                        <div class="fixed top-5 right-5 z-50 ${bg} text-white
                                    px-4 py-2 rounded-lg shadow-lg">
                            ${text}
                        </div>
                    `).appendTo('body')             // add to DOM
                      .hide()                       // start invisible
                      .fadeIn(200);                 // fade in

                    // auto‑dismiss after 3 s
                    setTimeout(() => $toast.fadeOut(400, () => $toast.remove()), 3000);
                }

                 // Reset Button Functionality
                $('#reset-btn').click(function() {
                    let resetUrl = "{{ route('admin.employees.index') }}";
                    window.location.href = resetUrl;
                });

                // Enable/disable the Reset button
                const inputs = document.querySelectorAll('.filter-form input, .filter-form select');
                const resetBtn = document.getElementById('reset-btn');

                function toggleButtonState() {
                    // Check if any field has a non-empty value
                    resetBtn.disabled = !Array.from(inputs).some(input => input.value.trim() !== '');
                }

                // Attach the event listeners to inputs and selects
                inputs.forEach(input => {
                    input.addEventListener('input', toggleButtonState);
                    input.addEventListener('change', toggleButtonState); // For dropdowns and date pickers
                });

                // Trigger the button state on page load
                toggleButtonState();
            });
        </script>
    @endpush
</x-app-layout>
