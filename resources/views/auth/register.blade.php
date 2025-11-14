<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() == 'ar' ? 'إنشاء حساب' : 'Register' }} - Digital Edge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" }};
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            margin: 20px auto;
        }
        .register-header {
            background: linear-gradient(135deg, #424242ff 0%, #4b65a2ff 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .register-body {
            padding: 30px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #424242ff 0%, #4b65a2ff 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .password-requirements li {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="register-header">
                <h2 class="mb-0">
                    <i class="fas fa-user-plus {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                    {{ app()->getLocale() == 'ar' ? 'إنشاء حساب جديد' : 'Create Account' }}
                </h2>
                <p class="mb-0 mt-2">
                    {{ app()->getLocale() == 'ar' ? 'انضم إلى منصة Digital Edge' : 'Join Digital Edge Platform' }}
                </p>
            </div>
            <div class="register-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-user text-primary"></i> 
                            {{ app()->getLocale() == 'ar' ? 'الاسم الكامل' : 'Full Name' }} 
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل اسمك الكامل' : 'Enter your full name' }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope text-primary"></i> 
                            {{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}"
                               placeholder="{{ app()->getLocale() == 'ar' ? 'أدخل بريدك الإلكتروني' : 'Enter your email address' }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                       
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone text-primary"></i> 
                            {{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}
                        </label>
                        <div class="input-group">
                            <select class="form-select" id="phone_code" name="phone_code" style="max-width: 150px;">
                                <option value="">{{ app()->getLocale() == 'ar' ? 'اختر الرمز' : 'Select Code' }}</option>
                                @foreach($countries as $country)
                                    @if($country->phone_code)
                                    <option value="{{ $country->phone_code }}" 
                                            data-country-id="{{ $country->id }}"
                                            {{ old('phone_code') == $country->phone_code ? 'selected' : '' }}>
                                        {{ $country->phone_code }} {{ $country->code }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                   placeholder="{{ app()->getLocale() == 'ar' ? '123456789' : '123456789' }}">
                        </div>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      
                    </div>

                    <!-- Country -->
                    <div class="mb-3">
                        <label for="country_id" class="form-label">
                            <i class="fas fa-globe text-primary"></i> Country <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('country_id') is-invalid @enderror" 
                                id="country_id" name="country_id" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" 
                                        data-phone-code="{{ $country->phone_code }}"
                                        {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="mb-3">
                        <label for="city_id" class="form-label">
                            <i class="fas fa-city text-primary"></i> City <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('city_id') is-invalid @enderror" 
                                id="city_id" name="city_id" required>
                            <option value="">Select City</option>
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock text-primary"></i> Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <ul class="password-requirements mt-2">
                            <li><i class="fas fa-check-circle text-success"></i> At least 8 characters</li>
                            <li><i class="fas fa-check-circle text-success"></i> One uppercase letter</li>
                            <li><i class="fas fa-check-circle text-success"></i> One lowercase letter</li>
                            <li><i class="fas fa-check-circle text-success"></i> One number</li>
                            <li><i class="fas fa-check-circle text-success"></i> One special character (@$!%*?&)</li>
                        </ul>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock text-primary"></i> Confirm Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-register w-100">
                        <i class="fas fa-user-plus {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                        {{ app()->getLocale() == 'ar' ? 'إنشاء الحساب' : 'Create Account' }}
                    </button>

                    <!-- Login Link -->
                    <div class="text-center mt-3">
                        <p class="mb-0">
                            {{ app()->getLocale() == 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }} 
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-sign-in-alt"></i> 
                                {{ app()->getLocale() == 'ar' ? 'سجل دخولك هنا' : 'Login here' }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Auto-fill phone code when country is selected
            $('#country_id').on('change', function() {
                var countryId = $(this).val();
                var phoneCode = $(this).find('option:selected').data('phone-code');
                var citySelect = $('#city_id');
                
                // Set phone code
                if (phoneCode) {
                    $('#phone_code').val(phoneCode);
                }
                
                // Load cities
                citySelect.html('<option value="">Loading...</option>');
                
                if (countryId) {
                    $.ajax({
                        url: '/get-cities',
                        type: 'GET',
                        data: { country_id: countryId },
                        success: function(data) {
                            citySelect.html('<option value="">Select City</option>');
                            if (data && data.length > 0) {
                                $.each(data, function(key, city) {
                                    // Use name_en as fallback
                                    var cityName = city.name_en || city.name || 'City ' + city.id;
                                    citySelect.append('<option value="' + city.id + '">' + cityName + '</option>');
                                });
                            } else {
                                citySelect.html('<option value="">No cities available</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading cities:', error);
                            citySelect.html('<option value="">Error loading cities</option>');
                        }
                    });
                } else {
                    citySelect.html('<option value="">Select City</option>');
                }
            });

            // Trigger on page load if country is selected
            @if(old('country_id'))
                $('#country_id').trigger('change');
                setTimeout(function() {
                    $('#city_id').val('{{ old('city_id') }}');
                }, 500);
            @endif

        });
    </script>
</body>
</html>
