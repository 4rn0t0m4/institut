<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

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

        $dir = Storage::disk('public')->path('editor-uploads');
        File::ensureDirectoryExists($dir, 0755);

        // Le SVG est vectoriel et le GIF peut être animé : les convertir les dégraderait.
        $passthrough = ['image/svg+xml' => 'svg', 'image/gif' => 'gif'];

        if (isset($passthrough[$mime])) {
            $filename = Str::uuid().'.'.$passthrough[$mime];
            $file->storeAs('editor-uploads', $filename, 'public');
        } else {
            // Redimensionne (max 1600px) et convertit en WebP, comme storeMedia() des produits.
            // 1600px couvre le plus grand rendu du blog (hero 896px CSS) en écran retina.
            $filename = Str::uuid().'.webp';

            Image::make($file->getRealPath())
                ->resize(1600, 1600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($dir.'/'.$filename);
        }

        // L'hébergement applique un umask 077 : les dossiers créés par Flysystem
        // naissent en 0700 et Apache renvoie alors 403 sur les fichiers servis.
        if (is_dir($dir) && (fileperms($dir) & 0777) !== 0755) {
            @chmod($dir, 0755);
        }

        return response()->json([
            'location' => '/storage/editor-uploads/'.$filename,
        ]);
    }
}
