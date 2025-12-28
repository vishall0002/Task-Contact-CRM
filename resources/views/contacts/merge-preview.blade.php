@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Merge Preview</h4>

    <form id="mergeConfirmForm" action="javascript:void(0);">
        @csrf

        <input type="hidden" name="master_id" value="{{ $master->id }}">
        <input type="hidden" name="secondary_id" value="{{ $secondary->id }}">

        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th width="30%">Field</th>
                    <th width="35%">Master</th>
                    <th width="35%">Secondary</th>
                </tr>
            </thead>

            <tbody>
                {{-- Core Fields --}}
                <tr>
                    <td><strong>Name</strong></td>
                    <td>{{ $master->name ?? '—' }}</td>
                    <td>{{ $secondary->name ?? '—' }}</td>
                </tr>

                <tr>
                    <td><strong>Email</strong></td>
                    <td>{{ $master->email ?? '—' }}</td>
                    <td>{{ $secondary->email ?? '—' }}</td>
                </tr>

                <tr>
                    <td><strong>Phone</strong></td>
                    <td>{{ $master->phone ?? '—' }}</td>
                    <td>{{ $secondary->phone ?? '—' }}</td>
                </tr>

                {{-- Custom Fields --}}
                @if($customFields->count())
                    <tr class="table-secondary">
                        <th colspan="3">Custom Fields</th>
                    </tr>

                    @foreach($customFields as $field)
                        <tr>
                            <td><strong>{{ $field['label'] }}</strong></td>
                            <td>{{ $field['master'] ?? '—' }}</td>
                            <td>{{ $field['secondary'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        {{-- SINGLE CHECKBOX --}}
        @if($customFields->count())
            <div class="form-check mt-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="merge_custom_fields"
                    value="1"
                    id="mergeCustomFields"
                    checked
                >
                <label class="form-check-label fw-bold" for="mergeCustomFields">
                    Copy / merge custom fields from primary contact
                </label>
            </div>
        @endif

        <div class="mt-3">
            <button type="submit" class="btn btn-danger">
                Confirm Merge
            </button>

            <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection



@push('scripts')
<script>
$(document).ready(function () {

    $('#mergeConfirmForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let submitBtn = form.find('button[type="submit"]');

        submitBtn.prop('disabled', true).text('Merging...');

        $.ajax({
            url: "{{ route('contacts.merge.confirm') }}",
            type: "POST",
            data: form.serialize(),
            success: function (res) {

                if (res.status) {
                    alert(res.message);
                    window.location.href = "{{ route('contacts.index') }}";
                } else {
                    alert(res.message || 'Merge failed');
                }

                submitBtn.prop('disabled', false).text('Confirm Merge');
            },
            error: function (xhr) {

                let msg = 'Something went wrong';

                // Validation errors (422)
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors)
                        .map(err => err.join(', '))
                        .join('\n');
                }
                // Business / logic errors
                else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }

                alert(msg);

                submitBtn.prop('disabled', false).text('Confirm Merge');
            }
        });
    });

});
</script>
@endpush
