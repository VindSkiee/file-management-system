<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'file_name' => $this->file_name,
            'folder' => $this->whenLoaded('folder', fn () => $this->folder ? [
                'id' => $this->folder->id,
                'name' => $this->folder->name,
            ] : null),
            'department' => $this->whenLoaded('department', fn () => $this->department ? [
                'id' => $this->department->id,
                'name' => $this->department->name,
            ] : null),
            'uploaded_by' => $this->whenLoaded('uploader', fn () => $this->uploader?->name),
            'upload_date' => $this->created_at->format('d M Y H:i'),
            'file_url' => url(Storage::disk('public')->url($this->file_path)),
            'download_url' => route('files.download', $this->id),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
