<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function index()
    {
        $recipientCount = Order::whereNotNull('billing_email')
            ->where('billing_email', '!=', '')
            ->distinct('billing_email')
            ->count('billing_email');

        return view('admin.newsletter.index', compact('recipientCount'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:50000',
        ]);

        $emails = Order::whereNotNull('billing_email')
            ->where('billing_email', '!=', '')
            ->distinct()
            ->pluck('billing_email');

        $sent = 0;
        $errors = 0;

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new NewsletterMail($validated['subject'], $validated['content']));
                $sent++;
            } catch (\Throwable $e) {
                $errors++;
                Log::error("Newsletter: échec envoi à {$email}", ['error' => $e->getMessage()]);
            }
        }

        Log::info("Newsletter envoyée : {$sent} envoyés, {$errors} erreurs", ['subject' => $validated['subject']]);

        $message = "{$sent} email(s) envoyé(s) avec succès.";
        if ($errors > 0) {
            $message .= " {$errors} erreur(s).";
        }

        return redirect()->route('admin.newsletter.index')->with('success', $message);
    }
}
