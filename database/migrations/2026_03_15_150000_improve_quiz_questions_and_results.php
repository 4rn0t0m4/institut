<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Améliore le quiz diagnostic de peau :
 * - Ajoute 2 questions contextuelles (objectif + routine)
 * - Reformule les questions et choix existants
 * - Remplit les descriptions des 19 résultats
 *
 * Migration idempotente : nettoie tout état partiel avant d'appliquer.
 */
return new class extends Migration
{
    public function up(): void
    {
        $quizId = DB::table('quizzes')->where('slug', 'diagnostic-de-peau')->value('id');

        if (! $quizId) {
            return;
        }

        // ─── Nettoyage : supprimer toute trace d'exécution précédente ────
        // Supprimer les questions contextuelles ajoutées précédemment (s'il y en a)
        $addedQuestions = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->where(function ($q) {
                $q->where('title', 'like', '%objectif principal%')
                    ->orWhere('title', 'like', '%routine de soin%');
            })
            ->pluck('id');

        if ($addedQuestions->isNotEmpty()) {
            DB::table('quiz_choices')->whereIn('question_id', $addedQuestions)->delete();
            DB::table('quiz_answers')->whereIn('question_id', $addedQuestions)->delete();
            DB::table('quiz_questions')->whereIn('id', $addedQuestions)->delete();
        }

        // Supprimer aussi toute question orpheline (sans choix)
        $orphans = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->pluck('id')
            ->filter(fn ($id) => DB::table('quiz_choices')->where('question_id', $id)->count() === 0);

        if ($orphans->isNotEmpty()) {
            DB::table('quiz_answers')->whereIn('question_id', $orphans)->delete();
            DB::table('quiz_questions')->whereIn('id', $orphans)->delete();
        }

        // ─── Rétablir les sort_order originaux (1-9) pour les questions existantes ─
        $existing = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('sort_order')
            ->pluck('id');

        foreach ($existing->values() as $i => $id) {
            DB::table('quiz_questions')->where('id', $id)->update(['sort_order' => $i + 1]);
        }

        // ─── À ce stade, on a exactement les 9 questions originales (sort 1-9) ───

        // Décaler les sort_order de +2 pour faire place aux nouvelles questions
        DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->increment('sort_order', 2);

        // ─── Ajouter les 2 questions contextuelles ──────────────────────

        // Question 1 : Objectif principal
        $q0Id = DB::table('quiz_questions')->insertGetId([
            'quiz_id' => $quizId,
            'title' => 'Quel est ton objectif principal pour ta peau ?',
            'question' => 'Cela nous aidera à personnaliser tes recommandations.',
            'type' => 'single',
            'sort_order' => 1,
            'accept_comments' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([
            ['label' => 'Lutter contre les signes de l\'âge', 'sort' => 1],
            ['label' => 'Retrouver de l\'éclat et un teint uniforme', 'sort' => 2],
            ['label' => 'Hydrater et nourrir en profondeur', 'sort' => 3],
            ['label' => 'Purifier et réduire les imperfections', 'sort' => 4],
            ['label' => 'Apaiser les rougeurs et sensibilités', 'sort' => 5],
        ] as $choice) {
            DB::table('quiz_choices')->insert([
                'question_id' => $q0Id,
                'label' => $choice['label'],
                'points' => 0,
                'is_correct' => false,
                'goto' => 'next',
                'sort_order' => $choice['sort'],
            ]);
        }

        // Question 2 : Routine actuelle
        $q0bId = DB::table('quiz_questions')->insertGetId([
            'quiz_id' => $quizId,
            'title' => 'Comment décrirais-tu ta routine de soin actuelle ?',
            'question' => 'Il n\'y a pas de mauvaise réponse, c\'est pour adapter nos conseils.',
            'type' => 'single',
            'sort_order' => 2,
            'accept_comments' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([
            ['label' => 'Je n\'ai pas vraiment de routine', 'sort' => 1],
            ['label' => 'Un nettoyant et une crème, c\'est tout', 'sort' => 2],
            ['label' => 'J\'ai une routine en plusieurs étapes', 'sort' => 3],
        ] as $choice) {
            DB::table('quiz_choices')->insert([
                'question_id' => $q0bId,
                'label' => $choice['label'],
                'points' => 0,
                'is_correct' => false,
                'goto' => 'next',
                'sort_order' => $choice['sort'],
            ]);
        }

        // ─── Mettre à jour les questions existantes par ID de choix ─────
        // On identifie chaque question par ses choix (goto + points = signature unique)

        $allOriginal = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->where('id', '!=', $q0Id)
            ->where('id', '!=', $q0bId)
            ->pluck('id');

        foreach ($allOriginal as $qId) {
            $choices = DB::table('quiz_choices')
                ->where('question_id', $qId)
                ->orderBy('sort_order')
                ->get();

            $gotos = $choices->pluck('goto')->toArray();
            $points = $choices->pluck('points')->map(fn ($p) => (float) $p)->toArray();
            $count = $choices->count();
            $numericGotos = array_filter($gotos, 'is_numeric');
            $hasFinalize = in_array('finalize', $gotos);

            // Q1 "tire" : 3 choix, tous goto numériques
            if ($count === 3 && count($numericGotos) === 3) {
                DB::table('quiz_questions')->where('id', $qId)->update([
                    'title' => 'Comment ta peau réagit-elle après le nettoyage ?',
                    'question' => 'Pense à ce que tu ressens juste après avoir nettoyé ton visage, sans crème.',
                ]);
                $labels = ['Oui, elle tire et est inconfortable', 'Un peu, surtout sur les joues', 'Pas du tout, elle est confortable'];
                foreach ($choices->values() as $i => $c) {
                    if (isset($labels[$i])) {
                        DB::table('quiz_choices')->where('id', $c->id)->update(['label' => $labels[$i]]);
                    }
                }
                continue;
            }

            // Q8 "boutons" : 2 choix, un finalize + un numérique
            if ($count === 2 && $hasFinalize && count($numericGotos) === 1) {
                DB::table('quiz_questions')->where('id', $qId)->update([
                    'title' => 'As-tu des imperfections ou boutons régulièrement ?',
                    'question' => 'Petits boutons, points noirs ou imperfections récurrentes.',
                ]);
                $labels = ['Oui, assez souvent', 'Non, rarement ou jamais'];
                foreach ($choices->values() as $i => $c) {
                    if (isset($labels[$i])) {
                        DB::table('quiz_choices')->where('id', $c->id)->update(['label' => $labels[$i]]);
                    }
                }
                continue;
            }

            // Q2, Q4, Q5 : 2 choix, tous goto numériques
            if ($count === 2 && count($numericGotos) === 2) {
                // Distinguer par les destinations
                $dest1 = (int) $gotos[0];
                $dest2 = (int) $gotos[1];

                // Q4 "pores" : un goto pointe vers la question boutons (qui a un choix finalize pts:13)
                $dest1HasBoutons = DB::table('quiz_choices')
                    ->where('question_id', $dest1)
                    ->where('goto', 'finalize')
                    ->where('points', 13)
                    ->exists();

                if ($dest1HasBoutons) {
                    DB::table('quiz_questions')->where('id', $qId)->update([
                        'title' => 'As-tu les pores visiblement dilatés ?',
                        'question' => 'Regarde de près ta peau dans un miroir, surtout sur le nez et les joues.',
                    ]);
                    $labels = ['Oui, mes pores sont bien visibles', 'Non, mes pores sont plutôt fins'];
                    foreach ($choices->values() as $i => $c) {
                        if (isset($labels[$i])) {
                            DB::table('quiz_choices')->where('id', $c->id)->update(['label' => $labels[$i]]);
                        }
                    }
                    continue;
                }

                // Q5 "rugueuse" : les 2 destinations ont des choix finalize
                $dest1HasFinalize = DB::table('quiz_choices')->where('question_id', $dest1)->where('goto', 'finalize')->exists();
                $dest2HasFinalize = DB::table('quiz_choices')->where('question_id', $dest2)->where('goto', 'finalize')->exists();

                if ($dest1HasFinalize && $dest2HasFinalize) {
                    DB::table('quiz_questions')->where('id', $qId)->update([
                        'title' => 'Ta peau est-elle parfois rugueuse ou desquame-t-elle ?',
                        'question' => 'Cela peut se manifester par des tiraillements ou de petites peaux sèches.',
                    ]);
                    $labels = ['Oui, j\'ai souvent des zones sèches ou qui pèlent', 'Non, ma peau est lisse au toucher'];
                    foreach ($choices->values() as $i => $c) {
                        if (isset($labels[$i])) {
                            DB::table('quiz_choices')->where('id', $c->id)->update(['label' => $labels[$i]]);
                        }
                    }
                    continue;
                }

                // Q2 "brille" : sinon
                DB::table('quiz_questions')->where('id', $qId)->update([
                    'title' => 'Ta peau a-t-elle tendance à briller en journée ?',
                    'question' => 'Observe surtout ta zone T (front, nez, menton) en milieu de journée.',
                ]);
                $labels = ['Non, ma peau reste mate', 'Oui, surtout sur la zone T'];
                foreach ($choices->values() as $i => $c) {
                    if (isset($labels[$i])) {
                        DB::table('quiz_choices')->where('id', $c->id)->update(['label' => $labels[$i]]);
                    }
                }
                continue;
            }

            // Questions "préoccupation" : tous goto=finalize
            if ($choices->every(fn ($c) => $c->goto === 'finalize')) {
                $minPts = min($points);

                $subtitle = match (true) {
                    $minPts <= 4 => 'Choisis celle qui te correspond le plus en ce moment.',
                    $minPts <= 8 => 'Même si tu en as plusieurs, choisis celle qui t\'embête le plus.',
                    $minPts <= 12 => 'Ta peau est plutôt équilibrée, mais as-tu un souci particulier ?',
                    default => 'Ta peau produit du sébum, mais tu peux avoir d\'autres soucis aussi.',
                };

                DB::table('quiz_questions')->where('id', $qId)->update([
                    'title' => 'Quelle est ta préoccupation principale ?',
                    'question' => $subtitle,
                ]);

                // Mettre à jour les labels de préoccupation
                foreach ($choices as $c) {
                    $label = trim($c->label);
                    $newLabel = match (true) {
                        str_contains(strtolower($label), 'ride') => 'Les rides et ridules',
                        str_contains(strtolower($label), 'rougeur') => 'Les rougeurs et sensibilités',
                        str_contains(strtolower($label), 'tache') => 'Les taches et le teint irrégulier',
                        str_contains(strtolower($label), 'bouton') => 'Les boutons et imperfections',
                        str_contains(strtolower($label), 'rien'), str_contains(strtolower($label), 'aucun') => 'Aucune en particulier',
                        default => $label,
                    };
                    DB::table('quiz_choices')->where('id', $c->id)->update(['label' => $newLabel]);
                }
            }
        }

        // ─── Remplir les descriptions des 19 résultats ──────────────────

        $descriptions = [
            1 => '<p>Ta peau manque de lipides et montre des signes de l\'âge. Elle a besoin de soins riches et nourrissants qui renforcent la barrière cutanée tout en lissant les rides.</p><p><strong>Tes priorités :</strong> nutrition intense, anti-âge, protection contre la déshydratation.</p>',
            2 => '<p>Ta peau est fine, réactive et manque de confort. Elle rougit facilement et tiraille après le nettoyage. Elle a besoin de douceur et de soins apaisants très riches.</p><p><strong>Tes priorités :</strong> apaiser, nourrir, protéger la barrière cutanée.</p>',
            3 => '<p>Ta peau est sèche et présente des taches pigmentaires ou un teint irrégulier. Elle a besoin d\'hydratation et de soins ciblés pour unifier le teint.</p><p><strong>Tes priorités :</strong> hydratation, éclat, correction des taches.</p>',
            4 => '<p>Ta peau manque de lipides au quotidien. Elle tiraille, peut être inconfortable mais n\'a pas de souci particulier. Une bonne routine nourrissante fera toute la différence.</p><p><strong>Tes priorités :</strong> nutrition, confort, protection.</p>',
            5 => '<p>Ta peau manque d\'eau (pas de gras) et montre des signes de l\'âge. Les ridules de déshydratation se mêlent aux rides d\'expression. Un bon soin hydratant anti-âge est essentiel.</p><p><strong>Tes priorités :</strong> hydratation profonde, anti-âge, repulper.</p>',
            6 => '<p>Ta peau manque d\'eau et présente des taches ou un teint terne. Elle a besoin d\'un boost d\'hydratation combiné à des actifs éclaircissants.</p><p><strong>Tes priorités :</strong> hydratation, éclat, uniformité du teint.</p>',
            7 => '<p>Ta peau est réactive et en manque d\'eau. Elle peut tirailler tout en étant sujette aux rougeurs. Elle a besoin de soins hydratants doux et apaisants.</p><p><strong>Tes priorités :</strong> hydratation douce, apaisement, renforcement cutané.</p>',
            8 => '<p>Ta peau manque d\'eau mais produit du sébum pour compenser. Résultat : elle brille tout en étant inconfortable. Il faut hydrater sans alourdir.</p><p><strong>Tes priorités :</strong> hydratation légère, équilibre, textures fluides.</p>',
            9 => '<p>Ta peau est bien équilibrée mais montre des signes de l\'âge. C\'est le moment idéal pour agir avec des soins anti-âge préventifs ou correcteurs.</p><p><strong>Tes priorités :</strong> prévention, fermeté, éclat.</p>',
            10 => '<p>Ta peau est équilibrée mais présente des taches ou un teint irrégulier, souvent liés au soleil ou aux variations hormonales. Des soins ciblés éclat feront la différence.</p><p><strong>Tes priorités :</strong> uniformité du teint, éclat, protection solaire.</p>',
            11 => '<p>Ta peau est globalement équilibrée mais réactive : elle rougit facilement face au froid, au stress ou à certains produits. Elle a besoin de soins doux et protecteurs.</p><p><strong>Tes priorités :</strong> apaisement, protection, douceur.</p>',
            12 => '<p>Ta peau est plutôt équilibrée mais a tendance à briller et à développer quelques imperfections. Des soins légers et matifiants t\'aideront à garder un teint net.</p><p><strong>Tes priorités :</strong> équilibre, matité, soin léger.</p>',
            13 => '<p>Ta peau produit beaucoup de sébum et est sujette aux boutons et imperfections. Elle a besoin de soins purifiants mais non agressifs pour rétablir l\'équilibre.</p><p><strong>Tes priorités :</strong> purifier, réguler le sébum, apaiser les inflammations.</p>',
            14 => '<p>Ta peau brille et produit du sébum mais montre aussi des signes de l\'âge. Il faut des soins anti-âge légers qui ne vont pas aggraver la brillance.</p><p><strong>Tes priorités :</strong> anti-âge, régulation du sébum, textures légères.</p>',
            15 => '<p>Ta peau brille et est en même temps réactive. C\'est un équilibre délicat : il faut purifier sans irriter. Privilégie les soins doux et régulateurs.</p><p><strong>Tes priorités :</strong> purifier en douceur, apaiser, réguler.</p>',
            16 => '<p>Ta peau produit du sébum et présente des taches ou marques post-inflammatoires. Les soins doivent cibler l\'excès de sébum tout en travaillant l\'éclat du teint.</p><p><strong>Tes priorités :</strong> régulation, éclat, correction des taches.</p>',
            17 => '<p>Ta peau manque d\'eau mais n\'a pas de souci majeur. Des soins hydratants bien choisis et une bonne routine suffiront à lui redonner confort et éclat.</p><p><strong>Tes priorités :</strong> hydratation, confort, barrière cutanée.</p>',
            18 => '<p>Bravo, ta peau est bien équilibrée ! Pas de souci particulier, mais une bonne routine d\'entretien la gardera belle longtemps. Profites-en pour prévenir.</p><p><strong>Tes priorités :</strong> entretien, prévention, éclat.</p>',
            19 => '<p>Ta peau brille et a les pores dilatés mais pas de souci majeur associé. Des soins matifiants et régulateurs suffiront à maintenir un teint net et frais.</p><p><strong>Tes priorités :</strong> matité, régulation du sébum, nettoyage doux.</p>',
        ];

        foreach ($descriptions as $points => $desc) {
            DB::table('quiz_results')
                ->where('quiz_id', $quizId)
                ->where('points_min', '<=', $points)
                ->where('points_max', '>=', $points)
                ->update(['description' => $desc]);
        }

        // Mettre à jour la description du quiz
        DB::table('quizzes')
            ->where('id', $quizId)
            ->update([
                'description' => 'Réponds à quelques questions simples pour découvrir ton type de peau et recevoir des recommandations personnalisées.',
            ]);
    }

    public function down(): void
    {
        // Non réversible — recréer depuis le WP import si nécessaire.
    }
};
