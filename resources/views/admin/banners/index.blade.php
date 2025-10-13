@extends('layouts.admin')

@section('title', 'Banners')
@section('page-title', 'Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Banners</h2>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        ➕ Add New Banner
    </a>
</div>

<div style="position: relative;">
    <!-- Loader -->
    <div id="loader" class="table-loader" style="display:none;">
        <div class="spinner"></div>
    </div>

    <table id="banners_table" class="display stripe hover" style="width:100%">
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Subtitle</th>
                <th>Button Text</th>
                <th>Status</th>
                <th>Created At</th>
                <th style="width:150px;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    /*----------------- AJAX Table for Banners ---------------*/
    $(document).ready(function() {
        var bannersTbl = $('#banners_table').DataTable({
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin.banners.data') }}", // ✅ route for banner data
                "type": "GET",
                "dataType": "json",
                "beforeSend": function() {
                    $('#loader').show();
                },
                "complete": function() {
                    $('#loader').hide();
                },
                "dataSrc": function(result) {
                    return result.data;
                },
                "error": function(error) {
                    console.log(error.responseText);
                }
            }
        });
    });

    /*----------------- Toggle Active Status ---------------*/
    $(document).on('change', '.toggle-status', function() {
        let bannerId = $(this).data('id');
        let isActive = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.banners.toggleStatus') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: bannerId,
                is_active: isActive
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function(xhr) {
                alert("Error updating banner status");
            }
        });
    });
</script>
@endpush
