@extends('admin.layouts.app')

@section('title', __('admin.activity_log_management'))

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('admin.activity_logs') }}</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="user_id" class="form-label">{{ __('admin.user') }}</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">{{ __('admin.all') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="action" class="form-label">{{ __('admin.action') }}</label>
                        <input type="text" class="form-control" id="action" name="action" value="{{ request('action') }}" placeholder="{{ __('admin.search_action') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">{{ __('admin.date_from') }}</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">{{ __('admin.date_to') }}</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">{{ __('admin.filter') }}</button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">{{ __('admin.reset') }}</a>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped" id="activityLogsTable">
                    <thead>
                        <tr>
                            <th>{{ __('admin.id') }}</th>
                            <th>{{ __('admin.user') }}</th>
                            <th>{{ __('admin.action') }}</th>
                            <th>{{ __('admin.description') }}</th>
                            <th>{{ __('admin.ip_address') }}</th>
                            <th>{{ __('admin.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>
                                    @if($log->user)
                                        <a href="{{ route('admin.users.show', $log->user->id) }}">
                                            {{ $log->user->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('admin.deleted_user') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $log->action }}</span>
                                </td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#activityLogsTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false,
            "ordering": false,
            "language": {
                "url": "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json' : '//cdn.datatables.net/plug-ins/1.10.25/i18n/English.json' }}"
            }
        });
    });
</script>
@endsection
