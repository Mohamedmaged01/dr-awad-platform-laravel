<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Shared media handling for the admin content editors.
 *
 * Files are moved straight into `public/uploads/{dir}` so they are served
 * directly by the web server with no `storage:link` symlink — the most
 * portable option on shared/Plesk hosting. Each media field accepts either
 * an uploaded file or a pasted URL; the file wins when both are present.
 */
trait HandlesUploads
{
    /**
     * Resolve a media value from an uploaded file or a URL fallback.
     *
     * @param  string       $fileField  name of the <input type="file">
     * @param  string       $urlField   name of the URL text input
     * @param  string       $dir        sub-folder under public/uploads
     * @param  string|null  $current    existing stored value (kept when nothing new is provided)
     */
    protected function resolveMedia(Request $request, string $fileField, string $urlField, string $dir, ?string $current = null): ?string
    {
        if ($request->hasFile($fileField) && $request->file($fileField)->isValid()) {
            $file = $request->file($fileField);
            $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
            $name = Str::uuid()->toString() . '.' . $ext;

            $destination = public_path('uploads/' . trim($dir, '/'));
            if (! is_dir($destination)) {
                @mkdir($destination, 0755, true);
            }

            $file->move($destination, $name);

            return '/uploads/' . trim($dir, '/') . '/' . $name;
        }

        $url = trim((string) $request->input($urlField));

        return $url !== '' ? $url : $current;
    }

    /**
     * Extract the 11-char YouTube id from any common YouTube/Shorts URL,
     * or return the input unchanged if it already looks like a bare id.
     */
    protected function youtubeId(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/|v/))([A-Za-z0-9_-]{11})~', $value, $m)) {
            return $m[1];
        }

        // Already a bare id (or an uploaded file path — leave those untouched).
        return preg_match('~^[A-Za-z0-9_-]{11}$~', $value) ? $value : $value;
    }
}
