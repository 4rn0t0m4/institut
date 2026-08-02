<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $brands = [
            'eskalia' => [
                'description' => 'Cosmétiques naturels aux inspirations voyageuses. Des soins visage et corps aux textures sensorielles et aux parfums d\'évasion.',
                'color' => '#2e7d5b',
                'meta_title' => 'Eskalia — Cosmétiques naturels inspirés du voyage | Institut Corps à Cœur',
                'meta_description' => 'Découvrez Eskalia chez Institut Corps à Cœur : cosmétiques naturels aux inspirations voyageuses. Soins visage et corps, textures sensorielles, parfums d\'évasion. Conseils personnalisés d\'une esthéticienne.',
                'content' => $this->eskaliaContent(),
            ],
            'jadea' => [
                'description' => 'Soins dermo-cosmétiques professionnels pour le visage. Des routines ciblées pour chaque type de peau, formulées avec des actifs naturels.',
                'color' => '#6b5b8a',
                'meta_title' => 'JADEA — Soins dermo-cosmétiques professionnels | Institut Corps à Cœur',
                'meta_description' => 'Découvrez JADEA chez Institut Corps à Cœur : soins dermo-cosmétiques professionnels pour le visage. Gammes Purifier, Hydrater et Anti-âge. Actifs naturels, conseils d\'une esthéticienne.',
                'content' => $this->jadeaContent(),
            ],
            'charme-dorient' => [
                'description' => 'Soins corporels d\'inspiration orientale. Beurres de karité, gommages à la pierre d\'alun et huiles parfumées aux senteurs envoûtantes.',
                'color' => '#b8860b',
                'meta_title' => 'Charme d\'Orient — Soins corporels orientaux naturels | Institut Corps à Cœur',
                'meta_description' => 'Découvrez Charme d\'Orient chez Institut Corps à Cœur : beurres de karité, gommages pierre d\'alun, huiles corporelles. Soins orientaux aux ingrédients naturels et parfums envoûtants.',
                'content' => $this->charmeContent(),
            ],
            'nakupenda' => [
                'description' => 'Bijoux en acier inoxydable dorés et raffinés. Des pièces élégantes et résistantes, parfaites pour sublimer chaque tenue.',
                'color' => '#c4954a',
                'meta_title' => 'Nakupenda — Bijoux en acier inoxydable dorés | Institut Corps à Cœur',
                'meta_description' => 'Découvrez Nakupenda chez Institut Corps à Cœur : bijoux en acier inoxydable dorés, bagues, bracelets, colliers et boucles d\'oreilles. Pièces élégantes et résistantes.',
                'content' => $this->nakupendaContent(),
            ],
        ];

        foreach ($brands as $slug => $data) {
            DB::table('brands')->where('slug', $slug)->update($data);
        }
    }

    public function down(): void
    {
        DB::table('brands')->whereIn('slug', ['eskalia', 'jadea', 'charme-dorient', 'nakupenda'])
            ->update(['content' => null, 'meta_title' => null, 'meta_description' => null, 'description' => null, 'color' => null]);
    }

    private function eskaliaContent(): string
    {
        return <<<'HTML'
<h2>L'histoire d'Eskalia : le voyage au cœur de la beauté</h2>

<p>Eskalia est née d'une idée aussi simple que séduisante : associer l'efficacité cosmétique à l'évasion sensorielle. Chaque gamme de la marque s'inspire d'une destination du monde — Bali, la Guadeloupe, Zanzibar, le Japon — et traduit en soins les trésors botaniques de ces terres lointaines. Les formulations puisent dans les rituels de beauté ancestraux de chaque culture pour offrir une expérience unique à chaque application.</p>

<p>Derrière cette marque française, il y a une conviction forte : la cosmétique naturelle peut être à la fois performante, plaisante et respectueuse de la peau. Eskalia développe ses produits en France, avec des ingrédients soigneusement sourcés et des textures travaillées pour un plaisir d'utilisation immédiat.</p>

<h2>Pourquoi j'ai choisi Eskalia pour l'institut</h2>

<p>Quand j'ai découvert Eskalia, c'est d'abord l'univers sensoriel qui m'a conquise. En tant qu'esthéticienne, je sais que l'efficacité d'un soin passe aussi par le plaisir qu'il procure. Quand une cliente s'installe en cabine et que le parfum du soin l'enveloppe, la détente commence avant même le premier geste.</p>

<p>Mais au-delà des senteurs, c'est la qualité des formulations qui m'a convaincue de proposer cette marque. J'ai testé chaque produit pendant plusieurs mois en cabine avant de les mettre en vente. Les résultats étaient là : des peaux visiblement plus hydratées, plus lumineuses, plus confortables. Mes clientes me redemandaient systématiquement quels produits j'avais utilisés.</p>

<p>Eskalia coche toutes les cases de ce que je recherche : des ingrédients naturels de qualité, des textures agréables, un excellent rapport qualité-prix et des résultats visibles. C'est aujourd'hui la marque la plus vendue de ma boutique, et ce n'est pas un hasard.</p>

<h2>Des ingrédients naturels venus du monde entier</h2>

<p>La force d'Eskalia réside dans sa sélection d'actifs naturels, choisis pour leur efficacité prouvée :</p>

<ul>
<li><strong>Huile de coco vierge</strong> (gamme Bali) : nourrissante et protectrice, elle enveloppe la peau d'un film doux sans effet gras.</li>
<li><strong>Mangue et papaye</strong> (gamme Guadeloupe) : riches en vitamines A et C, ces fruits tropicaux illuminent le teint et stimulent le renouvellement cellulaire.</li>
<li><strong>Épices et huiles essentielles</strong> (gamme Zanzibar) : cannelle, girofle et ylang-ylang aux propriétés tonifiantes et stimulantes pour la circulation.</li>
<li><strong>Thé vert et riz</strong> (gamme Japon) : antioxydants puissants, ils protègent la peau des agressions extérieures et unifient le teint.</li>
<li><strong>Beurre de karité</strong> : présent dans la plupart des gammes, il apporte une nutrition intense aux peaux les plus sèches.</li>
</ul>

<p>Les formules sont sans paraben, sans huile minérale et non testées sur les animaux. Eskalia privilégie les ingrédients d'origine naturelle tout en garantissant la stabilité et la sécurité de ses produits.</p>

<h2>Des textures qui transforment le soin en rituel</h2>

<p>C'est peut-être ce qui distingue le plus Eskalia des autres marques naturelles : le travail sur les textures. Les fluides nettoyants se transforment en mousse légère au contact de l'eau. Les crèmes fondent sur la peau sans laisser de film gras. Les gommages au sucre exfolient en douceur tout en déposant un voile satiné.</p>

<p>Le baume ultra-réparateur est un incontournable : sa texture riche mais non grasse pénètre rapidement pour nourrir les zones les plus desséchées — mains, pieds, coudes, lèvres. C'est un produit multi-usage que toutes mes clientes finissent par adopter.</p>

<p>Les gelées pailletées, quant à elles, apportent une touche festive en été : un voile scintillant sur le corps, les cheveux ou le décolleté pour un effet bonne mine immédiat.</p>

<h2>Des parfums qui font voyager</h2>

<p>Chaque gamme Eskalia a son identité olfactive, et c'est souvent la première chose que remarquent les clientes :</p>

<ul>
<li><strong>Bali</strong> : des notes de cassis, rose, framboise, citron, litchi et vanille. Un parfum gourmand et enveloppant qui évoque les temples balinais au crépuscule.</li>
<li><strong>Guadeloupe</strong> : mangue, passion et fleur de tiaré. Un cocktail fruité et solaire qui transporte instantanément sous les tropiques.</li>
<li><strong>Zanzibar</strong> : épices chaudes, bois de santal et notes musquées. Un sillage oriental et envoûtant, parfait pour les soirées d'hiver.</li>
<li><strong>Japon</strong> : fleur de cerisier, thé vert et notes poudrées. Une fragrance délicate et apaisante qui invite à la sérénité.</li>
</ul>

<p>Les parfums sont suffisamment présents pour créer une expérience sensorielle complète, mais restent subtils après application. Ils persistent agréablement sur la peau pendant plusieurs heures.</p>

<h2>Les produits incontournables Eskalia</h2>

<p>Après plusieurs années d'utilisation en institut et en vente, voici les produits que je recommande systématiquement :</p>

<ul>
<li><strong>Le fluide nettoyant moussant</strong> : en version Bali ou Guadeloupe, c'est le geste démaquillant parfait. Il élimine le maquillage et les impuretés sans assécher la peau.</li>
<li><strong>La crème jour et nuit Bali</strong> : une hydratation continue qui apaise et restructure. Convient à la plupart des types de peau.</li>
<li><strong>Le baume ultra-réparateur</strong> : le couteau suisse de la gamme. En hiver, mes clientes ne s'en passent plus pour les mains et les lèvres.</li>
<li><strong>L'élixir Bali</strong> : un soin tout-en-un qui nourrit, lisse et sublime la peau en un seul geste. Idéal pour les personnes pressées.</li>
<li><strong>Le gommage corps au sucre</strong> : une exfoliation douce qui laisse la peau incroyablement douce et parfumée.</li>
<li><strong>La bougie bijou</strong> : un cadeau qui fait toujours plaisir. Une bougie longue durée au parfum envoûtant, avec un bijou surprise à l'intérieur.</li>
</ul>

<h2>À quel type de peau convient Eskalia ?</h2>

<p>C'est l'un des grands atouts de la marque : la diversité des gammes permet de répondre à pratiquement tous les besoins :</p>

<ul>
<li><strong>Peaux sèches et déshydratées</strong> : la gamme Bali est idéale grâce à ses textures riches et nourrissantes. Le beurre de karité et l'huile de coco apportent un confort immédiat.</li>
<li><strong>Peaux ternes et fatiguées</strong> : la gamme Guadeloupe, riche en vitamines, redonne éclat et luminosité au teint. Le concentré bonne mine est un vrai coup de fouet.</li>
<li><strong>Peaux matures</strong> : les actifs antioxydants des gammes Japon et Bali aident à lutter contre les signes du vieillissement cutané.</li>
<li><strong>Toutes les peaux pour le corps</strong> : les gommages, baumes et huiles conviennent à tous les types de peau, même les plus sensibles.</li>
</ul>

<p>En cas de doute, n'hésitez pas à me contacter : un échange rapide me permet de vous orienter vers la gamme la plus adaptée à votre peau.</p>

<h2>Mes conseils d'utilisation</h2>

<p>Pour tirer le meilleur de vos produits Eskalia, voici les gestes que je recommande en institut :</p>

<ul>
<li><strong>Le double nettoyage</strong> : commencez par le fluide nettoyant moussant pour retirer le maquillage, puis appliquez la lotion tonifiante pour éliminer les dernières traces et préparer la peau à recevoir le soin.</li>
<li><strong>L'élixir avant la crème</strong> : quelques gouttes d'élixir avant votre crème de jour démultiplient l'hydratation et apportent un éclat immédiat.</li>
<li><strong>Le gommage hebdomadaire</strong> : un gommage corps une fois par semaine, suivi du baume réparateur, pour une peau durablement douce et nourrie.</li>
<li><strong>La crème teintée au quotidien</strong> : elle remplace à la fois la crème de jour et le fond de teint pour un résultat naturel et une routine simplifiée.</li>
</ul>

<h2>Questions fréquentes sur Eskalia</h2>

<h3>Eskalia est-elle une marque cruelty-free ?</h3>
<p>Oui, Eskalia ne teste pas ses produits sur les animaux. Les formulations sont testées dermatologiquement sur des volontaires humains.</p>

<h3>Peut-on utiliser Eskalia sur une peau sensible ?</h3>
<p>La majorité des produits Eskalia conviennent aux peaux sensibles grâce à leurs formulations douces et naturelles. En cas de peau très réactive, je recommande de commencer par la gamme Bali, la plus apaisante.</p>

<h3>Quelle est la durée de conservation des produits ?</h3>
<p>Les produits Eskalia se conservent 12 mois après ouverture (symbole PAO sur l'emballage). Avant ouverture, la durée de conservation est de 30 mois.</p>

<h3>Les produits Eskalia sont-ils fabriqués en France ?</h3>
<p>Oui, Eskalia développe et fabrique ses produits en France, avec des ingrédients soigneusement sourcés dans le monde entier.</p>

<h3>Puis-je commander en ligne et me faire livrer ?</h3>
<p>Bien sûr ! Toute la gamme Eskalia est disponible sur notre boutique en ligne avec livraison en Colissimo ou en point relais. La livraison est offerte dès 60 € d'achat. Vous pouvez aussi retirer votre commande gratuitement à l'institut.</p>
HTML;
    }

    private function jadeaContent(): string
    {
        return <<<'HTML'
<h2>L'histoire de JADEA : la dermo-cosmétique accessible</h2>

<p>JADEA est une marque française de dermo-cosmétique professionnelle née de la conviction que des soins de qualité ne doivent pas être réservés aux cabines d'instituts. Conçue par des professionnels de la beauté, chaque formule est développée pour répondre avec précision aux besoins spécifiques de chaque type de peau.</p>

<p>La marque se distingue par son approche méthodique : des gammes clairement identifiées par numéro et par besoin — Purifier, Hydrater, Anti-âge — qui permettent de construire une routine complète et cohérente. Chaque produit porte un numéro correspondant à l'ordre d'utilisation dans la routine, ce qui simplifie considérablement le choix pour les clientes.</p>

<h2>Pourquoi j'ai choisi JADEA pour l'institut</h2>

<p>JADEA s'est imposée dans mon institut pour une raison simple : c'est la marque la plus efficace que j'aie testée pour les problématiques de peau courantes. En cabine, les résultats sont visibles dès la première séance. Les peaux grasses retrouvent un équilibre, les peaux déshydratées sont repulpées, les premiers signes de l'âge sont atténués.</p>

<p>Ce qui me plaît particulièrement, c'est la logique de la gamme. Quand une cliente me demande conseil, je peux lui construire une routine complète en quelques minutes : « Commencez par le n°2 pour le nettoyage, puis le n°5 pour le jour, le n°6 pour la nuit. » C'est clair, simple, et les clientes s'y retrouvent facilement.</p>

<p>Les textures sont aussi un point fort : ni trop riches, ni trop légères, elles conviennent au climat français et à notre mode de vie. Les soins pénètrent rapidement, ne laissent pas de film gras et constituent une base parfaite sous le maquillage.</p>

<h2>Des actifs naturels ciblés et efficaces</h2>

<p>JADEA formule ses soins avec des actifs naturels choisis pour leur efficacité prouvée sur chaque problématique :</p>

<ul>
<li><strong>Argile verte et zinc</strong> (gamme Purifier) : régulent l'excès de sébum et resserrent les pores sans assécher la peau. L'argile absorbe les impuretés tandis que le zinc assainit.</li>
<li><strong>Acide hyaluronique et aloe vera</strong> (gamme Hydrater) : un duo hydratant puissant qui repulpe la peau en surface et en profondeur. L'acide hyaluronique capte jusqu'à 1 000 fois son poids en eau.</li>
<li><strong>Vitamine C et rétinol végétal</strong> (gamme Anti-âge) : stimulent la production de collagène, unifient le teint et réduisent visiblement les rides et ridules.</li>
<li><strong>Extraits de concombre et camomille</strong> : présents dans les soins contour des yeux pour leur action décongestionnante et apaisante.</li>
<li><strong>Huile de jojoba</strong> : utilisée dans les gommages et masques pour nourrir sans obstruer les pores, grâce à sa composition proche du sébum naturel.</li>
</ul>

<p>Toutes les formulations sont sans paraben, sans phénoxyéthanol et testées dermatologiquement. JADEA privilégie les actifs d'origine naturelle dans des concentrations efficaces.</p>

<h2>Des textures professionnelles pensées pour le quotidien</h2>

<p>L'un des grands atouts de JADEA, c'est le travail sur les textures. Chaque produit a été conçu pour offrir un toucher agréable qui donne envie de refaire le geste chaque jour :</p>

<ul>
<li><strong>Le lait démaquillant</strong> : une texture fluide qui libère les impuretés sans tiraillement. Un geste de confort même pour les peaux les plus réactives.</li>
<li><strong>Les sérums</strong> : des textures ultra-légères qui pénètrent instantanément. Ils s'appliquent en quelques secondes et ne collent pas.</li>
<li><strong>Les crèmes de jour</strong> : un fini mat pour la gamme Purifier, un fini lumineux pour la gamme Hydrater. Dans les deux cas, elles constituent une base idéale sous le maquillage.</li>
<li><strong>Les masques</strong> : des textures crémeuses qui ne sèchent pas sur le visage, permettant un moment de détente confortable pendant la pose.</li>
</ul>

<h2>Des parfums discrets et apaisants</h2>

<p>Contrairement à certaines marques qui misent sur des fragrances prononcées, JADEA fait le choix de parfums discrets et frais. Les soins dégagent une légère note végétale, juste assez pour rendre le geste agréable sans irriter les peaux sensibles ni interférer avec un parfum personnel.</p>

<p>La gamme Hydrater présente des notes de concombre et de thé blanc, très fraîches. La gamme Purifier dégage une touche herbacée légèrement mentholée. La gamme Anti-âge offre des notes florales délicates de rose et de jasmin.</p>

<h2>Les produits incontournables JADEA</h2>

<p>Voici les produits que je recommande en priorité et que mes clientes rachètent le plus souvent :</p>

<ul>
<li><strong>N°2 Nettoyant biphase Purifier</strong> : un démaquillant bi-phasé qui retire même le maquillage waterproof sans frotter. Efficace en un seul passage sur un coton.</li>
<li><strong>N°5 Sérum Hydratation Intense</strong> : le meilleur rapport efficacité-prix de la gamme. Quelques gouttes suffisent pour une hydratation qui dure toute la journée.</li>
<li><strong>N°5 Crème de jour matifiante Purifier</strong> : la solution idéale pour les peaux à tendance grasse. Le teint reste mat tout au long de la journée sans effet cartonné.</li>
<li><strong>N°4 Crème Contour des Yeux</strong> : agit sur les cernes, les poches et les ridules. Sa texture légère permet une application matin et soir.</li>
<li><strong>N°8 Gommage</strong> : disponible en version Purifier ou Hydrater, il affine le grain de peau en douceur et prépare la peau à mieux absorber les soins suivants.</li>
<li><strong>N°9 Masque Lissant Hydrater</strong> : un soin cocooning qui repulpe la peau en 10 minutes. Idéal le dimanche soir pour attaquer la semaine avec une peau éclatante.</li>
</ul>

<h2>À quel type de peau convient JADEA ?</h2>

<p>C'est la marque idéale si vous cherchez une routine ciblée pour votre type de peau :</p>

<ul>
<li><strong>Peaux grasses et mixtes à imperfections</strong> : la gamme Purifier régule le sébum, resserre les pores et réduit les imperfections. Le roll-on imperfections (n°10) est un SOS efficace sur les boutons.</li>
<li><strong>Peaux sèches, déshydratées ou normales</strong> : la gamme Hydrater apporte une hydratation profonde et durable. La crème de nuit et le sérum sont particulièrement adaptés aux peaux qui tiraillent.</li>
<li><strong>Peaux matures</strong> : la gamme Anti-âge associe des actifs repulpants et des antioxydants pour lisser, raffermir et illuminer. Compatible avec les deux autres gammes pour une action ciblée.</li>
<li><strong>Toutes les peaux</strong> : le contour des yeux et la crème cou et buste conviennent à tous les types de peau, quels que soient l'âge et les besoins.</li>
</ul>

<p>Il est tout à fait possible de mixer les gammes : par exemple, utiliser le nettoyant Purifier avec la crème Hydrater si vous avez une peau mixte qui a besoin d'hydratation sans excès de gras.</p>

<h2>Mes conseils d'utilisation</h2>

<p>Voici la routine que je préconise à mes clientes pour des résultats optimaux :</p>

<ul>
<li><strong>Le matin</strong> : nettoyant (n°2) → lotion tonifiante (n°3) → sérum (n°5) → crème de jour (n°5 ou n°6) → contour des yeux (n°4). Comptez 3 minutes.</li>
<li><strong>Le soir</strong> : nettoyant (n°2) → lotion tonifiante (n°3) → sérum (n°5) → crème de nuit (n°6 ou n°7) → contour des yeux (n°4).</li>
<li><strong>Deux fois par semaine</strong> : gommage (n°8) suivi du masque (n°9) pour un effet « peau neuve » visible immédiatement.</li>
<li><strong>En cas d'imperfection</strong> : appliquer le roll-on (n°10) localement, matin et soir, directement sur le bouton.</li>
</ul>

<p>Mon conseil le plus important : la régularité. Les meilleurs résultats s'observent après 3 à 4 semaines de routine quotidienne. La peau a besoin d'un cycle complet de renouvellement pour révéler tout son éclat.</p>

<h2>Questions fréquentes sur JADEA</h2>

<h3>Peut-on utiliser JADEA en complément d'un traitement dermatologique ?</h3>
<p>JADEA est une gamme dermo-cosmétique compatible avec la plupart des traitements. En cas de traitement spécifique (rétinoïdes, acides forts), je recommande de demander l'avis de votre dermatologue, notamment pour la gamme Anti-âge.</p>

<h3>Les produits JADEA conviennent-ils aux peaux sensibles ?</h3>
<p>La gamme Hydrater est particulièrement douce et convient aux peaux sensibles. Le lait démaquillant et la lotion tonifiante sont formulés pour respecter les peaux réactives. En revanche, la gamme Purifier, plus active, peut provoquer un léger picotement sur les peaux très sensibles.</p>

<h3>Combien de temps dure un produit JADEA ?</h3>
<p>En utilisation quotidienne, un tube de crème (30 ml) dure environ 2 mois. Un sérum (30 ml) dure 2 à 3 mois. Un nettoyant ou une lotion (200 ml) dure 3 à 4 mois. C'est un excellent rapport qualité-prix pour une marque professionnelle.</p>

<h3>JADEA est-elle disponible uniquement en institut ?</h3>
<p>JADEA est une marque professionnelle distribuée en instituts de beauté. Chez Corps à Cœur, vous pouvez l'acheter en boutique ou sur notre site en ligne avec livraison partout en France.</p>
HTML;
    }

    private function charmeContent(): string
    {
        return <<<'HTML'
<h2>L'histoire de Charme d'Orient : l'art du hammam à la française</h2>

<p>Charme d'Orient est une maison française fondée il y a plus de 20 ans, spécialisée dans les soins corporels d'inspiration orientale. La marque perpétue les rituels de beauté millénaires du hammam — gommage, enveloppement, massage — en les adaptant aux exigences de la cosmétique moderne.</p>

<p>Chaque produit raconte une histoire : celle des souks parfumés de Marrakech, des hammams de Fès, des jardins de roses de Damas. Charme d'Orient capture l'essence de ces traditions dans des formulations alliant ingrédients naturels nobles et savoir-faire cosmétique français.</p>

<p>La marque est aujourd'hui une référence dans les spas et instituts de beauté en France et à l'international. Elle est reconnue pour la qualité exceptionnelle de ses beurres de karité et de ses gommages à la pierre d'alun.</p>

<h2>Pourquoi j'ai choisi Charme d'Orient pour l'institut</h2>

<p>Le rituel oriental est l'un des soins les plus demandés dans mon institut. Quand j'ai cherché une marque pour accompagner ces prestations, Charme d'Orient s'est imposée immédiatement par la qualité de ses produits.</p>

<p>La première fois que j'ai ouvert un pot de beurre de karité Charme d'Orient, j'ai su que c'était la bonne marque. La texture, le parfum, la richesse du produit — tout était un cran au-dessus de ce que j'avais testé jusque-là. Et surtout, le résultat sur la peau est spectaculaire : après un gommage suivi du beurre de karité, la peau est transformée, douce comme du satin, nourrie en profondeur.</p>

<p>Mes clientes sont unanimes : les soins Charme d'Orient sont une expérience à part entière. Beaucoup repartent avec un pot de beurre de karité ou un gommage pour prolonger le rituel chez elles.</p>

<h2>Des ingrédients naturels d'exception</h2>

<p>Charme d'Orient sélectionne des ingrédients naturels nobles, issus de la tradition cosmétique orientale :</p>

<ul>
<li><strong>Beurre de karité pur</strong> : la star de la gamme. Un karité de qualité supérieure, onctueux et fondant, qui nourrit intensément les peaux les plus sèches. Riche en vitamines A, D, E et F.</li>
<li><strong>Pierre d'alun naturelle</strong> : utilisée dans les gommages, elle possède des propriétés astringentes et purifiantes. Elle resserre les pores et laisse la peau nette et lisse.</li>
<li><strong>Huile d'argan</strong> : trésor du Maroc, elle nourrit, assouplit et protège la peau grâce à ses acides gras essentiels et sa vitamine E.</li>
<li><strong>Eau de rose de Damas</strong> : tonifiante et apaisante, elle rafraîchit la peau et prépare au soin suivant.</li>
<li><strong>Huile de nigelle</strong> : connue pour ses propriétés purifiantes et régénérantes, utilisée depuis l'Antiquité dans les rituels de beauté orientaux.</li>
</ul>

<p>Les formulations sont sans paraben, sans colorant artificiel et sans huile minérale. Charme d'Orient privilégie les ingrédients bruts, peu transformés, pour préserver toutes leurs propriétés.</p>

<h2>Des textures généreuses et enveloppantes</h2>

<p>Les produits Charme d'Orient sont conçus pour transformer chaque soin en un moment de pur plaisir :</p>

<ul>
<li><strong>Les beurres de karité</strong> : une texture onctueuse qui fond au contact de la peau. Le karité se réchauffe entre les mains et s'applique en massage pour une pénétration optimale. Le fini est satiné, sans effet gras.</li>
<li><strong>Les gommages</strong> : à base de pierre d'alun finement broyée, ils offrent une exfoliation mécanique douce mais efficace. La texture granuleuse mais non agressive élimine les cellules mortes sans irriter.</li>
<li><strong>Les huiles corporelles</strong> : fluides et soyeuses, elles pénètrent rapidement et laissent un voile parfumé sur la peau. Parfaites en huile de massage ou en soin après la douche.</li>
<li><strong>Le lait corps</strong> : une texture légère et fondante pour une hydratation quotidienne parfumée, idéale pour les journées où l'on veut un soin rapide mais efficace.</li>
</ul>

<h2>Des senteurs qui transportent en Orient</h2>

<p>Les parfums de Charme d'Orient sont l'âme de la marque. Chaque senteur est une invitation au voyage :</p>

<ul>
<li><strong>Fleur d'oranger</strong> : le classique absolu. Doux, sucré et légèrement miellé, ce parfum évoque les jardins méditerranéens au printemps. C'est le beurre de karité le plus vendu chez nous.</li>
<li><strong>Néroli</strong> : plus raffiné et floral que la fleur d'oranger, le néroli apporte une note élégante et fraîche. Un parfum qui séduit les amatrices de fragrances plus subtiles.</li>
<li><strong>Fleur de tiaré</strong> : exotique et sensuel, ce parfum évoque les plages polynésiennes. Un coup de cœur pour l'été.</li>
<li><strong>Effluves du Nil</strong> : un mélange mystérieux d'encens, de musc et d'épices douces. Le plus oriental de la gamme.</li>
<li><strong>Thé vert</strong> : frais et végétal, pour celles qui préfèrent les notes discrètes et naturelles.</li>
<li><strong>Fruits</strong> : un mélange fruité et gourmand, acidulé et joyeux. Parfait au quotidien.</li>
</ul>

<p>Les parfums persistent agréablement sur la peau pendant plusieurs heures après l'application, sans être entêtants.</p>

<h2>Les produits incontournables Charme d'Orient</h2>

<ul>
<li><strong>Beurre de karité Fleur d'oranger</strong> : le best-seller absolu. 200 g de pur bonheur pour la peau. À utiliser sur le corps après la douche, mais aussi sur les mains, les pieds, les coudes — partout où la peau a besoin de nutrition.</li>
<li><strong>Gommage corps Pierre d'alun Fleur d'Oranger</strong> : le geste indispensable avant le karité. Il élimine les peaux mortes et permet au beurre de mieux pénétrer. La peau est immédiatement plus douce.</li>
<li><strong>Huile corps Douceurs Orientales</strong> : une huile sèche parfumée qui nourrit sans graisser. Parfaite en huile de massage ou en soin quotidien rapide après la douche.</li>
<li><strong>Gommage Pierre d'alun Figues Dattes</strong> : une version gourmande du gommage classique, aux senteurs chaudes et sucrées.</li>
<li><strong>Beurre de karité neutre</strong> : pour celles qui préfèrent les produits non parfumés ou qui ont la peau sensible aux fragrances.</li>
</ul>

<h2>Pour qui sont faits les soins Charme d'Orient ?</h2>

<ul>
<li><strong>Peaux sèches et très sèches</strong> : le beurre de karité est le soin par excellence pour les peaux qui tiraillent, notamment en hiver. Son pouvoir nourrissant est incomparable.</li>
<li><strong>Peaux ternes et rugueuses</strong> : le duo gommage + karité transforme littéralement la peau en une seule utilisation. L'éclat et la douceur sont immédiats.</li>
<li><strong>Toutes les peaux</strong> : les huiles corporelles et le lait corps conviennent à tous les types de peau pour une hydratation parfumée au quotidien.</li>
<li><strong>Femmes enceintes</strong> : le beurre de karité neutre (sans huiles essentielles) est un excellent soin pour prévenir les vergetures pendant la grossesse.</li>
</ul>

<h2>Mes conseils d'utilisation</h2>

<ul>
<li><strong>Le rituel hammam à la maison</strong> : sous une douche chaude, appliquez le gommage sur peau mouillée en mouvements circulaires. Rincez, puis appliquez le beurre de karité sur peau encore humide pour sceller l'hydratation. La peau est sublime.</li>
<li><strong>Le beurre en masque capillaire</strong> : appliquez une noisette de karité sur les longueurs et pointes, laissez poser 30 minutes avant le shampoing. Les cheveux secs et abîmés retrouvent douceur et brillance.</li>
<li><strong>L'huile en massage</strong> : réchauffez quelques gouttes entre vos paumes et massez les jambes, le ventre ou le dos pour un moment de détente parfumé.</li>
<li><strong>Le gommage hebdomadaire</strong> : une fois par semaine suffit pour maintenir une peau douce et lumineuse. En hiver, vous pouvez gommer deux fois par semaine si la peau est très sèche.</li>
</ul>

<h2>Questions fréquentes sur Charme d'Orient</h2>

<h3>Le beurre de karité convient-il au visage ?</h3>
<p>Les beurres de karité Charme d'Orient sont formulés pour le corps. Pour le visage, je recommande plutôt les soins JADEA ou Eskalia, plus adaptés à la finesse de la peau du visage. Cependant, en dépannage sur une zone très sèche (lèvres, nez irrité), le karité neutre peut être appliqué ponctuellement.</p>

<h3>Les gommages sont-ils trop abrasifs pour ma peau ?</h3>
<p>Non, la pierre d'alun est finement broyée et les gommages Charme d'Orient sont bien plus doux qu'ils n'en ont l'air. En appliquant sans appuyer sur peau mouillée, l'exfoliation est douce et progressive. Les peaux sensibles peuvent commencer par un gommage une fois par semaine.</p>

<h3>Combien de temps dure un pot de beurre de karité ?</h3>
<p>Un pot de 200 g dure en moyenne 2 à 3 mois en utilisation sur le corps entier deux à trois fois par semaine. Utilisé uniquement sur les mains et les zones sèches, il peut durer 4 à 6 mois.</p>

<h3>Charme d'Orient est-elle une marque éco-responsable ?</h3>
<p>Charme d'Orient s'engage pour des ingrédients naturels et un sourcing responsable du karité en Afrique de l'Ouest. Les produits sont fabriqués en France, et la marque réduit progressivement ses emballages plastiques au profit de contenants recyclables.</p>
HTML;
    }

    private function nakupendaContent(): string
    {
        return <<<'HTML'
<h2>L'histoire de Nakupenda : l'élégance au quotidien</h2>

<p>Nakupenda — « je t'aime » en swahili — est une marque de bijoux fantaisie qui allie élégance et résistance. Spécialisée dans les pièces en acier inoxydable doré, Nakupenda propose des bijoux raffinés conçus pour être portés chaque jour, sans craindre l'eau, la transpiration ni le temps qui passe.</p>

<p>La marque se distingue par son choix de l'acier inoxydable chirurgical, un matériau noble et durable qui ne noircit pas, ne rouille pas et ne provoque pas de réactions allergiques. Chaque pièce est soigneusement travaillée pour offrir le rendu lumineux de l'or, sans les contraintes ni le prix de l'or véritable.</p>

<h2>Pourquoi j'ai choisi Nakupenda pour la boutique</h2>

<p>Proposer des bijoux dans un institut de beauté, c'est une évidence pour moi. La beauté ne s'arrête pas aux soins : elle se prolonge dans les accessoires qui subliment la silhouette au quotidien. Quand une cliente sort d'un soin avec une peau lumineuse et un bijou Nakupenda au poignet, le résultat est complet.</p>

<p>J'ai choisi Nakupenda pour la qualité de ses pièces. Beaucoup de bijoux fantaisie perdent leur éclat après quelques semaines — ce n'est pas le cas ici. L'acier inoxydable garde son brillant même après des mois de port quotidien. Mes clientes me le confirment régulièrement : « Je ne l'ai jamais enlevé, et il est toujours aussi beau. »</p>

<p>Les prix accessibles permettent aussi de se faire plaisir sans se ruiner, ou d'offrir un cadeau élégant pour toute occasion.</p>

<h2>L'acier inoxydable : un matériau noble et durable</h2>

<p>Tous les bijoux Nakupenda sont fabriqués en acier inoxydable chirurgical (316L), le même alliage utilisé en chirurgie et en horlogerie de luxe. Ce choix n'est pas anodin :</p>

<ul>
<li><strong>Hypoallergénique</strong> : l'acier chirurgical ne contient pas de nickel libre, ce qui le rend parfaitement toléré par les peaux sensibles et allergiques. Vous pouvez le porter sans crainte d'irritation.</li>
<li><strong>Résistant à l'eau</strong> : douche, piscine, mer — vos bijoux Nakupenda ne craignent rien. Pas besoin de les retirer au quotidien.</li>
<li><strong>Inoxydable</strong> : contrairement au plaqué or classique, l'acier inoxydable ne noircit pas, ne ternit pas et ne s'oxyde pas avec le temps.</li>
<li><strong>Résistant aux rayures</strong> : la dureté de l'acier chirurgical protège vos bijoux des micro-rayures du quotidien.</li>
<li><strong>Dorure longue durée</strong> : le revêtement doré est appliqué par un procédé PVD (Physical Vapor Deposition) qui assure une tenue bien supérieure au plaqué or traditionnel.</li>
</ul>

<h2>Les collections Nakupenda</h2>

<p>La gamme Nakupenda se décline en quatre catégories de bijoux, toutes dans des tons dorés chauds et lumineux :</p>

<h3>Les bagues</h3>
<p>Des modèles variés, des plus épurés aux plus ornementés. Bagues fines à empiler, modèles avec pierre naturelle marron ou orangée, bagues chaîne, doubles bagues — il y en a pour tous les styles. Toutes sont en taille réglable, ce qui simplifie le choix et en fait un cadeau idéal.</p>

<h3>Les bracelets</h3>
<p>Bracelets en perles, chaînes dorées, doubles et triples rangs — la collection bracelets est la plus riche de la gamme. Les modèles se portent seuls pour un look minimaliste ou s'accumulent au poignet pour un effet bohème chic. Les tailles sont réglables.</p>

<h3>Les colliers</h3>
<p>Des chaînes fines et délicates, avec ou sans pendentif. Les colliers Nakupenda se portent au ras du cou ou en sautoir et se superposent facilement pour un look tendance.</p>

<h3>Les boucles d'oreilles</h3>
<p>Créoles, puces, pendantes — la collection boucles d'oreilles complète parfaitement une parure Nakupenda. Les modèles sont légers et confortables, conçus pour être portés du matin au soir.</p>

<h2>Comment choisir son bijou Nakupenda</h2>

<ul>
<li><strong>Pour un look minimaliste</strong> : optez pour une bague fine dorée et un bracelet chaîne simple. L'élégance dans la sobriété.</li>
<li><strong>Pour un style bohème</strong> : accumulez 2 ou 3 bracelets en perles et chaînes au même poignet, ajoutez une bague ornementée. Le mélange des textures crée un effet naturel et décontracté.</li>
<li><strong>Pour une occasion spéciale</strong> : la double bague avec pierre centrale ou les boucles d'oreilles pendantes apportent une touche d'éclat sans en faire trop.</li>
<li><strong>Pour offrir</strong> : les bagues et bracelets réglables sont un choix sûr — pas de risque de se tromper de taille. Le bijou est livré dans son écrin, prêt à offrir.</li>
</ul>

<h2>Mes conseils d'entretien</h2>

<p>Même si l'acier inoxydable est extrêmement résistant, quelques gestes simples prolongent l'éclat de vos bijoux :</p>

<ul>
<li><strong>Nettoyage régulier</strong> : un chiffon doux suffit pour redonner de l'éclat. Pour un nettoyage plus poussé, de l'eau tiède savonneuse et un rinçage à l'eau claire font des merveilles.</li>
<li><strong>Éviter les produits chimiques</strong> : parfum, crème et produits ménagers peuvent ternir le revêtement doré à long terme. Appliquez vos soins avant de mettre vos bijoux.</li>
<li><strong>Rangement</strong> : idéalement, rangez chaque bijou séparément pour éviter les frottements entre pièces. Un simple pochon en tissu suffit.</li>
</ul>

<h2>Questions fréquentes sur Nakupenda</h2>

<h3>Les bijoux Nakupenda sont-ils garantis sans nickel ?</h3>
<p>Oui, l'acier inoxydable chirurgical 316L utilisé par Nakupenda est garanti sans nickel libre. C'est le matériau recommandé pour les personnes allergiques au nickel.</p>

<h3>Puis-je garder mon bijou sous la douche ?</h3>
<p>Oui, c'est l'un des grands avantages de l'acier inoxydable. Vous pouvez porter vos bijoux Nakupenda sous la douche, à la piscine et même à la mer sans problème.</p>

<h3>La dorure va-t-elle s'estomper avec le temps ?</h3>
<p>Le procédé PVD utilisé par Nakupenda offre une dorure beaucoup plus résistante que le plaqué or traditionnel. En port quotidien normal, la dorure conserve son éclat pendant plusieurs années. Évitez simplement le contact prolongé avec des produits chimiques agressifs.</p>

<h3>Quelle taille choisir ?</h3>
<p>La plupart des bagues et bracelets Nakupenda sont réglables, donc pas de souci de taille ! Les colliers sont disponibles en longueurs standard. En cas de doute, n'hésitez pas à me contacter pour un conseil personnalisé.</p>

<h3>Puis-je commander en ligne ?</h3>
<p>Bien sûr ! Toute la collection Nakupenda est disponible sur notre boutique en ligne avec livraison rapide en Colissimo ou en point relais. La livraison est offerte dès 60 € d'achat. Vous pouvez aussi venir essayer les bijoux directement à l'institut.</p>
HTML;
    }
};
