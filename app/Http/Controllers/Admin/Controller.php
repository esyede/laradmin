<?php

namespace App\Http\Controllers\Admin;

use App\Traits\RestfulResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use RestfulResponse;

    /**
     * Base directory for uploads
     */
    const UPLOAD_FOLDER_PREFIX = 'uploads';

    /**
     * Save uploaded files to storage, and return information about each file
     *
     * @param Request $request
     * @param string  $folder  Storage folder
     *
     * @return array
     *
     * Return data example:
     * [
     *     'file' => [
     *         'filename' => 'filename.jpg',
     *         'ext' => 'jpg',
     *         'path' => '/path/to/filename.jpg',
     *         'size' => 10240,
     *         'mime_type' => 'image/jpeg',
     *     ],
     *     'other' => [...],
     * ]
     */
    protected function saveFiles(Request $request, string $folder = null): array
    {
        $files = $request->file();
        $driver = Storage::disk('uploads');

        $folder = static::UPLOAD_FOLDER_PREFIX.($folder ? '/'.trim($folder, '/') : '');

        $files = array_map(function (UploadedFile $file) use ($driver, $folder) {
            $ext = $file->getClientOriginalExtension();
            $filename = md5_file($file).($ext ? '.' . $ext : '');
            $path = $driver->putFileAs($folder, $file, $filename);

            return [
                'filename' => $filename,
                'ext' => $ext,
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
        }, $files);

        return $files;
    }
}
