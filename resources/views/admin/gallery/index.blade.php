@extends('admin.layouts.app')

@section('title', __('admin.gallery_management'))

@section('actions')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="fas fa-upload"></i> {{ __('admin.upload_image') }}
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('admin.gallery') }}</h5>
        </div>
        <div class="card-body">
            @if(count($images) > 0)
                <div class="row">
                    @foreach($images as $image)
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card">
                                <img src="{{ asset('storage/' . $image['path']) }}" class="card-img-top" alt="{{ $image['product_title'] }}" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $image['product_title'] }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            {{ $image['type'] == 'primary' ? __('admin.primary_image') : __('admin.other_image') }}
                                        </small>
                                    </p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.products.show', $image['product_id']) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('admin.view_product') }}
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $image['id'] }}">
                                            <i class="fas fa-trash"></i> {{ __('admin.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $image['id'] }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $image['id'] }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $image['id'] }}">{{ __('admin.confirm_delete') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            {{ __('admin.delete_image_confirm') }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                                            <form action="{{ route('admin.gallery.destroy', $image['id']) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">{{ __('admin.delete') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    {{ __('admin.no_images_found') }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.gallery.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">{{ __('admin.upload_image') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">{{ __('admin.select_product') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">{{ __('admin.select_product') }}</option>
                                @foreach(\App\Models\Product::all() as $product)
                                    <option value="{{ $product->id }}">
                                        {{ app()->getLocale() == 'ar' ? $product->title_ar : $product->title_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">{{ __('admin.image') }} <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            <div class="form-text">{{ __('admin.max_file_size', ['size' => '2MB']) }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('admin.upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
