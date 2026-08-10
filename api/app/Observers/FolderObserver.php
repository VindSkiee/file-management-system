<?php

namespace App\Observers;

use App\Models\Folder;

class FolderObserver extends BaseObserver
{
    public function created(Folder $folder): void
    {
        $this->log('created', $folder);
    }

    public function updated(Folder $folder): void
    {
        $this->log('updated', $folder);
    }

    public function deleted(Folder $folder): void
    {
        $this->log('deleted', $folder);
    }

    public function restored(Folder $folder): void
    {
        $this->log('restored', $folder);
    }
}
