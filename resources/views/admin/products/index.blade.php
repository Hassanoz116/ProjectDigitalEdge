@extends('admin.layouts.app')

@section('title', __('admin.product_management'))

@section('actions')
    <div class="dropdown d-inline-block ms-2">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-download"></i> {{ __('admin.export') }}
        </button>
        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
            <li><a class="dropdown-item" href="{{ route('admin.users.export', ['format' => 'xlsx']) }}">{{ __('admin.export_excel') }}</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.users.export', ['format' => 'csv']) }}">{{ __('admin.export_csv') }}</a></li>
        </ul>
    </div>
     <div class="btn-group me-2" role="group">
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> {{ __('admin.add_product') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('admin.products') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="productsTable">
                    <thead>
                        <tr>
                            <th>{{ __('admin.id') }}</th>
                            <th>{{ __('admin.image') }}</th>
                            <th>{{ __('admin.title_en') }}</th>
                            <th>{{ __('admin.title_ar') }}</th>
                            <th>{{ __('admin.price') }}</th>
                            <th>{{ __('admin.assigned_to') }}</th>
                            <th>{{ __('admin.created_at') }}</th>
                            <th>{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @if($product->primary_image)
                                        <img src="{{ asset('storage/' . $product->primary_image) }}" alt="{{ $product->title_en }}" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <span class="badge bg-secondary">{{ __('admin.no_image') }}</span>
                                    @endif
                                </td>
                                <td>{{ $product->title_en }}</td>
                                <td>{{ $product->title_ar }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->user ? $product->user->name : '-' }}</td>
                                <td>{{ $product->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $product->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $product->id }}">{{ __('admin.confirm_delete') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ __('admin.delete_product_confirm', ['title' => $product->title_en]) }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">{{ __('admin.delete') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#productsTable').DataTable({
            "paging": false,
            "info": false,
            "searching": true,
            "language": {
                "url": "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json' : '//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json' }}"
            }
        });
    });
</script>
@endsection
