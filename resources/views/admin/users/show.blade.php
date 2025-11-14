@extends('admin.layouts.app')

@section('title', __('admin.user_details'))

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin.back') }}
    </a>
    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> {{ __('admin.edit') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('admin.user_details') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">{{ __('admin.id') }}</th>
                            <td>{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.name') }}</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.email') }}</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.phone') }}</th>
                            <td>{{ $user->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.role') }}</th>
                            <td>
                                @if($user->roles->first())
                                    <span class="badge bg-primary">{{ $user->roles->first()->name }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.verified') }}</th>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">{{ __('admin.yes') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $user->email_verified_at->format('Y-m-d H:i:s') }}</small>
                                @else
                                    <span class="badge bg-danger">{{ __('admin.no') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.country') }}</th>
                            <td>
                                @if($user->country)
                                    {{ app()->getLocale() == 'ar' ? $user->country->name_ar : $user->country->name_en }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.city') }}</th>
                            <td>
                                @if($user->city)
                                    {{ app()->getLocale() == 'ar' ? $user->city->name_ar : $user->city->name_en }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.created_at') }}</th>
                            <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.updated_at') }}</th>
                            <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('admin.user_products') }} ({{ $user->products->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($user->products->count() > 0)
                        <div class="list-group">
                            @foreach($user->products as $product)
                                <a href="{{ route('admin.products.show', $product->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ app()->getLocale() == 'ar' ? $product->title_ar : $product->title_en }}</h6>
                                        <small>${{ number_format($product->price, 2) }}</small>
                                    </div>
                                    <p class="mb-1 text-muted">
                                        <small>{{ $product->created_at->format('Y-m-d') }}</small>
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __('admin.no_products_assigned') }}
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5>{{ __('admin.statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $user->products->count() }}</h3>
                                    <p class="mb-0">{{ __('admin.total_products') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-success">${{ number_format($user->products->sum('price'), 2) }}</h3>
                                    <p class="mb-0">{{ __('admin.total_value') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
