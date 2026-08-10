<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class BaseObserver
{
    /**
     * Write an activity log entry. Auth::check() guards against writes during
     * seeders / console commands where no authenticated user exists.
     */
    protected function log(string $action, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => class_basename($model),
            'entity_name' => $model->name ?? $model->title,
        ]);
    }
}
