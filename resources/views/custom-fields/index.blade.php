@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        .container {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
        }

        h4 {
            font-weight: 600;
            color: #2c3e50;
        }

        /* Table Styling */
        table {
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background-color: #1e3c72;
            color: #fff;
        }

        thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
        }

        tbody tr {
            transition: all 0.2s ease-in-out;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fc;
        }

        tbody tr:hover {
            background-color: #eaf1ff;
        }

        td,
        th {
            vertical-align: middle;
        }

        /* Buttons */
        .btn {
            border-radius: 6px;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-warning {
            background-color: #f0ad4e;
            color: #fff;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-primary {
            background-color: #1e88e5;
            border: none;
        }

        /* Badges */
        .badge {
            font-size: 0.85rem;
            padding: 6px 10px;
            border-radius: 12px;
        }

        /* Checkbox alignment */
        input[type="checkbox"] {
            transform: scale(1.2);
            cursor: pointer;
        }

        /* Modal */
        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #1e3c72;
            color: white;
        }

        /* edit button  */
        .btn-outline-primary:hover {
            background-color: #1e88e5;
            color: #fff;
        }
    </style>
@endpush


@section('content')
    <div class="container-fluid mt-5">

        <div class="d-flex justify-content-between mb-3">
            <h4>Custom Fields</h4>
            <div class="d-flex gap-2">
                <button class="btn btn-success" id="activateSelected">Activate</button>
                <button class="btn btn-warning" id="deactivateSelected">Deactivate</button>
                <button class="btn btn-danger" id="deleteSelected">Delete</button>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customFieldModal" id="addFieldBtn">
                    + Add Custom Field
                </button>

            </div>

        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>SNO</th>
                    <th>Label</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Placeholder</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody id="fieldTable">
                @foreach ($fields as $key => $field)
                    <tr>
                        <td>
                            <input type="checkbox" class="row-check" value="{{ $field->id }}">
                        </td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $field->label }}</td>
                        <td>{{ $field->name }}</td>
                        <td>{{ $field->type }}</td>
                        <td>{{ $field->placeholder ?? '' }}</td>
                        <td>
                            @if ($field->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary editBtn d-flex align-items-center gap-1"
                                data-id="{{ $field->id }}" data-label="{{ $field->label }}"
                                data-name="{{ $field->name }}" data-type="{{ $field->type }}"
                                data-placeholder="{{ $field->placeholder }}" data-status="{{ $field->status }}">
                                ✏️ Edit
                            </button>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="customFieldModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Custom Field</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- <div class="modal-body">
                    <form id="customFieldForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label>Label</label>
                                <input class="form-control" name="label" required>
                            </div>
                            <div class="col-md-3">
                                <label>Name</label>
                                <input class="form-control" name="name" required>
                            </div>
                            <div class="col-md-3">
    <label>Type</label>
    <select class="form-control" name="type" required>
        <option value="text">Text</option>
        <option value="number">Number</option>
        <option value="email">Email</option>
        <option value="date">Date</option>
        <option value="textarea">Textarea</option>
        <option value="file">File</option>
    </select>
</div>

                            <div class="col-md-3">
                                <label>Placeholder</label>
                                <input class="form-control" name="placeholder">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">
                            Save Field
                        </button>
                    </form>

                    <div id="formMsg" class="mt-3"></div>
                </div> --}}
                <div class="modal-body">
                    <form id="customFieldForm">
                        @csrf
                        <input type="hidden" name="id" id="field_id">

                        <div class="row">
                            <div class="col-md-3">
                                <label>Label</label>
                                <input class="form-control" name="label" id="label" required>
                            </div>

                            <div class="col-md-3">
                                <label>Name</label>
                                <input class="form-control" name="name" id="name" required>
                            </div>

                            <div class="col-md-3">
    <label>Type</label>
    <select class="form-control" name="type" required>
        <option value="text">Text</option>
        <option value="number">Number</option>
        <option value="email">Email</option>
        <option value="date">Date</option>
        <option value="textarea">Textarea</option>
        <option value="file">File</option>
    </select>
</div>

                            <div class="col-md-3">
                                <label>Placeholder</label>
                                <input class="form-control" name="placeholder" id="placeholder">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success mt-3" id="saveBtn">
                            Save Field
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#customFieldForm').submit(function(e) {
                e.preventDefault();

                let form = $(this);
                let url = "{{ route('custom.fields.store') }}";

                $.ajax({
                    url: url,
                    method: "POST",
                    data: form.serialize(),
                    success: function(res) {
                        $('#formMsg').html(
                            `<div class="alert alert-success">${res.message}</div>`);

                        $('#fieldTable').append(`
                    <tr>
                        <td>${res.data.id}</td>
                        <td>${res.data.label}</td>
                        <td>${res.data.name}</td>
                        <td>${res.data.type}</td>
                        <td>${res.data.placeholder ?? ''}</td>
                    </tr>
                `);

                        form[0].reset();
                        $('#customFieldModal').modal('hide'); // close modal
                    },
                    error: function(xhr) {
                        let html = '<div class="alert alert-danger"><ul>';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                html += `<li>${value[0]}</li>`;
                            });
                        } else {
                            html += `<li>Something went wrong</li>`;
                        }
                        html += '</ul></div>';
                        $('#formMsg').html(html);
                    }
                });
            });
        });
    </script>


    <script>
        // Select all
        $('#selectAll').on('change', function() {
            $('.row-check').prop('checked', this.checked);
        });

        // Get selected IDs
        function getSelectedIds() {
            return $('.row-check:checked').map(function() {
                return $(this).val();
            }).get();
        }

        // STATUS CHANGE
        $('#activateSelected, #deactivateSelected').on('click', function() {
            let status = $(this).attr('id') === 'activateSelected' ? 'active' : 'inactive';
            let ids = getSelectedIds();

            if (ids.length === 0) {
                alert('Please select at least one record');
                return;
            }

            $.ajax({
                url: "{{ route('custom.fields.status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids,
                    status: status
                },
                success: function() {
                    location.reload();
                }
            });
        });

        // DELETE
        $('#deleteSelected').on('click', function() {
            let ids = getSelectedIds();

            if (ids.length === 0) {
                alert('Please select at least one record');
                return;
            }

            if (!confirm('Are you sure you want to delete selected records?')) return;

            $.ajax({
                url: "{{ route('custom.fields.delete') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function() {
                    location.reload();
                }
            });
        });
    </script>

    <script>
        // ADD MODE
        $('#addFieldBtn').on('click', function() {
            $('#modalTitle').text('Add Custom Field');
            $('#field_id').val('');
            $('#customFieldForm')[0].reset();
        });



        // Open edit modal and fill values
        $(document).on('click', '.editBtn', function() {

            $('#modalTitle').text('Update Custom Field');

            $('#field_id').val($(this).data('id'));
            $('#label').val($(this).data('label'));
            $('#name').val($(this).data('name'));
            $('#type').val($(this).data('type'));
            $('#placeholder').val($(this).data('placeholder'));

            $('#customFieldModal').modal('show');
        });

        // Submit (Create / Update)
        $('#customFieldForm').submit(function(e) {
            e.preventDefault();

            let id = $('#field_id').val();
            let url = id ?
                "{{ route('custom.fields.update') }}" :
                "{{ route('custom.fields.store') }}";

            $.ajax({
                url: url,
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Something went wrong!');
                }
            });
        });
    </script>
@endpush
