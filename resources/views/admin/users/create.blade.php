@extends('admin.layouts.app')

@section('title', __('admin.add_user'))

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin.back') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('admin.add_user') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('admin.name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('admin.email') }} <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ __('admin.phone') }}</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('admin.password') }} <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('admin.password_confirmation') }} <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
                
                <div class="mb-3">
                    <label for="role" class="form-label">{{ __('admin.role') }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">{{ __('admin.select_role') }}</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('admin.admin') }}</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>{{ __('admin.user') }}</option>
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
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
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
                    <input class="form-check-input" type="checkbox" id="verified" name="verified" {{ old('verified') ? 'checked' : '' }}>
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
@endsection

@section('scripts')
<script>
jQuery(document).ready(function($) {
    
    // Country change event to load cities
    $('#country_id').on('change', function() {
        var countryId = $(this).val();
        
        // Clear cities dropdown
        $('#city_id').html('<option value="">{{ __("admin.select_city") }}</option>');
        
        if (countryId) {
            
            $.ajax({
                url: '/get-cities',
                method: 'GET',
                data: { country_id: countryId },
                dataType: 'json',
                success: function(response) {
                    
                    if (response && response.length > 0) {
                        var isArabic = '{{ app()->getLocale() }}' === 'ar';
                        
                        $.each(response, function(index, city) {
                            var cityName = isArabic ? city.name_ar : city.name_en;
                            $('#city_id').append(
                                $('<option></option>')
                                    .attr('value', city.id)
                                    .text(cityName)
                            );
                        });
                    } else {
                        $('#city_id').append('<option value="">{{ __("admin.no_cities_found") }}</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    alert('خطأ في تحميل المدن: ' + error);
                }
            });
        }
    });
    
});
</script>
@endsection
