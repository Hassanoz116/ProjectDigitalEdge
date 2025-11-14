@extends('admin.layouts.app')

@section('title', __('admin.product_details'))

@section('actions')
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin.back') }}
    </a>
    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> {{ __('admin.edit') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('admin.product_details') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">{{ __('admin.id') }}</th>
                            <td>{{ $product->id }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.title_en') }}</th>
                            <td>{{ $product->title_en }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.title_ar') }}</th>
                            <td>{{ $product->title_ar }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.description_en') }}</th>
                            <td>{{ $product->description_en ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.description_ar') }}</th>
                            <td>{{ $product->description_ar ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.price') }}</th>
                            <td>${{ number_format($product->price, 2) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.slug') }}</th>
                            <td>{{ $product->slug }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.assigned_to') }}</th>
                            <td>{{ $product->user ? $product->user->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.created_at') }}</th>
                            <td>{{ $product->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('admin.updated_at') }}</th>
                            <td>{{ $product->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('admin.primary_image') }}</h5>
                </div>
                <div class="card-body text-center">
                    @if($product->primary_image)
                        <img src="{{ asset('storage/' . $product->primary_image) }}" alt="{{ $product->title_en }}" class="img-fluid">
                    @else
                        <p class="text-muted">{{ __('admin.no_image') }}</p>
                    @endif
                </div>
            </div>
            
            @if($product->other_images)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>{{ __('admin.other_images') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach(json_decode($product->other_images, true) as $image)
                                <div class="col-6 mb-2">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->title_en }}" class="img-thumbnail">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
