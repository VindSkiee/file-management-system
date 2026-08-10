<?php

namespace App\Observers;

use App\Models\Department;

class DepartmentObserver extends BaseObserver
{
    public function created(Department $department): void
    {
        $this->log('created', $department);
    }

    public function updated(Department $department): void
    {
        $this->log('updated', $department);
    }

    public function deleted(Department $department): void
    {
        $this->log('deleted', $department);
    }

    public function restored(Department $department): void
    {
        $this->log('restored', $department);
    }
}
