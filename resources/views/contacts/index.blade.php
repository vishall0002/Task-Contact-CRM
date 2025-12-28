@extends('layouts.app')

@section('title', 'Contacts')

@push('styles')
    <style>
        body {
            background: #f3f6fb;
        }

        .container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
        }

        table thead {
            background: #1e3c72;
            color: #fff;
        }

        table tbody tr:hover {
            background: #eef3ff;
        }

        .btn-sm {
            padding: 4px 10px;
        }

        .badge {
            font-size: 12px;
            padding: 6px 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-4">
{{-- 
        <div class="d-flex justify-content-between mb-3">
            <h4>Contacts</h4>

            <div>
                <button class="btn btn-warning" id="mergeBtn" disabled>
                    Merge Contacts
                </button>




                <button class="btn btn-danger" id="deleteSelected">Delete</button>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                    + Add Contact
                </button>
            </div>

        </div> --}}


        <!-- 🔍 Search Section (TOP) -->
        <div class="mb-3">
            <h4>Contacts</h4>

            <form id="searchForm" class="row g-2 mt-2">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="name" placeholder="Search Name">
                </div>

                <div class="col-md-3">
                    <input type="text" class="form-control" name="email" placeholder="Search Email">
                </div>

                <div class="col-md-3">
                    <input type="text" class="form-control" name="phone" placeholder="Search Phone">
                </div>

                <div class="col-md-3">
                    <input type="text" class="form-control" name="gender" placeholder="Search Gender">
                </div>
            </form>
        </div>

        <!-- 🔘 Action Buttons (BELOW) -->
        <div class="d-flex justify-content-end mb-3 gap-2">
            <button class="btn btn-warning" id="mergeBtn" disabled>
                Merge Contacts
            </button>

            <button class="btn btn-danger" id="deleteSelected">
                Delete
            </button>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                + Add Contact
            </button>
        </div>



        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email(s)</th>
                    <th>Phone(s)</th>
                    <th>profile</th>
                    <th>Gender</th>
                    <th>Custom Fields</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="contactTable">
                @foreach ($contacts as $key => $contact)
                    {{-- MASTER CONTACT ROW --}}
                    <tr>
                        <td>
                            @if (!$contact->is_merged)
                                <input type="checkbox" class="row-check" value="{{ $contact->id }}">
                            @endif
                        </td>
                        <td>{{ $key + 1 }}</td>
                        <td><strong>{{ $contact->name }}</strong></td>

                        <td>
                            @foreach (explode(',', $contact->email ?? '') as $email)
                                <div>{{ trim($email) }}</div>
                            @endforeach
                        </td>

                        <td>
                            @foreach (explode(',', $contact->phone ?? '') as $phone)
                                <div>{{ trim($phone) }}</div>
                            @endforeach
                        </td>

                         <td>
                            @if($contact->profile_image)
                                <img src="{{ asset('storage/' . $contact->profile_image) }}"
                                    alt="Profile Image"
                                    width="60"
                                    height="60"
                                    style="object-fit: cover; border-radius: 50%;">
                            @else
                                N/A
                            @endif
                        </td>


                        <td>{{ ucfirst($contact->gender) }}</td>

                        <td>
                           @foreach ($contact->customFields as $field)
                                @if($field->customField)
                                    <div>
                                        <strong>{{ $field->customField->label }}:</strong>
                                        {{ $field->field_value }}
                                    </div>
                                @endif
                            @endforeach

                        </td>

                        <td>
                            @if ($contact->mergedContacts->count())
                                <span class="badge bg-warning text-dark">Master</span>
                            @else
                                Active
                            @endif
                        </td>

                        <td>
                            <button class="btn btn-sm btn-info editBtn" data-id="{{ $contact->id }}"
                                data-name="{{ $contact->name }}" data-email="{{ $contact->email }}"
                                data-phone="{{ $contact->phone }}" data-gender="{{ $contact->gender }}"  data-image="{{ $contact->profile_image }}">
                                Edit
                            </button>
                        </td>

                        <td>
                            <button class="btn btn-sm btn-secondary viewBtn" data-id="{{ $contact->id }}">
                                View
                            </button>
                        </td>
                    </tr>

                    {{-- 🔽 MERGED SECONDARY CONTACTS --}}
                    @foreach ($contact->mergedContacts as $merge)
                        @php $secondary = $merge->secondaryContact; @endphp

                        @if ($secondary)
                            <tr style="background:#fff3cd">
                                <td></td>
                                <td></td>
                                <td>
                                    <strong>{{ $secondary->name }}</strong><br>
                                    <small class="text-muted">(Merged)</small>
                                </td>

                                <td>
                                    @foreach (explode(',', $secondary->email ?? '') as $email)
                                        <div>{{ trim($email) }}</div>
                                    @endforeach
                                </td>

                                <td>
                                    @foreach (explode(',', $secondary->phone ?? '') as $phone)
                                        <div>{{ trim($phone) }}</div>
                                    @endforeach
                                </td>

                                 <td>
                                    @if($secondary->profile_image)
                                        <img src="{{ asset('storage/' . $secondary->profile_image) }}"
                                            alt="Profile Image"
                                            width="60"
                                            height="60"
                                            style="object-fit: cover; border-radius: 50%;">
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td>{{ ucfirst($secondary->gender) }}</td>

                                <td>
                                    @foreach ($secondary->customFields as $field)
                                        @if ($field->customField)
                                            <div>
                                                <strong>{{ $field->customField->label }}:</strong>
                                                {{ $field->field_value }}
                                            </div>
                                        @endif
                                    @endforeach
                                </td>


                                <td colspan="3">
                                    <span class="badge bg-warning text-dark">
                                        Merged Contact
                                    </span>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>

        </table>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Add Contact</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="contactForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="contact_id">

                        <div class="row">
                            <div class="col-md-6">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>

                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" id="email">
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" pattern="\d{10}"
                                    title="Enter 10 digit phone number">
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Gender</label>
                                <select class="form-control" name="gender" id="gender" required>
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            {{-- <div class="col-md-6 mt-2">
                                <label>Profile Image</label>
                                <input type="file" class="form-control" name="profile_image" id="profile_image"
                                    accept="image/*">
                            </div> --}}
                            <div class="col-md-6 mt-2">
                                <label>Profile Image</label>
                                <input type="file" class="form-control" name="profile_image" id="profile_image" accept="image/*">

                                <!-- Image preview -->
                                <div id="profilePreview" class="mt-2"></div>
                            </div>


                            <div class="col-md-6 mt-2">
                                <label>Additional File</label>
                                <input type="file" class="form-control" name="additional_file" id="additional_file"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                            </div>
                        </div>

                        {{-- Dynamic Custom Fields --}}
                        <div class="row mt-3" id="customFieldsWrapper">
                            @foreach ($customFields as $field)
                                <div class="col-md-4 mb-3">
                                    <label>{{ $field->label }}</label>

                                    @if ($field->type === 'text')
                                        <input type="text" class="form-control custom-field"
                                            name="custom_fields[{{ $field->id }}]" data-field-id="{{ $field->id }}">
                                    @elseif($field->type === 'number')
                                        <input type="number" class="form-control custom-field"
                                            name="custom_fields[{{ $field->id }}]" data-field-id="{{ $field->id }}">
                                    @elseif($field->type === 'email')
                                        <input type="email" class="form-control custom-field"
                                            name="custom_fields[{{ $field->id }}]" data-field-id="{{ $field->id }}">
                                    @elseif($field->type === 'date')
                                        <input type="date" class="form-control custom-field"
                                            name="custom_fields[{{ $field->id }}]"
                                            data-field-id="{{ $field->id }}">
                                    @elseif($field->type === 'textarea')
                                        <textarea class="form-control custom-field" name="custom_fields[{{ $field->id }}]"
                                            data-field-id="{{ $field->id }}"></textarea>
                                    @elseif($field->type === 'file')
                                        <input type="file" class="form-control custom-field"
                                            name="custom_fields[{{ $field->id }}]"
                                            data-field-id="{{ $field->id }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <button class="btn btn-success mt-3" type="submit">
                            Save Contact
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- second model for custom field data visibility  --}}
    <div class="modal fade" id="viewContactModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Contact Details ( custom fields )</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="viewContactBody">
                    <!-- Data loads here -->
                </div>
            </div>
        </div>
    </div>


    {{--  model for merge  --}}

    <div class="modal fade" id="mergeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST" action="{{ route('contacts.merge.preview') }}">


                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Select Master Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" id="mergeOptions">
                        <!-- JS injects content -->
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Continue
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    </div>

