<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SystemMedia extends Model
{
    protected $fillable = [
        'filename',
        'ext',
        'category_id',
        'path',
        'path_key',
        'size',
        'mime_type',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'size' => 'integer',
    ];

    protected $perPage = 30;

    public function category()
    {
        return $this->belongsTo(SystemMediaCategory::class, 'category_id');
    }

    public function delete()
    {
        DB::beginTransaction();

        $deleted = parent::delete();

        if (! $deleted) {
            throw new FileException('Failed to delete file record from database');
        }

        if (! $this->hasSameFile()) {
            $storage = Storage::disk('uploads');

            if ($storage->exists($this->path) && ! $storage->delete($this->path)) {
                DB::rollBack();
                throw new FileException('Failed to delete file from disk');
            }
        }

        DB::commit();
        return true;
    }

    protected function hasSameFile()
    {
        return !!static::query()
            ->where('path_key', $this->path_key)
            ->where('id', '<>', $this->id)
            ->first();
    }

    public function setPathAttribute($value)
    {
        $this->attributes['path'] = $value;
        $this->attributes['path_key'] = md5($value);
    }
}
