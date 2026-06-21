<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertArtistSpecialtyRequest;
use App\Models\ArtistSpecialty;
use App\Models\ArtistSpecialtyMedia;
use App\Services\ImageResizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArtistSpecialtyController extends Controller
{
    private function safeUnlink(string $relativePath): void
    {
        $root = realpath(public_path('uploads'));
        if (!$root) return;
        $abs  = realpath(public_path('uploads/' . $relativePath));
        if ($abs && str_starts_with($abs, $root . DIRECTORY_SEPARATOR) && is_file($abs)) {
            if (!unlink($abs)) {
                Log::warning("Failed to delete specialty media file: {$abs}");
            }
        }
    }

    public function store(UpsertArtistSpecialtyRequest $request)
    {
        $user = auth()->user();

        if (!$user->artistProfile) {
            return back()->with('error', 'ابتدا اطلاعات پایه پروفایل را ذخیره کنید.');
        }

        if ($user->artistSpecialties()->where('category_id', $request->category_id)->exists()) {
            return back()->with('error', 'این تخصص قبلاً به پروفایل شما اضافه شده است.');
        }

        $data             = $request->validated();
        $data['user_id']  = $user->id;
        $isFirst          = $user->artistSpecialties()->count() === 0;
        $wantsPrimary     = $request->boolean('is_primary') || $isFirst;

        if ($wantsPrimary) {
            $user->artistSpecialties()->update(['is_primary' => false]);
            $data['is_primary'] = true;
        }

        ArtistSpecialty::create($data);

        return back()->with('success', 'تخصص با موفقیت اضافه شد.');
    }

    public function update(UpsertArtistSpecialtyRequest $request, ArtistSpecialty $specialty)
    {
        if ($specialty->user_id !== auth()->id()) abort(403);

        $data = $request->validated();

        if ($request->boolean('is_primary')) {
            auth()->user()->artistSpecialties()
                ->where('id', '!=', $specialty->id)
                ->update(['is_primary' => false]);
            $data['is_primary'] = true;
        }

        $specialty->update($data);

        return back()->with('success', 'تخصص با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(ArtistSpecialty $specialty)
    {
        if ($specialty->user_id !== auth()->id()) abort(403);

        foreach ($specialty->media as $media) {
            if ($media->file_path) {
                $this->safeUnlink($media->file_path);
            }
        }

        $specialty->delete();

        return back()->with('success', 'تخصص و رسانه‌های آن حذف شد.');
    }

    public function setPrimary(ArtistSpecialty $specialty)
    {
        if ($specialty->user_id !== auth()->id()) abort(403);

        auth()->user()->artistSpecialties()->update(['is_primary' => false]);
        $specialty->update(['is_primary' => true]);

        return back()->with('success', 'تخصص اصلی تغییر کرد.');
    }

    public function uploadMedia(Request $request, ArtistSpecialty $specialty)
    {
        if ($specialty->user_id !== auth()->id()) abort(403);

        $type = $request->input('type', 'photo');

        if ($type === 'photo') {
            $totalPhotos = ArtistSpecialtyMedia::whereHas(
                'artistSpecialty',
                fn($q) => $q->where('user_id', auth()->id())
            )->where('type', 'photo')->count();

            if ($totalPhotos >= 30) {
                return back()->with(
                    'error',
                    'به سقف ۳۰ عکس کل پروفایل رسیده‌اید. برای افزودن عکس جدید، یک عکس قبلی را حذف کنید.'
                );
            }

            $request->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:15360',
                'title' => 'nullable|string|max:200',
            ], [
                'photo.required' => 'فایل عکس الزامی است.',
                'photo.image'    => 'فایل باید تصویر باشد.',
                'photo.mimes'    => 'فرمت تصویر باید jpg، png یا webp باشد.',
                'photo.max'      => 'حجم عکس نباید بیشتر از ۱۵ مگابایت باشد.',
            ]);

            $user     = auth()->user();
            $dir      = public_path("uploads/{$user->id}/specialties/{$specialty->id}");
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $filename = Str::uuid() . '.jpg';
            $destPath = "{$dir}/{$filename}";

            try {
                app(ImageResizer::class)->resizeAndSave(
                    $request->file('photo')->getPathname(),
                    $destPath
                );
            } catch (\Throwable $e) {
                Log::error('Specialty photo resize failed: ' . $e->getMessage());
                return back()->with('error', 'خطا در پردازش تصویر. لطفاً دوباره امتحان کنید.');
            }

            ArtistSpecialtyMedia::create([
                'artist_specialty_id' => $specialty->id,
                'type'                => 'photo',
                'file_path'           => "{$user->id}/specialties/{$specialty->id}/{$filename}",
                'title'               => $request->input('title') ?: null,
                'sort_order'          => (ArtistSpecialtyMedia::where('artist_specialty_id', $specialty->id)->max('sort_order') ?? 0) + 1,
            ]);

            return back()->with('success', 'عکس آپلود و فشرده‌سازی شد.');
        }

        if ($type === 'video_link') {
            $request->validate([
                'external_url' => [
                    'required', 'string', 'max:500',
                    'regex:/^https:\/\/(www\.)?aparat\.com\/v\/[A-Za-z0-9]+/',
                ],
                'title' => 'nullable|string|max:200',
            ], [
                'external_url.required' => 'لینک ویدیو الزامی است.',
                'external_url.regex'    => 'لینک ویدیو باید آپارات باشد: https://www.aparat.com/v/...',
            ]);

            ArtistSpecialtyMedia::create([
                'artist_specialty_id' => $specialty->id,
                'type'                => 'video_link',
                'external_url'        => $request->input('external_url'),
                'title'               => $request->input('title') ?: null,
                'sort_order'          => (ArtistSpecialtyMedia::where('artist_specialty_id', $specialty->id)->max('sort_order') ?? 0) + 1,
            ]);

            return back()->with('success', 'لینک ویدیو آپارات اضافه شد.');
        }

        return back()->with('error', 'نوع رسانه نامعتبر است.');
    }

    public function deleteMedia(ArtistSpecialtyMedia $media)
    {
        if ($media->artistSpecialty->user_id !== auth()->id()) abort(403);

        if ($media->file_path) {
            $this->safeUnlink($media->file_path);
        }

        $media->delete();

        return back()->with('success', 'رسانه حذف شد.');
    }
}
