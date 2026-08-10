<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $articles = [
            [
                'title' => 'Comment connaître son type de peau ?',
                'slug' => 'comment-connaitre-son-type-de-peau',
                'excerpt' => 'Peau grasse, sèche, mixte ou sensible ? Apprenez à identifier votre type de peau pour choisir les soins les plus adaptés à vos besoins.',
                'meta_title' => 'Comment connaître son type de peau ? Guide complet',
                'meta_description' => 'Apprenez à identifier votre type de peau (grasse, sèche, mixte, sensible) pour adapter votre routine beauté. Conseils d\'esthéticienne.',
                'published_at' => '2026-01-10 09:00:00',
                'content' => '<h2>Pourquoi connaître son type de peau ?</h2>
<p>Choisir les bons soins commence par une étape essentielle : <strong>identifier son type de peau</strong>. Un sérum trop riche sur une peau grasse ou une crème trop légère sur une peau sèche peut aggraver les déséquilibres au lieu de les corriger.</p>
<p>En tant qu\'esthéticienne, c\'est la première question que je pose à chaque cliente. Et la réponse n\'est pas toujours celle qu\'on croit !</p>

<h2>Les 4 types de peau</h2>

<h3>Peau normale</h3>
<p>C\'est la peau dite « idéale » : ni trop grasse, ni trop sèche. Le grain de peau est fin et régulier, le teint est lumineux. Elle tolère bien la plupart des produits. C\'est le type de peau le plus rare.</p>

<h3>Peau sèche</h3>
<p>Elle manque de lipides (sébum) et parfois d\'eau. Les signes caractéristiques :</p>
<ul>
<li>Tiraillements, surtout après le nettoyage</li>
<li>Desquamations (petites peaux qui se détachent)</li>
<li>Teint terne, manque d\'éclat</li>
<li>Rides et ridules plus marquées</li>
</ul>

<h3>Peau grasse</h3>
<p>Elle produit trop de sébum. Le grain de peau est souvent épais, les pores dilatés sont visibles, surtout sur la zone T (front, nez, menton). La peau brille, en particulier en milieu de journée.</p>

<h3>Peau mixte</h3>
<p>C\'est le type de peau le plus courant. La zone T est grasse (pores visibles, brillance) tandis que les joues sont normales à sèches. Elle demande des soins ciblés selon les zones.</p>

<h2>Le test simple à faire chez soi</h2>
<p>Pour déterminer votre type de peau, suivez cette méthode :</p>
<ul>
<li><strong>Nettoyez</strong> votre visage avec un nettoyant doux</li>
<li><strong>N\'appliquez rien</strong> pendant 30 minutes</li>
<li><strong>Observez</strong> votre peau dans un miroir en lumière naturelle</li>
<li>Passez un <strong>mouchoir en papier</strong> sur la zone T, puis sur les joues</li>
</ul>
<p>Si le mouchoir est gras partout : peau grasse. Gras uniquement sur la zone T : peau mixte. Rien sur le mouchoir et tiraillements : peau sèche. Rien sur le mouchoir et confort : peau normale.</p>

<h2>Type de peau et état de peau : quelle différence ?</h2>
<p>Votre type de peau est <strong>génétique et permanent</strong>. En revanche, l\'état de votre peau change selon les saisons, le stress, les hormones ou les produits utilisés. Une peau grasse peut être déshydratée, une peau sèche peut devenir sensible.</p>
<p>C\'est pourquoi un <strong>diagnostic de peau professionnel</strong> est si précieux : il permet de distinguer le type de l\'état et d\'adapter les soins en conséquence.</p>

<h2>Les soins adaptés à chaque type de peau</h2>
<ul>
<li><strong>Peau sèche</strong> : privilégiez des textures riches, des huiles végétales nourrissantes et des actifs comme l\'acide hyaluronique</li>
<li><strong>Peau grasse</strong> : optez pour des textures légères, des actifs matifiants et un nettoyage doux (jamais agressif !)</li>
<li><strong>Peau mixte</strong> : appliquez des soins différents selon les zones, ou choisissez des produits équilibrants</li>
<li><strong>Peau sensible</strong> : recherchez des formulations minimalistes, sans parfum, avec des actifs apaisants</li>
</ul>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Pourquoi hydrater sa peau même lorsqu\'elle est grasse ?',
                'slug' => 'pourquoi-hydrater-peau-grasse',
                'excerpt' => 'Contrairement aux idées reçues, une peau grasse a besoin d\'hydratation. Découvrez pourquoi et comment bien hydrater sans briller.',
                'meta_title' => 'Peau grasse : pourquoi l\'hydrater quand même ?',
                'meta_description' => 'Une peau grasse a aussi besoin d\'hydratation. Découvrez pourquoi sauter cette étape aggrave la brillance et comment bien hydrater une peau grasse.',
                'published_at' => '2026-01-24 09:00:00',
                'content' => '<h2>L\'erreur la plus courante</h2>
<p>« Ma peau brille, donc elle n\'a pas besoin de crème. » C\'est probablement le <strong>mythe beauté le plus tenace</strong> que j\'entends à l\'institut. Et pourtant, c\'est tout l\'inverse.</p>
<p>Quand on prive une peau grasse d\'hydratation, elle interprète cette sécheresse comme un signal d\'alarme et <strong>produit encore plus de sébum</strong> pour compenser. C\'est un cercle vicieux.</p>

<h2>Hydratation et nutrition : deux choses différentes</h2>
<p>Il est essentiel de comprendre la différence :</p>
<ul>
<li><strong>Hydrater</strong>, c\'est apporter de l\'eau à la peau (actifs hydratants : acide hyaluronique, glycérine, aloe vera)</li>
<li><strong>Nourrir</strong>, c\'est apporter des lipides (huiles, beurres, céramides)</li>
</ul>
<p>Une peau grasse a souvent <strong>suffisamment de lipides</strong> mais peut être <strong>déshydratée</strong> — c\'est-à-dire en manque d\'eau. Les signes : tiraillements malgré la brillance, grain de peau irrégulier, teint brouillé.</p>

<h2>Comment bien hydrater une peau grasse ?</h2>

<h3>1. Choisir la bonne texture</h3>
<p>Oubliez les crèmes épaisses et les baumes. Privilégiez :</p>
<ul>
<li>Les <strong>fluides hydratants</strong> légers</li>
<li>Les <strong>gels-crèmes</strong> à absorption rapide</li>
<li>Les <strong>sérums aqueux</strong> à l\'acide hyaluronique</li>
</ul>

<h3>2. Éviter les produits asséchants</h3>
<p>Les nettoyants agressifs, les lotions alcoolisées et les gommages trop fréquents <strong>stimulent la production de sébum</strong>. Plus vous décapez, plus votre peau brille.</p>

<h3>3. Ne pas sauter l\'étape hydratation</h3>
<p>Même en été, même si votre peau semble luisante le matin, l\'hydratation reste indispensable. Appliquez votre soin sur peau propre, matin et soir.</p>

<h2>Les actifs à privilégier</h2>
<ul>
<li><strong>Acide hyaluronique</strong> : hydrate sans graisser, convient à tous les types de peau</li>
<li><strong>Niacinamide (vitamine B3)</strong> : régule le sébum et resserre les pores</li>
<li><strong>Zinc</strong> : purifiant et anti-inflammatoire</li>
<li><strong>Aloe vera</strong> : hydrate et apaise les imperfections</li>
</ul>

<h2>Mon conseil d\'esthéticienne</h2>
<p>Si votre peau brille malgré une bonne hydratation, le problème vient peut-être de votre <strong>routine de nettoyage</strong> ou de facteurs internes (alimentation, stress, hormones). Un diagnostic de peau en institut permet d\'identifier la cause exacte.</p>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Les bienfaits des huiles végétales pour la peau',
                'slug' => 'bienfaits-huiles-vegetales-peau',
                'excerpt' => 'Argan, jojoba, rose musquée… Les huiles végétales sont de véritables trésors pour la peau. Découvrez leurs bienfaits et comment les utiliser.',
                'meta_title' => 'Les bienfaits des huiles végétales pour la peau',
                'meta_description' => 'Huile d\'argan, jojoba, rose musquée : découvrez les bienfaits des huiles végétales pour la peau et comment les intégrer dans votre routine beauté.',
                'published_at' => '2026-02-07 09:00:00',
                'content' => '<h2>Pourquoi les huiles végétales sont-elles si efficaces ?</h2>
<p>Contrairement aux huiles minérales (issues du pétrole), les <strong>huiles végétales</strong> ont une composition proche des lipides naturels de la peau. Elles sont riches en acides gras essentiels, en vitamines et en antioxydants que notre peau sait parfaitement assimiler.</p>
<p>Elles nourrissent en profondeur, réparent la barrière cutanée et protègent contre les agressions extérieures. Et non, une huile végétale de qualité <strong>ne bouche pas les pores</strong>.</p>

<h2>Les huiles incontournables</h2>

<h3>Huile d\'argan</h3>
<p>Originaire du Maroc, l\'huile d\'argan est un <strong>concentré de vitamine E et d\'oméga-6</strong>. Elle nourrit intensément les peaux sèches, atténue les ridules et redonne de l\'éclat. En soin corps, elle est parfaite pour les vergetures et les zones très sèches (coudes, talons).</p>

<h3>Huile de jojoba</h3>
<p>Techniquement, c\'est une cire liquide. Sa composition est très proche du sébum humain, ce qui en fait un allié idéal pour :</p>
<ul>
<li>Les peaux grasses (elle régule le sébum)</li>
<li>Les peaux mixtes (elle rééquilibre)</li>
<li>Le démaquillage naturel</li>
</ul>

<h3>Huile de rose musquée</h3>
<p>Riche en vitamine A (rétinol naturel) et en acides gras, elle est la star des soins <strong>anti-âge et anti-taches</strong>. Elle stimule la régénération cellulaire et atténue les cicatrices, les vergetures et les taches pigmentaires.</p>

<h3>Huile de nigelle</h3>
<p>Moins connue mais redoutablement efficace, l\'huile de nigelle est <strong>anti-inflammatoire et purifiante</strong>. Elle est particulièrement recommandée pour les peaux à imperfections, l\'eczéma et le psoriasis.</p>

<h2>Comment les utiliser ?</h2>
<ul>
<li><strong>En soin visage</strong> : quelques gouttes le soir, après votre sérum, en remplacement ou en complément de votre crème de nuit</li>
<li><strong>En soin corps</strong> : appliquez après la douche sur peau encore humide pour une pénétration optimale</li>
<li><strong>En démaquillage</strong> : l\'huile de jojoba ou l\'huile d\'amande douce dissolvent parfaitement le maquillage, même waterproof</li>
<li><strong>En soin capillaire</strong> : en masque avant-shampoing pour nourrir les longueurs</li>
</ul>

<h2>Comment choisir une huile végétale de qualité ?</h2>
<p>Trois critères essentiels :</p>
<ul>
<li><strong>Première pression à froid</strong> : c\'est le procédé qui préserve le mieux les nutriments</li>
<li><strong>Biologique</strong> : les huiles conventionnelles peuvent contenir des résidus de pesticides</li>
<li><strong>Pure, sans additifs</strong> : vérifiez la liste INCI, il ne doit y avoir qu\'un seul ingrédient</li>
</ul>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Routine beauté naturelle en hiver',
                'slug' => 'routine-beaute-naturelle-hiver',
                'excerpt' => 'Le froid, le vent et le chauffage agressent la peau en hiver. Voici comment adapter votre routine pour garder une peau douce et protégée.',
                'meta_title' => 'Routine beauté naturelle en hiver : les bons gestes',
                'meta_description' => 'Adaptez votre routine beauté à l\'hiver : nettoyage, hydratation, protection. Les conseils d\'une esthéticienne pour protéger votre peau du froid.',
                'published_at' => '2026-02-21 09:00:00',
                'content' => '<h2>Pourquoi la peau souffre en hiver ?</h2>
<p>En hiver, votre peau subit une <strong>double agression</strong> : le froid et le vent à l\'extérieur, l\'air sec du chauffage à l\'intérieur. Résultat : la barrière cutanée s\'affaiblit, la peau perd en hydratation, tiraille, rougit et perd de son éclat.</p>
<p>Même les peaux grasses peuvent se retrouver déshydratées en hiver. C\'est le moment de renforcer votre routine.</p>

<h2>Les 5 étapes de la routine hivernale</h2>

<h3>1. Un nettoyage tout en douceur</h3>
<p>Rangez les gels moussants agressifs. En hiver, privilégiez :</p>
<ul>
<li>Un <strong>lait démaquillant</strong> onctueux</li>
<li>Une <strong>huile démaquillante</strong> qui nourrit en nettoyant</li>
<li>Une <strong>eau micellaire douce</strong> (sans alcool)</li>
</ul>
<p>Le matin, un simple rinçage à l\'eau tiède peut suffire si votre peau est sèche.</p>

<h3>2. Un sérum concentré</h3>
<p>Le sérum est votre meilleur allié en hiver. Il pénètre plus profondément qu\'une crème et apporte une dose concentrée d\'actifs. Choisissez-le en fonction de votre besoin principal :</p>
<ul>
<li><strong>Acide hyaluronique</strong> pour l\'hydratation intense</li>
<li><strong>Vitamine C</strong> pour l\'éclat et la protection antioxydante</li>
<li><strong>Niacinamide</strong> pour renforcer la barrière cutanée</li>
</ul>

<h3>3. Une crème riche et protectrice</h3>
<p>Passez à une crème plus <strong>riche et enveloppante</strong> que votre soin d\'été. Elle doit former un véritable bouclier contre le froid tout en maintenant l\'hydratation. Les textures baume ou cold cream sont idéales pour les peaux très sèches.</p>

<h3>4. Un soin pour les lèvres</h3>
<p>Les lèvres n\'ont pas de glandes sébacées : elles se dessèchent très vite en hiver. Appliquez un <strong>baume à lèvres nourrissant</strong> plusieurs fois par jour, à base de beurre de karité, de cire d\'abeille ou d\'huile de coco.</p>

<h3>5. Un soin pour les mains</h3>
<p>Les mains sont les grandes oubliées de la routine. Pourtant, elles sont exposées en permanence. Gardez une <strong>crème mains</strong> dans votre sac et appliquez-la après chaque lavage.</p>

<h2>Les gestes à éviter en hiver</h2>
<ul>
<li><strong>L\'eau très chaude</strong> sous la douche (elle déshydrate la peau)</li>
<li><strong>Les gommages mécaniques trop fréquents</strong> (une fois par semaine maximum)</li>
<li><strong>Les produits à base d\'alcool</strong> (toniques, lotions)</li>
<li><strong>Oublier la protection solaire</strong> (les UV sont présents même en hiver, surtout en altitude)</li>
</ul>

<h2>Mon rituel du dimanche soir</h2>
<p>Une fois par semaine, offrez à votre peau un moment de soin complet : un gommage doux suivi d\'un masque hydratant (20 minutes), puis votre sérum et votre crème. Votre peau abordera la semaine en pleine forme.</p>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Comment choisir son sérum visage ?',
                'slug' => 'comment-choisir-serum-visage',
                'excerpt' => 'Le sérum est devenu l\'indispensable de la routine beauté. Mais face à la multitude de choix, comment trouver celui qu\'il vous faut ?',
                'meta_title' => 'Comment choisir son sérum visage ? Guide par type de peau',
                'meta_description' => 'Hydratant, anti-âge, éclat, purifiant… Comment choisir le bon sérum visage ? Guide complet par type de peau et objectif beauté.',
                'published_at' => '2026-03-07 09:00:00',
                'content' => '<h2>Qu\'est-ce qu\'un sérum et pourquoi est-il indispensable ?</h2>
<p>Le sérum est un soin <strong>ultra-concentré en actifs</strong>. Contrairement à une crème qui hydrate et protège en surface, le sérum pénètre plus profondément dans la peau pour traiter un problème spécifique : rides, taches, manque d\'éclat, imperfections…</p>
<p>Sa texture fluide et légère le rend agréable à appliquer et compatible avec tous les types de peau. Il ne remplace pas la crème : il la <strong>complète et en décuple l\'efficacité</strong>.</p>

<h2>Quel sérum pour quel besoin ?</h2>

<h3>Pour hydrater en profondeur</h3>
<p>L\'actif star : <strong>l\'acide hyaluronique</strong>. Il agit comme une éponge capable de retenir jusqu\'à 1 000 fois son poids en eau. Résultat : la peau est repulpée, lisse et confortable. C\'est le sérum universel qui convient à tous les types de peau.</p>

<h3>Pour lutter contre les rides</h3>
<p>Les actifs anti-âge les plus efficaces :</p>
<ul>
<li><strong>Rétinol</strong> (vitamine A) : stimule le renouvellement cellulaire et la production de collagène</li>
<li><strong>Vitamine C</strong> : antioxydant puissant qui prévient le vieillissement et unifie le teint</li>
<li><strong>Peptides</strong> : renforcent la structure de la peau</li>
</ul>

<h3>Pour retrouver de l\'éclat</h3>
<p>La <strong>vitamine C</strong> est imbattable pour redonner du peps à un teint terne. Elle neutralise les radicaux libres, stimule la production de collagène et atténue les taches pigmentaires. Appliquez-la le matin, avant votre crème solaire.</p>

<h3>Pour purifier et réguler</h3>
<p>Si votre peau est sujette aux imperfections, tournez-vous vers :</p>
<ul>
<li><strong>Niacinamide</strong> (vitamine B3) : régule le sébum, resserre les pores et réduit les rougeurs</li>
<li><strong>Acide salicylique</strong> (BHA) : exfolie en douceur et débouche les pores</li>
<li><strong>Zinc</strong> : antiseptique et anti-inflammatoire</li>
</ul>

<h2>Comment l\'appliquer correctement ?</h2>
<ul>
<li>Appliquez le sérum sur peau <strong>propre et légèrement humide</strong></li>
<li>Quelques gouttes suffisent (3 à 5 selon les produits)</li>
<li>Chauffez-le entre vos paumes puis <strong>pressez doucement</strong> sur le visage (ne frottez pas)</li>
<li>Attendez 1 à 2 minutes avant d\'appliquer votre crème</li>
</ul>

<h2>Peut-on superposer plusieurs sérums ?</h2>
<p>Oui, mais avec méthode. La règle : du plus fluide au plus épais, et jamais plus de deux sérums. Par exemple, un sérum à l\'acide hyaluronique suivi d\'un sérum à la vitamine C le matin. Le soir, un sérum au rétinol seul (il ne s\'associe pas avec la vitamine C).</p>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Pourquoi faire un diagnostic de peau ?',
                'slug' => 'pourquoi-faire-diagnostic-de-peau',
                'excerpt' => 'Un diagnostic de peau professionnel est la clé pour une routine vraiment efficace. Découvrez ce que ça change et comment ça se passe.',
                'meta_title' => 'Pourquoi faire un diagnostic de peau chez une esthéticienne ?',
                'meta_description' => 'Le diagnostic de peau professionnel : pourquoi c\'est essentiel, comment ça se déroule et ce que ça change dans votre routine beauté.',
                'published_at' => '2026-03-21 09:00:00',
                'content' => '<h2>Votre peau est unique</h2>
<p>Nous avons toutes et tous une peau différente. Votre collègue jure par sa crème miracle, mais sur vous, elle provoque des boutons. Votre amie adore son nettoyant moussant, mais chez vous, il tire la peau. <strong>Ce qui fonctionne pour l\'une ne fonctionne pas forcément pour l\'autre</strong>, et c\'est normal.</p>
<p>Un diagnostic de peau professionnel permet de comprendre <strong>votre</strong> peau : ses forces, ses faiblesses, et les soins qu\'elle réclame vraiment.</p>

<h2>Ce que révèle un diagnostic professionnel</h2>
<p>En institut, le diagnostic de peau va bien au-delà du simple « type de peau ». Il analyse :</p>
<ul>
<li>Le <strong>type de peau</strong> (grasse, sèche, mixte, normale)</li>
<li>L\'<strong>état de la peau</strong> (déshydratée, sensibilisée, terne, acnéique…)</li>
<li>La <strong>qualité de la barrière cutanée</strong></li>
<li>Le <strong>niveau d\'hydratation</strong> réel (qui diffère souvent de la sensation)</li>
<li>Les <strong>signes de vieillissement</strong> : rides, taches, perte de fermeté</li>
<li>Les <strong>facteurs aggravants</strong> : alimentation, stress, soleil, tabac</li>
</ul>

<h2>Comment se déroule un diagnostic ?</h2>
<p>À l\'institut, le diagnostic se fait en plusieurs étapes :</p>
<ul>
<li><strong>Échange</strong> sur vos habitudes, vos produits actuels, vos préoccupations</li>
<li><strong>Observation</strong> de la peau à la lumière et au toucher</li>
<li><strong>Analyse</strong> visuelle des zones (joues, zone T, contour des yeux, cou)</li>
<li><strong>Recommandations</strong> personnalisées : routine, produits, soins en institut</li>
</ul>
<p>C\'est un moment d\'échange, pas un examen médical. L\'objectif est de vous donner les clés pour <strong>prendre soin de votre peau au quotidien</strong> avec les bons gestes et les bons produits.</p>

<h2>Quand faire un diagnostic de peau ?</h2>
<p>Idéalement :</p>
<ul>
<li>Quand vous <strong>commencez</strong> à vous intéresser aux soins (quel que soit votre âge)</li>
<li>Quand votre peau <strong>change</strong> (grossesse, ménopause, changement de saison, stress…)</li>
<li>Quand vos produits habituels <strong>ne fonctionnent plus</strong></li>
<li>Quand vous avez des <strong>problèmes persistants</strong> (acné adulte, rougeurs, taches…)</li>
<li>Au moins <strong>une fois par an</strong>, comme un bilan santé de votre peau</li>
</ul>

<h2>Le diagnostic en ligne</h2>
<p>Si vous ne pouvez pas vous déplacer à l\'institut, notre <a href="/diagnostic-de-peau">diagnostic de peau en ligne</a> vous donne déjà de très bonnes indications sur votre type de peau et les produits les plus adaptés.</p>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Les erreurs qui accélèrent le vieillissement cutané',
                'slug' => 'erreurs-accelerent-vieillissement-cutane',
                'excerpt' => 'Certaines habitudes quotidiennes accélèrent le vieillissement de la peau sans qu\'on s\'en rende compte. Voici les erreurs les plus courantes.',
                'meta_title' => 'Les erreurs qui accélèrent le vieillissement de la peau',
                'meta_description' => 'Découvrez les 7 erreurs qui accélèrent le vieillissement cutané et comment les corriger pour garder une peau jeune et éclatante plus longtemps.',
                'published_at' => '2026-04-04 09:00:00',
                'content' => '<h2>Le vieillissement cutané n\'est pas une fatalité</h2>
<p>Si le vieillissement de la peau est un processus naturel, <strong>80 % des signes visibles de l\'âge sont liés à des facteurs externes</strong> que l\'on peut contrôler. Autrement dit, nos habitudes quotidiennes ont un impact considérable sur la jeunesse de notre peau.</p>
<p>Voici les erreurs les plus fréquentes que je constate en institut — et surtout, comment les corriger.</p>

<h2>Erreur n°1 : négliger la protection solaire</h2>
<p>C\'est <strong>le facteur n°1 du vieillissement prématuré</strong>. Les rayons UV dégradent le collagène et l\'élastine, provoquent des taches pigmentaires et accélèrent l\'apparition des rides. Et non, ce n\'est pas réservé à l\'été : les UV sont présents toute l\'année, même par temps nuageux.</p>
<p><strong>Le bon réflexe</strong> : appliquer une protection solaire SPF 30 minimum chaque matin, en dernière étape de votre routine, même en ville.</p>

<h2>Erreur n°2 : ne pas hydrater suffisamment</h2>
<p>Une peau déshydratée vieillit plus vite. Le manque d\'eau accentue les ridules, ternit le teint et affaiblit la barrière cutanée. L\'hydratation doit être <strong>quotidienne, matin et soir</strong>, avec des produits adaptés à votre type de peau.</p>

<h2>Erreur n°3 : dormir avec son maquillage</h2>
<p>La nuit, la peau se régénère. Si elle est recouverte de maquillage, de sébum et de pollution, ce processus de réparation est bloqué. Conséquence : teint terne, pores obstrués, vieillissement accéléré. <strong>Le démaquillage est non négociable</strong>, même les soirs de grande fatigue.</p>

<h2>Erreur n°4 : fumer</h2>
<p>Le tabac est l\'un des pires ennemis de la peau. Il réduit l\'apport en oxygène, dégrade le collagène et provoque un teint grisâtre. Les fumeuses développent en moyenne <strong>5 fois plus de rides</strong> que les non-fumeuses du même âge.</p>

<h2>Erreur n°5 : utiliser des produits trop agressifs</h2>
<p>Gommages quotidiens, nettoyants décapants, peelings chimiques mal dosés… Trop exfolier ou trop décaper <strong>fragilise la barrière cutanée</strong> et provoque une inflammation chronique qui accélère le vieillissement. La clé, c\'est la douceur.</p>

<h2>Erreur n°6 : négliger le contour des yeux</h2>
<p>La peau du contour de l\'œil est <strong>5 fois plus fine</strong> que celle du reste du visage. C\'est là que les premiers signes de l\'âge apparaissent. Un soin contour des yeux spécifique n\'est pas un luxe, c\'est une nécessité dès 25-30 ans.</p>

<h2>Erreur n°7 : oublier le cou et le décolleté</h2>
<p>Quand vous appliquez votre sérum et votre crème, descendez jusqu\'au décolleté. Ces zones sont souvent négligées mais vieillissent très vite car la peau y est fine et rarement protégée du soleil.</p>

<h2>Les bons réflexes anti-âge</h2>
<ul>
<li>Protection solaire <strong>quotidienne</strong></li>
<li>Hydratation matin et soir</li>
<li>Démaquillage soigneux chaque soir</li>
<li>Antioxydants (vitamine C, vitamine E) dans votre routine</li>
<li>Sommeil suffisant (7-8 heures)</li>
<li>Alimentation riche en fruits, légumes et oméga-3</li>
</ul>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Comment réussir sa routine anti-âge ?',
                'slug' => 'comment-reussir-routine-anti-age',
                'excerpt' => 'Une routine anti-âge efficace ne se limite pas à une crème. Découvrez les étapes essentielles et les actifs à intégrer selon votre âge.',
                'meta_title' => 'Routine anti-âge : les étapes essentielles à tout âge',
                'meta_description' => 'Comment construire une routine anti-âge efficace ? Les bons actifs (rétinol, vitamine C, acide hyaluronique) et les étapes essentielles par tranche d\'âge.',
                'published_at' => '2026-04-18 09:00:00',
                'content' => '<h2>Quand commencer une routine anti-âge ?</h2>
<p>La question n\'est pas « suis-je trop jeune ? » mais « est-ce que ma peau en a besoin ? ». La prévention est toujours plus efficace que la correction. <strong>Dès 25 ans</strong>, les premières mesures préventives sont pertinentes. Dès 30-35 ans, une routine anti-âge structurée devient vraiment bénéfique.</p>
<p>Mais il n\'est <strong>jamais trop tard</strong> pour commencer. Même à 50 ou 60 ans, les bons produits et les bons gestes font une différence visible.</p>

<h2>Les 4 piliers de la routine anti-âge</h2>

<h3>1. Le nettoyage doux</h3>
<p>Tout commence par une peau propre. Mais attention : un nettoyage trop agressif fragilise la barrière cutanée, ce qui aggrave le vieillissement. Privilégiez un <strong>lait, une huile ou un baume démaquillant</strong> suivi d\'un nettoyant doux au pH physiologique.</p>

<h3>2. Les actifs ciblés (sérums)</h3>
<p>C\'est le cœur de la routine anti-âge. Les sérums délivrent les actifs en concentration optimale :</p>
<ul>
<li><strong>Le matin</strong> : vitamine C (antioxydant + éclat) ou niacinamide (anti-taches + fermeté)</li>
<li><strong>Le soir</strong> : rétinol (renouvellement cellulaire) ou acides de fruits AHA (exfoliation douce)</li>
</ul>
<p>L\'acide hyaluronique peut être utilisé matin et soir pour maintenir une hydratation optimale.</p>

<h3>3. La crème hydratante adaptée</h3>
<p>Votre crème doit hydrater et protéger. Avec l\'âge, la peau produit moins de sébum : optez pour des textures plus riches, enrichies en <strong>céramides</strong> (réparation de la barrière), en <strong>peptides</strong> (stimulation du collagène) ou en <strong>squalane</strong> (nutrition sans occlusion).</p>

<h3>4. La protection solaire</h3>
<p>Aucune routine anti-âge ne fonctionne sans protection solaire. Les UV sont responsables de 80 % du vieillissement visible. Un SPF 30 minimum le matin, tous les jours, c\'est <strong>le geste anti-âge le plus efficace qui existe</strong>.</p>

<h2>L\'anti-âge selon votre âge</h2>

<h3>25-30 ans : la prévention</h3>
<ul>
<li>Hydratation quotidienne (acide hyaluronique)</li>
<li>Protection solaire</li>
<li>Antioxydants (vitamine C)</li>
</ul>

<h3>30-40 ans : la correction douce</h3>
<ul>
<li>Introduction du rétinol (commencer doucement, 2 fois par semaine)</li>
<li>Soin contour des yeux</li>
<li>Exfoliation douce régulière</li>
</ul>

<h3>40-50 ans et au-delà : la régénération</h3>
<ul>
<li>Rétinol ou acides de fruits réguliers</li>
<li>Peptides et céramides</li>
<li>Soins en institut (massages liftants, soins éclat)</li>
<li>Huiles végétales nourrissantes le soir</li>
</ul>

<h2>Mon conseil d\'esthéticienne</h2>
<p>N\'essayez pas d\'introduire tous les actifs en même temps. Commencez par un, observez comment votre peau réagit pendant 3-4 semaines, puis ajoutez le suivant. La patience est la clé d\'une routine anti-âge réussie.</p>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Pourquoi j\'aime les soins Charme d\'Orient',
                'slug' => 'pourquoi-jaime-soins-charme-orient',
                'excerpt' => 'Charme d\'Orient, c\'est l\'alliance du luxe oriental et du naturel. Je vous raconte pourquoi j\'ai choisi cette marque pour l\'institut.',
                'meta_title' => 'Charme d\'Orient : pourquoi j\'aime cette marque de soins',
                'meta_description' => 'Découvrez pourquoi j\'ai choisi Charme d\'Orient pour l\'institut : ingrédients naturels, rituels orientaux, résultats visibles. Mon avis d\'esthéticienne.',
                'published_at' => '2026-05-02 09:00:00',
                'content' => '<h2>Ma rencontre avec Charme d\'Orient</h2>
<p>Quand j\'ai découvert Charme d\'Orient lors d\'un salon professionnel, j\'ai été immédiatement séduite par la <strong>qualité des textures et la richesse des parfums</strong>. Mais c\'est en testant les produits en cabine que j\'ai compris : cette marque ne se contente pas de sentir bon, elle <strong>transforme réellement la peau</strong>.</p>
<p>Depuis, Charme d\'Orient fait partie intégrante de mes protocoles de soins à l\'institut.</p>

<h2>Une marque aux racines orientales</h2>
<p>Charme d\'Orient s\'inspire des <strong>rituels de beauté millénaires du Moyen-Orient et du Maghreb</strong>. Le hammam, le savon noir, le rhassoul, l\'huile d\'argan, l\'eau de rose… Autant de trésors que la marque a su moderniser tout en respectant leurs vertus ancestrales.</p>
<p>Chaque produit raconte une histoire et transporte dans un véritable voyage sensoriel.</p>

<h2>Ce que j\'aime chez Charme d\'Orient</h2>

<h3>Des ingrédients nobles et naturels</h3>
<p>La marque utilise des matières premières d\'exception :</p>
<ul>
<li><strong>Huile d\'argan bio</strong> du Maroc, pressée à froid</li>
<li><strong>Beurre de karité</strong> pur, non raffiné</li>
<li><strong>Eau de rose de Damas</strong></li>
<li><strong>Rhassoul</strong> (argile naturelle du Maroc)</li>
<li><strong>Savon noir à l\'huile d\'olive</strong></li>
</ul>

<h3>Des textures incomparables</h3>
<p>C\'est ce qui distingue Charme d\'Orient de beaucoup d\'autres marques. Leurs <strong>beurres corporels</strong> fondent sur la peau comme du satin, leurs <strong>gommages</strong> sont à la fois efficaces et sensoriels, leurs <strong>huiles</strong> laissent la peau veloutée sans effet gras.</p>

<h3>Des résultats visibles dès le premier soin</h3>
<p>En cabine, les retours de mes clientes sont unanimes : après un soin Charme d\'Orient, la peau est <strong>incroyablement douce, nourrie et lumineuse</strong>. Le gommage au savon noir et au gant kessa, suivi d\'un enveloppement au rhassoul et d\'un modelage à l\'huile d\'argan, c\'est le soin signature que mes clientes redemandent systématiquement.</p>

<h2>Mes produits préférés de la marque</h2>
<ul>
<li><strong>Le savon noir à l\'eucalyptus</strong> : un incontournable pour préparer la peau au gommage</li>
<li><strong>Le beurre de karité parfumé</strong> : nutrition intense, parfum envoûtant, peau soyeuse</li>
<li><strong>L\'huile d\'argan pure</strong> : visage, corps, cheveux — un produit multifonction d\'exception</li>
<li><strong>Le rhassoul en poudre</strong> : masque purifiant et adoucissant pour le visage et les cheveux</li>
</ul>

<h2>Un rituel complet à la maison</h2>
<p>Vous pouvez recréer l\'expérience du hammam chez vous :</p>
<ul>
<li>Appliquez le <strong>savon noir</strong> sur peau humide, laissez poser 5 minutes</li>
<li>Rincez et <strong>gommez au gant kessa</strong> en mouvements circulaires</li>
<li>Appliquez un <strong>masque au rhassoul</strong> (10 minutes)</li>
<li>Rincez et terminez par un <strong>massage au beurre de karité</strong></li>
</ul>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Les bienfaits des massages du visage',
                'slug' => 'bienfaits-massages-du-visage',
                'excerpt' => 'Le massage du visage est bien plus qu\'un moment de détente. C\'est un véritable soin anti-âge naturel aux effets prouvés.',
                'meta_title' => 'Les bienfaits des massages du visage : anti-âge naturel',
                'meta_description' => 'Le massage du visage stimule la circulation, lisse les rides et draine les toxines. Découvrez ses bienfaits et comment le pratiquer chez vous.',
                'published_at' => '2026-05-16 09:00:00',
                'content' => '<h2>Un geste ancestral redécouvert</h2>
<p>Le massage du visage n\'est pas une tendance : c\'est une <strong>pratique ancestrale</strong> que l\'on retrouve dans les traditions de beauté japonaises, chinoises, indiennes et ayurvédiques. Et pour cause : ses effets sur la peau sont réels et visibles.</p>
<p>En tant qu\'esthéticienne, le massage est la partie du soin que mes clientes préfèrent. C\'est aussi celle qui produit les résultats les plus immédiats.</p>

<h2>Les bienfaits prouvés du massage facial</h2>

<h3>Stimulation de la circulation sanguine</h3>
<p>Le massage active la microcirculation, ce qui apporte <strong>plus d\'oxygène et de nutriments aux cellules</strong>. Résultat immédiat : le teint est plus frais, plus rosé, plus lumineux. Avec une pratique régulière, cet éclat devient permanent.</p>

<h3>Drainage lymphatique</h3>
<p>Le système lymphatique du visage est responsable de l\'élimination des toxines et de l\'excès d\'eau. Un massage drainant permet de :</p>
<ul>
<li><strong>Réduire les poches</strong> sous les yeux</li>
<li><strong>Affiner l\'ovale</strong> du visage</li>
<li><strong>Décongestionner</strong> les zones sujettes aux gonflements (matin)</li>
</ul>

<h3>Effet liftant naturel</h3>
<p>En travaillant les muscles du visage (il y en a 43 !), le massage maintient leur <strong>tonicité et leur élasticité</strong>. C\'est un lifting naturel qui prévient le relâchement cutané. Les techniques de pétrissage et de tapotement stimulent également la production de collagène.</p>

<h3>Relaxation et anti-stress</h3>
<p>Le visage accumule énormément de tension (mâchoire serrée, froncement des sourcils, contraction du front…). Le massage <strong>relâche ces tensions</strong>, ce qui atténue les rides d\'expression et procure un profond sentiment de bien-être.</p>

<h2>Les techniques à connaître</h2>

<h3>L\'effleurage</h3>
<p>Mouvements légers et glissants, du centre du visage vers l\'extérieur. C\'est la technique de base pour activer la circulation et appliquer votre huile ou votre sérum.</p>

<h3>Le pétrissage</h3>
<p>Pressions fermes avec les doigts sur les zones charnues (joues, menton). Stimule les muscles et favorise le renouvellement cellulaire.</p>

<h3>Le pincement (palper-rouler)</h3>
<p>Petits pincements légers sur tout le visage. Idéal pour <strong>stimuler la production de collagène</strong> et redonner du rebond.</p>

<h3>Le drainage</h3>
<p>Pressions douces et lentes le long des lignes lymphatiques, toujours vers le bas (vers les ganglions du cou). À pratiquer le matin pour dégonfler le visage.</p>

<h2>Comment masser son visage chez soi ?</h2>
<ul>
<li>Appliquez une <strong>huile végétale ou un sérum</strong> pour que vos doigts glissent</li>
<li>Commencez par le <strong>front</strong> : lissez du centre vers les tempes</li>
<li>Descendez sur les <strong>joues</strong> : remontez de la mâchoire vers les pommettes</li>
<li>Travaillez le <strong>contour des yeux</strong> : tapotements légers du coin interne vers l\'extérieur</li>
<li>Terminez par le <strong>cou</strong> : mouvements descendants vers les clavicules</li>
<li>Durée : <strong>5 à 10 minutes</strong>, idéalement chaque soir</li>
</ul>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Préparer sa peau avant les vacances',
                'slug' => 'preparer-peau-avant-vacances',
                'excerpt' => 'Quelques semaines avant le départ, préparez votre peau pour qu\'elle profite pleinement du soleil sans en subir les dégâts.',
                'meta_title' => 'Préparer sa peau avant les vacances : le guide complet',
                'meta_description' => 'Comment préparer sa peau avant les vacances d\'été ? Exfoliation, hydratation, compléments alimentaires : le programme 4 semaines avant le départ.',
                'published_at' => '2026-06-06 09:00:00',
                'content' => '<h2>Pourquoi préparer sa peau au soleil ?</h2>
<p>Partir en vacances sans avoir préparé sa peau, c\'est comme courir un marathon sans entraînement. <strong>Une peau préparée bronze plus vite, plus uniformément et conserve son hâle plus longtemps</strong>. Elle résiste aussi mieux aux agressions du soleil, du sel et du chlore.</p>
<p>Idéalement, commencez votre préparation <strong>4 semaines avant le départ</strong>.</p>

<h2>Le programme en 4 étapes</h2>

<h3>Étape 1 : exfolier (4 semaines avant)</h3>
<p>Un gommage permet d\'éliminer les cellules mortes qui ternissent le teint et empêchent un bronzage uniforme. Pour le visage, optez pour un <strong>gommage enzymatique doux</strong>. Pour le corps, un <strong>gommage au sucre ou au savon noir</strong> fait des merveilles.</p>
<p>Rythme : une fois par semaine jusqu\'au départ, puis arrêtez les gommages pendant l\'exposition au soleil (ils sensibilisent la peau).</p>

<h3>Étape 2 : hydrater intensément (3 semaines avant)</h3>
<p>Une peau bien hydratée est une peau qui bronze joliment et qui pèle moins. Renforcez votre hydratation :</p>
<ul>
<li><strong>Visage</strong> : sérum à l\'acide hyaluronique + crème hydratante riche</li>
<li><strong>Corps</strong> : lait ou beurre corporel après chaque douche</li>
<li><strong>De l\'intérieur</strong> : buvez au moins 1,5 litre d\'eau par jour</li>
</ul>

<h3>Étape 3 : stimuler le bronzage naturel (2 semaines avant)</h3>
<p>Certains aliments et compléments favorisent la production de mélanine :</p>
<ul>
<li><strong>Aliments riches en bêta-carotène</strong> : carottes, patates douces, abricots, mangues</li>
<li><strong>Compléments solaires</strong> : à base de bêta-carotène, lycopène et vitamine E (disponibles en pharmacie)</li>
<li><strong>Huile de carotte</strong> : en application sur le corps (attention, ne protège pas du soleil !)</li>
</ul>

<h3>Étape 4 : commencer les expositions progressives (1 semaine avant)</h3>
<p>Si possible, exposez-vous <strong>progressivement</strong> avant le départ : 15-20 minutes par jour, aux heures les moins chaudes (avant 11h ou après 16h), toujours avec une protection solaire. Cela permet à la peau de commencer à produire de la mélanine en douceur.</p>

<h2>Les zones à ne pas oublier</h2>
<ul>
<li><strong>Les pieds</strong> : gommage + hydratation pour des pieds présentables en sandales</li>
<li><strong>Les lèvres</strong> : baume hydratant SPF pour éviter les gerçures</li>
<li><strong>Le cuir chevelu</strong> : pensez à un spray protecteur capillaire</li>
<li><strong>Les mains</strong> : souvent oubliées mais très exposées</li>
</ul>

<h2>Ce qu\'il faut emporter dans la valise</h2>
<ul>
<li>Crème solaire SPF 50 pour le visage et SPF 30 pour le corps</li>
<li>Après-soleil hydratant (aloe vera ou beurre de karité)</li>
<li>Brumisateur d\'eau thermale</li>
<li>Huile de coco pour les cheveux (protection + nutrition)</li>
</ul>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
            [
                'title' => 'Comment protéger naturellement sa peau du soleil ?',
                'slug' => 'proteger-naturellement-peau-soleil',
                'excerpt' => 'La protection solaire est essentielle, mais comment concilier efficacité et naturalité ? Découvrez les solutions naturelles et les bons réflexes.',
                'meta_title' => 'Protection solaire naturelle : comment protéger sa peau ?',
                'meta_description' => 'Filtres minéraux, vêtements protecteurs, alimentation : comment protéger naturellement sa peau du soleil sans sacrifier l\'efficacité ?',
                'published_at' => '2026-06-20 09:00:00',
                'content' => '<h2>Le soleil : ami et ennemi de la peau</h2>
<p>Le soleil est indispensable à notre bien-être : il stimule la production de vitamine D, améliore l\'humeur et donne bonne mine. Mais ses <strong>rayons ultraviolets (UV) sont aussi la première cause de vieillissement prématuré</strong> et de cancers cutanés.</p>
<p>L\'enjeu n\'est pas d\'éviter le soleil, mais de <strong>s\'en protéger intelligemment</strong>.</p>

<h2>Comprendre les rayons UV</h2>
<ul>
<li><strong>UVB</strong> : responsables des coups de soleil. Ils frappent surtout en été et aux heures les plus chaudes</li>
<li><strong>UVA</strong> : responsables du vieillissement cutané (rides, taches, perte d\'élasticité). Ils pénètrent plus profondément et sont présents toute l\'année, même à travers les vitres</li>
</ul>
<p>Une bonne protection doit couvrir <strong>les deux types de rayons</strong>.</p>

<h2>Les filtres solaires naturels (minéraux)</h2>
<p>Les crèmes solaires naturelles utilisent des <strong>filtres minéraux</strong> (oxyde de zinc et dioxyde de titane) plutôt que des filtres chimiques. Leur fonctionnement :</p>
<ul>
<li>Ils se <strong>déposent sur la peau</strong> et réfléchissent les UV comme un miroir</li>
<li>Ils sont <strong>efficaces immédiatement</strong> (pas besoin d\'attendre 20 minutes)</li>
<li>Ils sont <strong>mieux tolérés</strong> par les peaux sensibles et réactives</li>
<li>Ils sont <strong>plus respectueux de l\'environnement</strong> (pas de pollution des océans)</li>
</ul>
<p>Le petit inconvénient : les formulations anciennes laissaient un film blanc sur la peau. Les nouvelles générations de filtres minéraux micronisés sont beaucoup plus transparentes.</p>

<h2>Les réflexes de protection au quotidien</h2>

<h3>Les heures d\'exposition</h3>
<p>La règle d\'or : <strong>évitez l\'exposition entre 12h et 16h</strong>, quand les UV sont les plus intenses. Le reste du temps, profitez du soleil avec modération et protection.</p>

<h3>Les vêtements protecteurs</h3>
<p>C\'est la <strong>protection la plus efficace et la plus naturelle</strong>. Un chapeau à larges bords, des lunettes de soleil certifiées et un t-shirt à manches longues en tissu dense protègent mieux qu\'aucune crème.</p>

<h3>L\'ombre intelligente</h3>
<p>Parasol, pergola, arbre… Cherchez l\'ombre, surtout aux heures les plus chaudes. Attention cependant : l\'ombre ne bloque pas tous les UV (réverbération du sable, de l\'eau, du béton).</p>

<h2>L\'alimentation protectrice</h2>
<p>Certains nutriments renforcent les défenses naturelles de la peau face aux UV :</p>
<ul>
<li><strong>Bêta-carotène</strong> (carottes, patates douces, abricots) : prépare la peau au soleil</li>
<li><strong>Lycopène</strong> (tomates cuites, pastèque) : protection antioxydante contre les UV</li>
<li><strong>Vitamine E</strong> (amandes, huile d\'olive, avocat) : protège les membranes cellulaires</li>
<li><strong>Vitamine C</strong> (agrumes, kiwi, poivrons) : neutralise les radicaux libres</li>
<li><strong>Oméga-3</strong> (poissons gras, noix, graines de lin) : anti-inflammatoires naturels</li>
</ul>
<p>Ces aliments ne remplacent pas la crème solaire mais <strong>complètent la protection de l\'intérieur</strong>.</p>

<h2>Les soins après-soleil</h2>
<p>Après une journée d\'exposition, votre peau a besoin de récupérer :</p>
<ul>
<li><strong>Aloe vera</strong> : apaise, hydrate et aide à la réparation (en gel pur)</li>
<li><strong>Huile de calendula</strong> : anti-inflammatoire et réparatrice</li>
<li><strong>Beurre de karité</strong> : nutrition intense pour les peaux très sèches après le soleil</li>
<li><strong>Eau thermale en brumisateur</strong> : rafraîchit et calme les échauffements</li>
</ul>

<h2>Mon conseil d\'esthéticienne</h2>
<p>La protection solaire n\'est pas un geste estival, c\'est un geste <strong>quotidien et annuel</strong>. Intégrez un SPF dans votre routine du matin, même en hiver, même en ville. C\'est le geste anti-âge le plus efficace et le plus simple que vous puissiez adopter.</p>

<p>Vous souhaitez des conseils adaptés à votre peau ? <a href="/contact">Prenez rendez-vous à l\'institut</a> ou <a href="/boutique">découvrez ma sélection de produits</a>.</p>',
            ],
        ];

        foreach ($articles as $article) {
            Post::create(array_merge($article, [
                'status' => 'published',
                'user_id' => null,
            ]));
        }
    }

    public function down(): void
    {
        $slugs = [
            'comment-connaitre-son-type-de-peau',
            'pourquoi-hydrater-peau-grasse',
            'bienfaits-huiles-vegetales-peau',
            'routine-beaute-naturelle-hiver',
            'comment-choisir-serum-visage',
            'pourquoi-faire-diagnostic-de-peau',
            'erreurs-accelerent-vieillissement-cutane',
            'comment-reussir-routine-anti-age',
            'pourquoi-jaime-soins-charme-orient',
            'bienfaits-massages-du-visage',
            'preparer-peau-avant-vacances',
            'proteger-naturellement-peau-soleil',
        ];

        Post::whereIn('slug', $slugs)->delete();
    }
};
