<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'folder_id',
        'department_id',
        'uploaded_by',
        'title',
        'file_name',
        'file_path',
    ];

    /**
     * Get the folder that holds the file.
     *
     * @return BelongsTo<Folder, File>
     */
    public function folder(): BelongsTo
    {
        // withTrashed: nama folder tetap ter-resolve walau folder di-soft-delete.
        return $this->belongsTo(Folder::class)->withTrashed();
    }

    /**
     * Get the department metadata attached to the file.
     *
     * @return BelongsTo<Department, File>
     */
    public function department(): BelongsTo
    {
        // withTrashed: nama department tetap ter-resolve walau di-soft-delete.
        return $this->belongsTo(Department::class)->withTrashed();
    }

    /**
     * Get the user (administrator) who uploaded the file.
     *
     * @return BelongsTo<User, File>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
