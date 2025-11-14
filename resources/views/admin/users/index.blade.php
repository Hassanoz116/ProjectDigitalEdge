@extends('admin.layouts.app')

@section('title', __('admin.user_management'))

@section('actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin.add_user') }}
    </a>
    <div class="dropdown d-inline-block ms-2">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-download"></i> {{ __('admin.export') }}
        </button>
        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
            <li><a class="dropdown-item" href="{{ route('admin.users.export', ['format' => 'xlsx']) }}">{{ __('admin.export_excel') }}</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.users.export', ['format' => 'csv']) }}">{{ __('admin.export_csv') }}</a></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('admin.users') }}</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="role" class="form-label">{{ __('admin.role') }}</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">{{ __('admin.all') }}</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('admin.admin') }}</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>{{ __('admin.user') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="country_id" class="form-label">{{ __('admin.country') }}</label>
                        <select class="form-select" id="country_id" name="country_id">
                            <option value="">{{ __('admin.all') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="city_id" class="form-label">{{ __('admin.city') }}</label>
                        <select class="form-select" id="city_id" name="city_id">
                            <option value="">{{ __('admin.all') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'ar' ? $city->name_ar : $city->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="verified" class="form-label">{{ __('admin.verified') }}</label>
                        <select class="form-select" id="verified" name="verified">
                            <option value="">{{ __('admin.all') }}</option>
                            <option value="1" {{ request('verified') == '1' ? 'selected' : '' }}>{{ __('admin.yes') }}</option>
                            <option value="0" {{ request('verified') == '0' ? 'selected' : '' }}>{{ __('admin.no') }}</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">{{ __('admin.filter') }}</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('admin.reset') }}</a>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>{{ __('admin.id') }}</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.email') }}</th>
                            <th>{{ __('admin.phone') }}</th>
                            <th>{{ __('admin.role') }}</th>
                            <th>{{ __('admin.country') }}</th>
                            <th>{{ __('admin.city') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.verified') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Yajra DataTables
        var table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.users.index") }}',
                data: function(d) {
                    d.role = $('#role').val();
                    d.country_id = $('#country_id').val();
                    d.city_id = $('#city_id').val();
                    d.verified = $('#verified').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone', orderable: false },
                { data: 'role', name: 'role', orderable: false },
                { data: 'country', name: 'country', orderable: false },
                { data: 'city', name: 'city', orderable: false },
                { data: 'status', name: 'status', orderable: false },
                { data: 'verified', name: 'verified', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            language: {
                url: "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json' : '//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json' }}"
            },
            pageLength: 25,
            responsive: true
        });
        
        // Filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            table.draw();
        });
        
        // Country change event to load cities
        $('#country_id').change(function() {
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: '{{ route("admin.get-cities") }}',
                    type: 'GET',
                    data: { country_id: countryId },
                    success: function(data) {
                        $('#city_id').empty();
                        $('#city_id').append('<option value="">{{ __("admin.all") }}</option>');
                        $.each(data, function(key, value) {
                            $('#city_id').append('<option value="' + value.id + '">' + 
                                ({{ app()->getLocale() == 'ar' ? 'true' : 'false' }} ? value.name_ar : value.name_en) + 
                            '</option>');
                        });
                    }
                });
            } else {
                $('#city_id').empty();
                $('#city_id').append('<option value="">{{ __("admin.all") }}</option>');
            }
        });
    });
</script>
@endsection
