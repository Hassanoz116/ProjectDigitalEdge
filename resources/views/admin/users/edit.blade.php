@extends('admin.layouts.app')

@section('title', __('admin.edit_user'))

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin.back') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('admin.edit_user') }}: {{ $user->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('admin.name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('admin.email') }} <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ __('admin.phone') }}</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="role" class="form-label">{{ __('admin.role') }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">{{ __('admin.select_role') }}</option>
                        <option value="admin" {{ (old('role', $user->roles->first() ? $user->roles->first()->name : '') == 'admin') ? 'selected' : '' }}>{{ __('admin.admin') }}</option>
                        <option value="user" {{ (old('role', $user->roles->first() ? $user->roles->first()->name : '') == 'user') ? 'selected' : '' }}>{{ __('admin.user') }}</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="country_id" class="form-label">{{ __('admin.country') }}</label>
                    <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id">
                        <option value="">{{ __('admin.select_country') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="city_id" class="form-label">{{ __('admin.city') }}</label>
                    <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id">
                        <option value="">{{ __('admin.select_city') }}</option>
                    </select>
                    @error('city_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="verified" name="verified" {{ old('verified', $user->email_verified_at ? true : false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="verified">
                        {{ __('admin.mark_as_verified') }}
                    </label>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>{{ __('admin.change_password') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.change-password', $user->id) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">{{ __('admin.new_password') }} <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                    @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">{{ __('admin.password_confirmation') }} <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-warning">{{ __('admin.change_password') }}</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>{{ __('admin.send_email') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.send-email', $user->id) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="subject" class="form-label">{{ __('admin.subject') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="message" class="form-label">{{ __('admin.message') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required></textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-info">{{ __('admin.send_email') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Country change event to load cities
        $('#country_id').on('change', function() {
            var countryId = $(this).val();
            console.log('Country selected:', countryId);
            
            if (countryId) {
                $.ajax({
                    url: '/get-cities',
                    type: 'GET',
                    data: { country_id: countryId },
                    success: function(data) {
                        console.log('Cities received:', data);
                        $('#city_id').empty();
                        $('#city_id').append('<option value="">{{ __("admin.select_city") }}</option>');
                        
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                var cityName = {{ app()->getLocale() == 'ar' ? 1 : 0 }} ? value.name_ar : value.name_en;
                                $('#city_id').append('<option value="' + value.id + '">' + cityName + '</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading cities:', error);
                        alert('خطأ في تحميل المدن');
                    }
                });
            } else {
                $('#city_id').empty();
                $('#city_id').append('<option value="">{{ __("admin.select_city") }}</option>');
            }
        });
        
        // Load cities if country is already selected
        var countryId = $('#country_id').val();
        if (countryId) {
            $.ajax({
                url: '/get-cities',
                type: 'GET',
                data: { country_id: countryId },
                success: function(data) {
                    $('#city_id').empty();
                    $('#city_id').append('<option value="">{{ __("admin.select_city") }}</option>');
                    var selectedCity = {{ old('city_id', $user->city_id ?? 'null') }};
                    
                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            var selected = (selectedCity == value.id) ? 'selected' : '';
                            var cityName = {{ app()->getLocale() == 'ar' ? 1 : 0 }} ? value.name_ar : value.name_en;
                            $('#city_id').append('<option value="' + value.id + '" ' + selected + '>' + cityName + '</option>');
                        });
                    }
                }
            });
        }
    });
</script>
@endsection
