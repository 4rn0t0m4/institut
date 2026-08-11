<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $file->storeAs('editor-uploads', $filename, 'public');

        // L'hébergement applique un umask 077 : les dossiers créés par Flysystem
        // naissent en 0700 et Apache renvoie alors 403 sur les fichiers servis.
        $dir = Storage::disk('public')->path('editor-uploads');
        if (is_dir($dir) && (fileperms($dir) & 0777) !== 0755) {
            @chmod($dir, 0755);
        }

        return response()->json([
            'location' => '/storage/editor-uploads/'.$filename,
        ]);
    }
}
