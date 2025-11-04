<?php


namespace App\Helpers;


use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class UploadHelper
{
    use ValidationHelper;

    use ValidationHelper;

    private $uploadPath, $fullUrl, $disk;
    private ?UploadedFile $file;
    private ?Upload $upload;
    private ?array $params = [];

    public function handleFileUpload(UploadedFile $file, ?array $options = [])
    {
        try {
            $this->file = $file;
            $this->validateRequest($options);
            $this->setFileName();
            $this->setFileSystemDisk();
            $this->setUploadFolder();

            $this->uploadPath = $this->file->storePubliclyAs($this->params['folder'], $this->params['file_name'], (string)$this->disk);

            $this->fullUrl = Storage::disk($this->disk)->url( $this->uploadPath );
            $this->createUpload();

            return [
                'success' => true,
                'message' => 'upload successful',
                'data' => $this->upload->toArray(),
            ];
        }catch (\Exception $e) {
            Log::error($e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function handleFileDelete(array $options)
    {
        try {
            array_merge($options, ['action' => 'delete']);
            $this->validateRequest($options);
            $this->setFileSystemDisk();
            $this->setUpload();
            Storage::disk($this->disk)->delete($this->upload->storage_url);
            $this->upload->delete();
            return [
                'success' => true,
                'message' => 'file deleted',
                'data' => []
            ];
        }catch (\Exception $e) {
            Log::error($e);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }
    }

    private function setFileName()
    {
        if (!array_key_exists('file_name', $this->params)) {
            $this->params['file_name'] = explode('.', $this->file->getClientOriginalName())[0];
        }
        $this->params['file_name'] = Str::slug($this->params['file_name']);
        $this->params['file_name'] = $this->params['file_name'].'.'.$this->file->getClientOriginalExtension();
    }

    private function setUploadFolder()
    {
        if (!array_key_exists('folder', $this->params)) {
            $this->params['folder'] = 'uploads';
        }
    }

    private function setUpload()
    {
        if (array_key_exists('upload_id', $this->params) && $this->params['upload_id']) {
            $this->upload = Upload::query()->find($this->params['upload_id']);
            if (empty($this->upload)) abort(401, 'upload not found');
        }else
            abort(401, 'can not delete file, upload ID missing');
    }

    private function setFileSystemDisk()
    {
        $this->disk = env('FILESYSTEM_DISK', 'local');
    }

    private function createUpload()
    {
        $this->upload = Upload::query()->create([
            'storage_url' => $this->uploadPath,
            'full_url' => $this->fullUrl,
            'size' => $this->file->getSize(),
            'extension' => $this->file->getClientOriginalExtension(),
            'name' => $this->file->getClientOriginalName(),
            'driver' => $this->disk
        ]);
    }

    private function validateRequest($options)
    {
        $this->params = $this->validate($options, [
            "action" => "nullable|string",
            "file_name" => "nullable|string",
            "driver" => "nullable|string",
            "upload_id" => "required_unless:action,null|exists:uploads,id",
            "folder" => "nullable|string",
        ]);
    }
}
