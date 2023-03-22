<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $items = Media::query()->orderBy('created_at', 'desc')->get();
        $endpoint = rtrim(route('media.index'), '/') . '/';

        return view('media')->with([
            'items' => $items,
            'endpoint' => $endpoint,
        ]);
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');

        if(empty($file)) {
            return redirect()->route('media.index')->with([
                'messageType' => 'error',
                'message' => 'ファイルを選択してください',
            ]);
        }

        $mime = $file->getMimeType();
        $now = Carbon::now();
        $format = $now->format('Y/m/d');

        if(strpos($mime, 'image/') === 0) {
            $dir = 'images/' . $format;
        } else {
            $dir = 'files/' . $format;
        }

        $fileName = $file->getClientOriginalName();

        try {
            $request->file('file')->storeAs('public/' . $dir, $fileName);
            $thumbnail = $this->createThumbnail($dir, $fileName);
            $this->saveMedia($request, $file, $dir, $fileName, $thumbnail);
            $messageType = 'success';
            $message = '保存成功';
        } catch(\Exception $e) {
            logger()->error($e->getMessage());
            $messageType = 'error';
            $message = '保存失敗';

        }

        return redirect()->route('media.index')->with([
            'messageType' => $messageType,
            'message' => $message,
        ]);
    }

    public function show(Request $request, $alias)
    {
        $headers = [];

        $itemQuery = Media::query();
        $item = $itemQuery->where('alias', '=', $alias)->first();

        if(!$item) {
            abort(404);
        }

        if((string)$item->mime !== '') {
            $headers['Content-Type'] = $item->mime;
        }

        if((string)$alias !== '') {
            $headers['Content-Disposition'] = 'inline; filename="' . $alias . '"';
        }

        $path = public_path('storage/' . $item->path);

        $response = Response(File::get($path), 200, $headers);

        return $response;
    }

    public function delete(Request $request, $id)
    {
        $itemQuery = Media::query();
        $item = $itemQuery->where('id', '=', $id)->first();
        if($item) {
            $item->delete();
            $path = storage_path('app/public/' . $item->path);
            $thumbnailPath = storage_path('app/public/' . $item->thumbnail_path);
            if(file_exists($path)) {
                unlink($path);
            }
            if(file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
            }

            $messageType = 'success';
            $message = '削除成功';
        } else {
            $messageType = 'error';
            $message = '削除失敗';
        }

        return redirect()->route('media.index')->with([
            'messageType' => $messageType,
            'message' => $message,
        ]);
    }

    public function saveMedia(Request $request, $file, $dir, $name, $thumbnail)
    {
        $alias = $request->get('alias');
        $path = $dir . '/' . $name;
        $model = new Media();
        $model->title = $name;
        $model->path = $path;
        $model->mime = $file->getMimeType();
        if((string)$alias !== '') {
            $model->alias = $alias;
        }

        if((string)$thumbnail !== '') {
            $model->thumbnail_path = $thumbnail;
        }

        $model->save();

        return $model;
    }

    public function createThumbnail($dir, $name)
    {
        $filePath = storage_path('app/public/' . $dir . '/' . $name);
        if(!file_exists($filePath)) {
            return null;
        }

        $mime = File::mimeType($filePath);
        if(strpos($mime, 'image/') !== 0) {
            $list = explode('.', $name);
            $end = end($list);
            $name = str_replace($end, 'png', $name);
            $dir = str_replace('files', 'images', $dir);
        }

        $image = Image::make($filePath);
        $image->orientate();
        if($image->getWidth() >= $image->getHeight()) {
            $width = 600;
            $height = null;
        } else {
            $width = null;
            $height = 600;
        }

        $image->resize($width, $height, function($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $path = $dir . '/500x500_' . $name;
        $image->save(storage_path('app/public/' . $path));

//        $this->cropThumbnail(storage_path('app/public/' . $path));

        return $path;
    }

    public function cropThumbnail($path)
    {
        $image = Image::make($path);
        if($image->getWidth() >= $image->getHeight()) {
            $width = 500;
            $height = null;
        } else {
            $width = null;
            $height = 500;
        }
        $image->resize($width, $height, function($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $image->crop(500, 500);

        if(file_exists($path)) {
            unlink($path);
        }

        $image->save($path);
    }
}
