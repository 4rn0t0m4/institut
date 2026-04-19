<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EditorUploadController extends Controller
{
    public function upload(Request $request)
    {
        if (! $request->hasFile('file') || ! $request->file('file')->isValid()) {
            return response()->json(['error' => 'Fichier invalide ou trop volumineux (max 8 Mo)'], 422);
        }

        $file = $request->file('file');

        // Vérification manuelle du type MIME réel
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        $mime = $file->getMimeType();
        if (! in_array($mime, $allowed)) {
            return response()->json(['error' => "Type de fichier non autorisé ($mime)"], 422);
        }

        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', 'image/svg+xml' => 'svg'];
        $ext = $extensions[$mime] ?? $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::uuid().'.'.$ext;
        $file->storeAs('public/editor-uploads', $filename);

        // Copier dans public/storage (OVH mutualisé ne supporte pas les symlinks)
        $publicDir = public_path('storage/editor-uploads');
        if (is_dir($publicDir)) {
            copy(storage_path('app/public/editor-uploads/'.$filename), $publicDir.'/'.$filename);
        }

        return response()->json([
            'location' => '/storage/editor-uploads/'.$filename,
        ]);
    }
}
