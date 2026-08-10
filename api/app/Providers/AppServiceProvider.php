<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Observers\DepartmentObserver;
use App\Observers\FileObserver;
use App\Observers\FolderObserver;
use App\Policies\ActivityLogPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\FilePolicy;
use App\Policies\FolderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Folder::class, FolderPolicy::class);
        Gate::policy(File::class, FilePolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);

        Department::observe(DepartmentObserver::class);
        Folder::observe(FolderObserver::class);
        File::observe(FileObserver::class);
    }
}
