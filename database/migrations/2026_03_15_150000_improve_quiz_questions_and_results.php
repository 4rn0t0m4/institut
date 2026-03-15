<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $quizId = DB::table('quizzes')->where('slug', 'diagnostic-de-peau')->value('id');

        if (! $quizId) {
            return;
        }

        // ─── Ajouter 2 questions contextuelles en début de quiz ─────────

        // Décaler les sort_order existants
        DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->increment('sort_order', 2);

        // Question 0 : Objectif principal
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

        // Question 0b : Routine actuelle
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

        // ─── Améliorer les questions existantes ─────────────────────────

        // Récupérer les questions existantes par leur ancien sort_order (maintenant +2)
        $questions = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->where('id', '!=', $q0Id)
            ->where('id', '!=', $q0bId)
            ->orderBy('sort_order')
            ->get()
            ->keyBy(function ($q) {
                return $q->sort_order - 2; // Retrouver l'ancien sort_order
            });

        $updates = [
            1 => [
                'title' => 'Comment ta peau réagit-elle après le nettoyage ?',
                'question' => 'Pense à ce que tu ressens juste après avoir nettoyé ton visage, sans crème.',
            ],
            2 => [
                'title' => 'Ta peau a-t-elle tendance à briller en journée ?',
                'question' => 'Observe surtout ta zone T (front, nez, menton) en milieu de journée.',
            ],
            3 => [
                'title' => 'Quelle est ta préoccupation principale ?',
                'question' => 'Choisis celle qui te correspond le plus en ce moment.',
            ],
            4 => [
                'title' => 'As-tu les pores visiblement dilatés ?',
                'question' => 'Regarde de près ta peau dans un miroir, surtout sur le nez et les joues.',
            ],
            5 => [
                'title' => 'Ta peau est-elle parfois rugueuse ou desquame-t-elle ?',
                'question' => 'Cela peut se manifester par des tiraillements ou de petites peaux sèches.',
            ],
            6 => [
                'title' => 'Quelle est ta préoccupation principale ?',
                'question' => 'Même si tu en as plusieurs, choisis celle qui t\'embête le plus.',
            ],
            7 => [
                'title' => 'Quelle est ta préoccupation principale ?',
                'question' => 'Ta peau est plutôt équilibrée, mais as-tu un souci particulier ?',
            ],
            8 => [
                'title' => 'As-tu des imperfections ou boutons régulièrement ?',
                'question' => 'Petits boutons, points noirs ou imperfections récurrentes.',
            ],
            9 => [
                'title' => 'Quelle est ta préoccupation principale ?',
                'question' => 'Ta peau produit du sébum, mais tu peux avoir d\'autres soucis aussi.',
            ],
        ];

        foreach ($updates as $oldSort => $data) {
            if (isset($questions[$oldSort])) {
                DB::table('quiz_questions')
                    ->where('id', $questions[$oldSort]->id)
                    ->update($data);
            }
        }

        // ─── Améliorer les libellés des choix ───────────────────────────

        // Q1 (anciennement sort_order 1) : réaction après nettoyage
        if (isset($questions[1])) {
            $choices = DB::table('quiz_choices')
                ->where('question_id', $questions[1]->id)
                ->orderBy('sort_order')
                ->get();

            $newLabels = [
                'Oui, elle tire et est inconfortable',
                'Un peu, surtout sur les joues',
                'Pas du tout, elle est confortable',
            ];
            foreach ($choices as $i => $choice) {
                if (isset($newLabels[$i])) {
                    DB::table('quiz_choices')->where('id', $choice->id)->update(['label' => $newLabels[$i]]);
                }
            }
        }

        // Q2 : brille en journée
        if (isset($questions[2])) {
            $choices = DB::table('quiz_choices')
                ->where('question_id', $questions[2]->id)
                ->orderBy('sort_order')
                ->get();

            $newLabels = [
                'Non, ma peau reste mate',
                'Oui, surtout sur la zone T',
            ];
            foreach ($choices as $i => $choice) {
                if (isset($newLabels[$i])) {
                    DB::table('quiz_choices')->where('id', $choice->id)->update(['label' => $newLabels[$i]]);
                }
            }
        }

        // Q3, Q6, Q7, Q9 : préoccupations (enrichir les labels)
        $concernQuestions = [3, 6, 7, 9];
        $concernLabels = [
            'Rides' => 'Les rides et ridules',
            'Rougeurs' => 'Les rougeurs et sensibilités',
            'Taches' => 'Les taches et le teint irrégulier',
            'Boutons' => 'Les boutons et imperfections',
            'Rien' => 'Aucune en particulier',
            'Aucun' => 'Aucune en particulier',
        ];

        foreach ($concernQuestions as $oldSort) {
            if (isset($questions[$oldSort])) {
                $choices = DB::table('quiz_choices')
                    ->where('question_id', $questions[$oldSort]->id)
                    ->get();

                foreach ($choices as $choice) {
                    $trimmed = trim($choice->label);
                    if (isset($concernLabels[$trimmed])) {
                        DB::table('quiz_choices')
                            ->where('id', $choice->id)
                            ->update(['label' => $concernLabels[$trimmed]]);
                    }
                }
            }
        }

        // Q4 : pores dilatés
        if (isset($questions[4])) {
            $choices = DB::table('quiz_choices')
                ->where('question_id', $questions[4]->id)
                ->orderBy('sort_order')
                ->get();

            $newLabels = [
                'Oui, mes pores sont bien visibles',
                'Non, mes pores sont plutôt fins',
            ];
            foreach ($choices as $i => $choice) {
                if (isset($newLabels[$i])) {
                    DB::table('quiz_choices')->where('id', $choice->id)->update(['label' => $newLabels[$i]]);
                }
            }
        }

        // Q5 : rugueuse / desquame
        if (isset($questions[5])) {
            $choices = DB::table('quiz_choices')
                ->where('question_id', $questions[5]->id)
                ->orderBy('sort_order')
                ->get();

            $newLabels = [
                'Oui, j\'ai souvent des zones sèches ou qui pèlent',
                'Non, ma peau est lisse au toucher',
            ];
            foreach ($choices as $i => $choice) {
                if (isset($newLabels[$i])) {
                    DB::table('quiz_choices')->where('id', $choice->id)->update(['label' => $newLabels[$i]]);
                }
            }
        }

        // Q8 : boutons
        if (isset($questions[8])) {
            $choices = DB::table('quiz_choices')
                ->where('question_id', $questions[8]->id)
                ->orderBy('sort_order')
                ->get();

            $newLabels = [
                'Oui, assez souvent',
                'Non, rarement ou jamais',
            ];
            foreach ($choices as $i => $choice) {
                if (isset($newLabels[$i])) {
                    DB::table('quiz_choices')->where('id', $choice->id)->update(['label' => $newLabels[$i]]);
                }
            }
        }

        // ─── Remplir les descriptions des 19 résultats ──────────────────

        $results = [
            1 => [
                'title' => 'Peau Sèche Mature',
                'description' => '<p>Ta peau manque de lipides et montre des signes de l\'âge. Elle a besoin de soins riches et nourrissants qui renforcent la barrière cutanée tout en lissant les rides.</p><p><strong>Tes priorités :</strong> nutrition intense, anti-âge, protection contre la déshydratation.</p>',
            ],
            2 => [
                'title' => 'Peau Sèche Sensible',
                'description' => '<p>Ta peau est fine, réactive et manque de confort. Elle rougit facilement et tiraille après le nettoyage. Elle a besoin de douceur et de soins apaisants très riches.</p><p><strong>Tes priorités :</strong> apaiser, nourrir, protéger la barrière cutanée.</p>',
            ],
            3 => [
                'title' => 'Peau Sèche Hyperpigmentée',
                'description' => '<p>Ta peau est sèche et présente des taches pigmentaires ou un teint irrégulier. Elle a besoin d\'hydratation et de soins ciblés pour unifier le teint.</p><p><strong>Tes priorités :</strong> hydratation, éclat, correction des taches.</p>',
            ],
            4 => [
                'title' => 'Peau Sèche',
                'description' => '<p>Ta peau manque de lipides au quotidien. Elle tiraille, peut être inconfortable mais n\'a pas de souci particulier. Une bonne routine nourrissante fera toute la différence.</p><p><strong>Tes priorités :</strong> nutrition, confort, protection.</p>',
            ],
            5 => [
                'title' => 'Peau Déshydratée Mature',
                'description' => '<p>Ta peau manque d\'eau (pas de gras) et montre des signes de l\'âge. Les ridules de déshydratation se mêlent aux rides d\'expression. Un bon soin hydratant anti-âge est essentiel.</p><p><strong>Tes priorités :</strong> hydratation profonde, anti-âge, repulper.</p>',
            ],
            6 => [
                'title' => 'Peau Déshydratée Hyperpigmentée',
                'description' => '<p>Ta peau manque d\'eau et présente des taches ou un teint terne. Elle a besoin d\'un boost d\'hydratation combiné à des actifs éclaircissants.</p><p><strong>Tes priorités :</strong> hydratation, éclat, uniformité du teint.</p>',
            ],
            7 => [
                'title' => 'Peau Déshydratée Sensible',
                'description' => '<p>Ta peau est réactive et en manque d\'eau. Elle peut tirailler tout en étant sujette aux rougeurs. Elle a besoin de soins hydratants doux et apaisants.</p><p><strong>Tes priorités :</strong> hydratation douce, apaisement, renforcement cutané.</p>',
            ],
            8 => [
                'title' => 'Peau Déshydratée à tendance Grasse',
                'description' => '<p>Ta peau manque d\'eau mais produit du sébum pour compenser. Résultat : elle brille tout en étant inconfortable. Il faut hydrater sans alourdir.</p><p><strong>Tes priorités :</strong> hydratation légère, équilibre, textures fluides.</p>',
            ],
            9 => [
                'title' => 'Peau Normale Mature',
                'description' => '<p>Ta peau est bien équilibrée mais montre des signes de l\'âge. C\'est le moment idéal pour agir avec des soins anti-âge préventifs ou correcteurs.</p><p><strong>Tes priorités :</strong> prévention, fermeté, éclat.</p>',
            ],
            10 => [
                'title' => 'Peau Normale Hyperpigmentée',
                'description' => '<p>Ta peau est équilibrée mais présente des taches ou un teint irrégulier, souvent liés au soleil ou aux variations hormonales. Des soins ciblés éclat feront la différence.</p><p><strong>Tes priorités :</strong> uniformité du teint, éclat, protection solaire.</p>',
            ],
            11 => [
                'title' => 'Peau Normale Sensible',
                'description' => '<p>Ta peau est globalement équilibrée mais réactive : elle rougit facilement face au froid, au stress ou à certains produits. Elle a besoin de soins doux et protecteurs.</p><p><strong>Tes priorités :</strong> apaisement, protection, douceur.</p>',
            ],
            12 => [
                'title' => 'Peau Normale à tendance Grasse',
                'description' => '<p>Ta peau est plutôt équilibrée mais a tendance à briller et à développer quelques imperfections. Des soins légers et matifiants t\'aideront à garder un teint net.</p><p><strong>Tes priorités :</strong> équilibre, matité, soin léger.</p>',
            ],
            13 => [
                'title' => 'Peau Grasse Acnéique',
                'description' => '<p>Ta peau produit beaucoup de sébum et est sujette aux boutons et imperfections. Elle a besoin de soins purifiants mais non agressifs pour rétablir l\'équilibre.</p><p><strong>Tes priorités :</strong> purifier, réguler le sébum, apaiser les inflammations.</p>',
            ],
            14 => [
                'title' => 'Peau Grasse Mature',
                'description' => '<p>Ta peau brille et produit du sébum mais montre aussi des signes de l\'âge. Il faut des soins anti-âge légers qui ne vont pas aggraver la brillance.</p><p><strong>Tes priorités :</strong> anti-âge, régulation du sébum, textures légères.</p>',
            ],
            15 => [
                'title' => 'Peau Grasse Sensible',
                'description' => '<p>Ta peau brille et est en même temps réactive. C\'est un équilibre délicat : il faut purifier sans irriter. Privilégie les soins doux et régulateurs.</p><p><strong>Tes priorités :</strong> purifier en douceur, apaiser, réguler.</p>',
            ],
            16 => [
                'title' => 'Peau Grasse Hyperpigmentée',
                'description' => '<p>Ta peau produit du sébum et présente des taches ou marques post-inflammatoires. Les soins doivent cibler l\'excès de sébum tout en travaillant l\'éclat du teint.</p><p><strong>Tes priorités :</strong> régulation, éclat, correction des taches.</p>',
            ],
            17 => [
                'title' => 'Peau Déshydratée',
                'description' => '<p>Ta peau manque d\'eau mais n\'a pas de souci majeur. Des soins hydratants bien choisis et une bonne routine suffiront à lui redonner confort et éclat.</p><p><strong>Tes priorités :</strong> hydratation, confort, barrière cutanée.</p>',
            ],
            18 => [
                'title' => 'Peau Normale',
                'description' => '<p>Bravo, ta peau est bien équilibrée ! Pas de souci particulier, mais une bonne routine d\'entretien la gardera belle longtemps. Profites-en pour prévenir.</p><p><strong>Tes priorités :</strong> entretien, prévention, éclat.</p>',
            ],
            19 => [
                'title' => 'Peau Grasse',
                'description' => '<p>Ta peau brille et a les pores dilatés mais pas de souci majeur associé. Des soins matifiants et régulateurs suffiront à maintenir un teint net et frais.</p><p><strong>Tes priorités :</strong> matité, régulation du sébum, nettoyage doux.</p>',
            ],
        ];

        foreach ($results as $points => $data) {
            DB::table('quiz_results')
                ->where('quiz_id', $quizId)
                ->where('points_min', '<=', $points)
                ->where('points_max', '>=', $points)
                ->update(['description' => $data['description']]);
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
        // Les questions ajoutées et les mises à jour de texte ne sont pas
        // facilement réversibles — recréer depuis le WP import si nécessaire.
    }
};
