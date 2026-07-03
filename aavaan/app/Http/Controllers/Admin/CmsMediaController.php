<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsMediaController extends Controller
{
    private const MAX_DIMENSION = 2000;

    public function index(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $query = CmsMedia::with('uploader')->orderByDesc('id');
        if ($request->get('type') === 'image') {
            $query->where('mime_type', 'like', 'image/%');
        } elseif ($request->get('type') === 'file') {
            $query->where('mime_type', 'not like', 'image/%');
        }

        $media = $query->paginate(24)->withQueryString();

        // درخواست AJAX (انتخاب در ویرایشگر) فقط JSON برمی‌گرداند.
        if ($request->wantsJson()) {
            return response()->json($media->map(fn ($m) => [
                'id' => $m->id, 'url' => $m->url, 'name' => $m->original_name,
            ]));
        }

        return view('admin.cms.media.index', compact('media'));
    }

    public function upload(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf',
        ]);

        $file = $request->file('file');
        $size = $file->getSize();
        $mime = $file->getClientMimeType();
        $original = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');

        $relDir = 'uploads/cms/media/' . now()->format('Y') . '/' . now()->format('m');
        $absDir = public_path($relDir);
        if (! is_dir($absDir)) {
            mkdir($absDir, 0755, true);
        }

        $filename = Str::uuid() . '.' . $ext;
        $file->move($absDir, $filename);
        $absPath = $absDir . '/' . $filename;
        $relPath = $relDir . '/' . $filename;

        [$width, $height] = $this->processImage($absPath, $mime);

        $media = CmsMedia::create([
            'uploader_id'   => auth()->id(),
            'file_path'     => $relPath,
            'original_name' => $original,
            'file_size'     => is_file($absPath) ? filesize($absPath) : $size,
            'mime_type'     => $mime,
            'width'         => $width,
            'height'        => $height,
        ]);

        return response()->json([
            'id'        => $media->id,
            'url'       => $media->url,
            'thumbnail' => $media->url,
            'name'      => $media->original_name,
        ]);
    }

    public function destroy(int $id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $media = CmsMedia::findOrFail($id);

        $abs = public_path($media->file_path);
        if (is_file($abs)) {
            @unlink($abs);
        }
        $media->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'فایل حذف شد.');
    }

    public function getUrl(int $id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        $media = CmsMedia::findOrFail($id);

        return response()->json(['url' => $media->url]);
    }

    /**
     * استخراج ابعاد و کوچک‌سازی تصاویر بزرگ‌تر از MAX_DIMENSION (در صورت وجود GD).
     * برای فایل‌های غیرتصویری یا نبود GD، بدون خطا رد می‌شود.
     *
     * @return array{0:?int,1:?int} [width, height]
     */
    private function processImage(string $path, string $mime): array
    {
        if (! str_starts_with($mime, 'image/') || ! function_exists('getimagesize')) {
            return [null, null];
        }

        $info = @getimagesize($path);
        if (! $info) {
            return [null, null];
        }
        [$w, $h] = $info;

        if (! extension_loaded('gd') || ($w <= self::MAX_DIMENSION && $h <= self::MAX_DIMENSION)) {
            return [$w, $h];
        }

        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/gif'  => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default      => null,
        };
        if (! $src) {
            return [$w, $h];
        }

        $ratio  = min(self::MAX_DIMENSION / $w, self::MAX_DIMENSION / $h);
        $newW   = (int) round($w * $ratio);
        $newH   = (int) round($h * $ratio);
        $dst    = imagecreatetruecolor($newW, $newH);

        if (in_array($mime, ['image/png', 'image/gif', 'image/webp'], true)) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        match ($mime) {
            'image/jpeg' => imagejpeg($dst, $path, 85),
            'image/png'  => imagepng($dst, $path),
            'image/gif'  => imagegif($dst, $path),
            'image/webp' => function_exists('imagewebp') ? imagewebp($dst, $path, 85) : null,
            default      => null,
        };
        imagedestroy($src);
        imagedestroy($dst);

        return [$newW, $newH];
    }
}
