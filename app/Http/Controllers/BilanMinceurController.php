<?php

namespace App\Http\Controllers;

use App\Mail\BilanMinceurRappel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BilanMinceurController extends Controller
{
    const PLANITY_URL = 'https://www.planity.com/institut-corps-a-coeur-14270-mezidon-vallee-dauge';

    public function show()
    {
        return view('pages.bilan-minceur');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'nom'              => 'required|string|max:100',
            'prenom'           => 'required|string|max:100',
            'email'            => 'required|email|max:255',
            'telephone'        => 'required|string|max:20',
            'objectifs'        => 'required|array|min:1',
            'objectifs.*'      => 'string|in:Perdre du poids,Perdre des centimètres,Raffermir et tonifier',
            'objectif_quantite'=> 'nullable|string|max:100',
            'objectif_delai'   => 'nullable|string|max:100',
            'acceptation'      => 'required|accepted',
            'action'           => 'required|in:planity,rappel',
        ], [
            'nom.required'        => 'Le nom est obligatoire.',
            'prenom.required'     => 'Le prénom est obligatoire.',
            'email.required'      => 'L\'adresse e-mail est obligatoire.',
            'email.email'         => 'L\'adresse e-mail n\'est pas valide.',
            'telephone.required'  => 'Le téléphone est obligatoire.',
            'objectifs.required'  => 'Veuillez sélectionner au moins un objectif.',
            'acceptation.required'=> 'Vous devez accepter les conditions pour continuer.',
            'acceptation.accepted'=> 'Vous devez accepter les conditions pour continuer.',
            'action.required'     => 'Veuillez choisir une option.',
        ]);

        if ($validated['action'] === 'rappel') {
            Mail::to('contact@institutcorpsacoeur.fr')
                ->send(new BilanMinceurRappel($validated));

            return redirect()
                ->route('bilan-minceur.show')
                ->with('success', "Merci {$validated['prenom']}, votre demande a bien été reçue ! Nous vous rappelons très prochainement.");
        }

        // action === 'planity' : redirection directe vers Planity
        return redirect(self::PLANITY_URL);
    }
}
