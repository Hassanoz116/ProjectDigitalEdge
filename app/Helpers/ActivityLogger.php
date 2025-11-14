<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log an activity
     */
    public static function log($action, $description = null, $model = null, $properties = null)
    {
        $data = [
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        
        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->id;
        }
        
        if ($properties) {
            $data['properties'] = $properties;
        }
        
        ActivityLog::create($data);
    }
}