@endsection

@push('scripts')
    <script>
        $('.viewBtn').on('click', function() {
            let contactId = $(this).data('id');

            $.get("{{ url('/contacts/custom-fields') }}/" + contactId, function(data) {

                let html = `<div class="row">`;

                data.forEach(item => {
                    html += `
                <div class="col-md-6 mb-3">
                    <label><strong>${item.custom_field.label}</strong></label>
                    <div class="form-control bg-light">${item.field_value ?? '-'}</div>
                </div>
            `;
                });

                html += `</div>`;

                $('#viewContactBody').html(html);
                $('#viewContactModal').modal('show');
            });
        });
    </script>
    <script>
        // Select all
        $('#selectAll').on('change', function() {
            $('.row-check').prop('checked', this.checked);
        });

        // Open edit modal
            $(document).on('click', '.editBtn', function () {
            $('#modalTitle').text('Update Contact');

            const contactId = $(this).data('id');

            $('#contact_id').val(contactId);
            $('#name').val($(this).data('name'));
            $('#email').val($(this).data('email'));
            $('#phone').val($(this).data('phone'));
            $('#gender').val($(this).data('gender'));

            // Clear old values
            $('.custom-field').val('');
            $('#profilePreview').html('');

            // Show existing profile image
            let image = $(this).data('image');
            if (image) {
                $('#profilePreview').html(`
                    <img src="/storage/${image}"
                        width="80"
                        height="80"
                        style="object-fit:cover;border-radius:50%;">
                `);
            }

            // 🔥 FETCH CUSTOM FIELD VALUES
            $.get("{{ url('/contacts/custom-fields') }}/" + contactId, function (res) {

                res.forEach(item => {
                    const fieldName = `custom_fields[${item.custom_field_id}]`;
                    const input = $(`[name="${fieldName}"]`);

                    if (input.length) {
                        input.val(item.field_value);
                    }
                });
            });

            $('#contactModal').modal('show');
        });




        // Save / Update
        $('#contactForm').submit(function(e) {
            e.preventDefault();

            let url = $('#contact_id').val() ?
                "{{ route('contacts.update') }}" :
                "{{ route('contacts.store') }}";

            // Use FormData to include files
            let formData = new FormData(this);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false, // important to handle files
                contentType: false, // important to handle files
                success: function(response) {
                    // reset form
                    $('#contactForm')[0].reset();
                    $('#contact_id').val('');
                    $('.custom-field').val('');

                    // close modal
                    $('#contactModal').modal('hide');

                    // reload list
                    location.reload();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });



        // Delete
        $('#deleteSelected').click(function() {
            let ids = $('.row-check:checked').map(function() {
                return $(this).val();
            }).get();

            if (!ids.length) {
                alert('Select at least one record');
                return;
            }

            if (!confirm('Are you sure?')) return;

            $.post("{{ route('contacts.delete') }}", {
                _token: "{{ csrf_token() }}",
                ids: ids
            }, function() {
                location.reload();
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const mergeBtn = document.getElementById('mergeBtn');
            const checkboxes = document.querySelectorAll('.row-check');
            const mergeOptions = document.getElementById('mergeOptions');

            function getSelected() {
                return Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
            }

            function toggleMergeBtn() {
                mergeBtn.disabled = getSelected().length !== 2;
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', toggleMergeBtn);
            });

            mergeBtn.addEventListener('click', function() {

                const selected = getSelected();
                let html = '';

                selected.forEach((id, index) => {

                    const checkbox = document.querySelector('.row-check[value="' + id + '"]');
                    const row = checkbox.closest('tr');

                    const name = row.children[2].innerText.trim();
                    const email = row.children[3].innerHTML.trim();
                    const phone = row.children[4].innerHTML.trim();

                    html += `
        <div class="form-check mb-3 p-3 border rounded">
            <input class="form-check-input" type="radio"
                name="master_id" value="${id}" ${index === 0 ? 'checked' : ''}>

            <label class="form-check-label w-100">
                <strong>${name}</strong><br>
                <small class="text-muted">Email:</small>
                <div>${email}</div>

                <small class="text-muted">Phone:</small>
                <div>${phone}</div>
            </label>

            <input type="hidden" name="contact_ids[]" value="${id}">
        </div>
    `;
                });


                mergeOptions.innerHTML = html;

                const modal = new bootstrap.Modal(document.getElementById('mergeModal'));
                modal.show();
            });

        });



    $('#searchForm input').on('keyup', function () {
        let formData = $('#searchForm').serialize();

        $.ajax({
            url: "{{ route('contacts.index') }}",
            type: "GET",
            data: formData,
            success: function (response) {

                // Replace only table body
                let html = $(response).find('#contactTable').html();
                $('#contactTable').html(html);

                // Re-bind events after DOM update
                $('.editBtn').off().on('click', function () {
                    $('#modalTitle').text('Update Contact');
                    $('#contact_id').val($(this).data('id'));
                    $('#name').val($(this).data('name'));
                    $('#email').val($(this).data('email'));
                    $('#phone').val($(this).data('phone'));
                    $('#gender').val($(this).data('gender'));
                    $('#contactModal').modal('show');
                });
            }
        });
    });


    // model clear 
    $('#contactModal').on('hidden.bs.modal', function () {

        // Reset form
        $('#contactForm')[0].reset();

        // Reset hidden ID
        $('#contact_id').val('');

        // Clear custom fields
        $('.custom-field').val('');

        // Reset title
        $('#modalTitle').text('Add Contact');

        // Remove image preview if exists
        $('#profilePreview').remove();
    });

    </script>
@endpush


