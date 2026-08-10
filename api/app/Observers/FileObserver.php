<?php

namespace App\Observers;

use App\Models\File;

class FileObserver extends BaseObserver
{
    public function created(File $file): void
    {
        $this->log('created', $file);
    }

    public function updated(File $file): void
    {
        $this->log('updated', $file);
    }

    public function deleted(File $file): void
    {
        $this->log('deleted', $file);
    }

    public function restored(File $file): void
    {
        $this->log('restored', $file);
    }
}
