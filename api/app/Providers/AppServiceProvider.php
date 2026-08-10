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
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Laravel 11 has no AuthServiceProvider by default, so policies are
        // registered here via the Gate facade.
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Folder::class, FolderPolicy::class);
        Gate::policy(File::class, FilePolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);

        // Model observers (activity log).
        Department::observe(DepartmentObserver::class);
        Folder::observe(FolderObserver::class);
        File::observe(FileObserver::class);
    }
}
