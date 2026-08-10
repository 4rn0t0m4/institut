<?php

use App\Models\ProductCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $categories = [
            'cremes-visage' => [
                'meta_title' => 'Crèmes visage naturelles — Hydratation, anti-âge et éclat | Corps à Cœur',
                'meta_description' => 'Découvrez nos crèmes visage naturelles : hydratantes, anti-âge, nourrissantes. Sélectionnées par une esthéticienne pour tous les types de peau. Livraison rapide.',
                'content' => <<<'HTML'
<h2>Pourquoi hydrater son visage au quotidien ?</h2>

<p>L'hydratation est le geste fondamental de toute routine beauté. Chaque jour, votre peau perd naturellement de l'eau par évaporation — c'est ce qu'on appelle la perte insensible en eau. Sans une hydratation adaptée, la barrière cutanée s'affaiblit, la peau tiraille, les ridules se creusent et le teint perd son éclat.</p>

<p>Une bonne crème visage ne se contente pas d'apporter de l'eau : elle renforce le film hydrolipidique, cette fine couche protectrice qui retient l'humidité dans l'épiderme. C'est pourquoi même les peaux grasses ont besoin d'hydratation — ce n'est pas une question de gras, mais d'eau.</p>

<p>En institut, je constate chaque jour que les clientes qui hydratent leur peau régulièrement ont un grain de peau plus fin, un teint plus lumineux et des signes de l'âge moins marqués. C'est le geste le plus simple et le plus efficace que vous puissiez adopter.</p>

<h2>Comment choisir sa crème visage ?</h2>

<p>Le choix d'une crème visage dépend avant tout de votre type de peau et de vos besoins spécifiques. Voici les critères que j'utilise en institut pour orienter mes clientes :</p>

<ul>
<li><strong>Votre type de peau</strong> : sèche, mixte, grasse ou sensible — chaque type a des besoins différents en termes de texture et d'actifs.</li>
<li><strong>Vos préoccupations principales</strong> : déshydratation, rides, taches, manque d'éclat, rougeurs… La crème idéale cible votre problématique prioritaire.</li>
<li><strong>La saison</strong> : en hiver, privilégiez des textures plus riches et nourrissantes. En été, optez pour des fluides légers qui ne brillent pas.</li>
<li><strong>Votre âge</strong> : à partir de 30 ans, les besoins en antioxydants et en actifs repulpants augmentent progressivement.</li>
<li><strong>La qualité des ingrédients</strong> : préférez les formules avec des actifs naturels concentrés plutôt que des listes d'ingrédients à rallonge.</li>
</ul>

<h2>Les différents types de peau</h2>

<h3>Peau sèche</h3>
<p>La peau sèche manque de lipides (gras). Elle tiraille, desquame parfois et peut présenter des zones rugueuses. Elle a besoin de crèmes riches en huiles végétales et en beurres nourrissants comme le karité ou l'huile de coco. Les textures baume ou crème onctueuse sont idéales.</p>

<h3>Peau déshydratée</h3>
<p>Attention à ne pas confondre peau sèche et peau déshydratée ! La déshydratation est un état passager lié à un manque d'eau, pas de gras. Même une peau grasse peut être déshydratée. Les signes : ridules de déshydratation, inconfort, teint terne. Privilégiez les actifs hydratants comme l'acide hyaluronique, l'aloe vera ou la glycérine.</p>

<h3>Peau mixte à grasse</h3>
<p>La zone T (front, nez, menton) brille tandis que les joues restent normales ou sèches. Optez pour des textures légères — fluides, gels-crèmes — qui hydratent sans alourdir. Les actifs matifiants et les formules non comédogènes sont vos alliés.</p>

<h3>Peau sensible et réactive</h3>
<p>Rougeurs, picotements, inconfort… La peau sensible réagit facilement aux agressions extérieures. Choisissez des formules minimalistes, sans parfum de synthèse, avec des actifs apaisants comme la camomille, le calendula ou le bisabolol.</p>

<h3>Peau mature</h3>
<p>Avec le temps, la production de collagène et d'élastine diminue. La peau perd en fermeté, les rides se creusent. Les crèmes anti-âge combinent des actifs repulpants (acide hyaluronique), antioxydants (vitamine C, thé vert) et restructurants (rétinol, peptides).</p>

<h2>Les actifs à privilégier</h2>

<ul>
<li><strong>Acide hyaluronique</strong> : capable de retenir jusqu'à 1000 fois son poids en eau, c'est le champion de l'hydratation. Il repulpe et lisse les ridules.</li>
<li><strong>Beurre de karité</strong> : nourrissant et protecteur, il restaure le film hydrolipidique des peaux sèches et abîmées.</li>
<li><strong>Vitamine C</strong> : antioxydant puissant, elle unifie le teint, booste l'éclat et stimule la production de collagène.</li>
<li><strong>Huile de coco vierge</strong> : émolliente et antibactérienne, elle nourrit en profondeur sans obstruer les pores quand elle est de qualité.</li>
<li><strong>Aloe vera</strong> : hydratant, apaisant et cicatrisant, il convient à tous les types de peau, même les plus sensibles.</li>
<li><strong>Thé vert</strong> : riche en polyphénols antioxydants, il protège la peau des radicaux libres et du vieillissement prématuré.</li>
<li><strong>Huiles essentielles</strong> : ylang-ylang, rose, lavande… Utilisées à bon dosage, elles apportent des bénéfices ciblés et un plaisir sensoriel.</li>
</ul>

<h2>Ma sélection de crèmes visage</h2>

<p>Chaque crème proposée dans cette catégorie a été testée en institut pendant plusieurs semaines avant d'être mise en vente. Je les utilise en cabine sur mes clientes et j'observe les résultats sur différents types de peau. Seules celles qui tiennent leurs promesses rejoignent ma sélection.</p>

<p>Vous trouverez des crèmes de jour, des crèmes de nuit, des fluides hydratants et des soins ciblés anti-âge. Si vous hésitez entre plusieurs produits, n'hésitez pas à me contacter — un échange rapide me permet de vous orienter vers le soin le plus adapté à votre peau.</p>
HTML,
            ],

            'nettoyant-visage' => [
                'meta_title' => 'Nettoyants visage naturels — Démaquillage doux et efficace | Corps à Cœur',
                'meta_description' => 'Nettoyants visage naturels : fluides moussants, eaux micellaires, huiles démaquillantes. Nettoyage en douceur pour tous types de peau. Sélection d\'esthéticienne.',
                'content' => <<<'HTML'
<h2>Pourquoi le nettoyage du visage est essentiel</h2>

<p>Le nettoyage est l'étape la plus importante de votre routine beauté — et pourtant la plus négligée. Au cours de la journée, votre peau accumule sébum, cellules mortes, pollution, maquillage et résidus de crème solaire. Si ces impuretés ne sont pas éliminées correctement, elles obstruent les pores, ternissent le teint et empêchent vos soins de pénétrer efficacement.</p>

<p>En institut, je commence toujours par un nettoyage minutieux avant tout soin. Et je constate systématiquement la même chose : une peau bien nettoyée absorbe mieux les actifs, réagit mieux aux soins et retrouve plus vite son éclat naturel.</p>

<h2>Comment bien nettoyer son visage ?</h2>

<p>Le secret d'un bon nettoyage, c'est la douceur. Un nettoyant trop agressif décape la peau, détruit son film protecteur et provoque un effet rebond (la peau produit encore plus de sébum pour compenser). Voici la méthode que je recommande :</p>

<ul>
<li><strong>Le soir</strong> : c'est le nettoyage le plus important. Commencez par un démaquillant (huile ou fluide) pour dissoudre le maquillage et le sébum, puis rincez avec un nettoyant moussant doux pour éliminer les dernières traces. C'est le principe du double nettoyage.</li>
<li><strong>Le matin</strong> : un nettoyage léger suffit. Un fluide moussant doux ou même de l'eau micellaire pour retirer l'excès de sébum nocturne sans agresser la peau.</li>
<li><strong>Évitez l'eau trop chaude</strong> : elle dessèche et irrite. L'eau tiède est idéale pour ouvrir légèrement les pores sans agresser.</li>
<li><strong>Séchez en tamponnant</strong> : ne frottez jamais votre visage avec la serviette. Tamponnez délicatement pour absorber l'eau.</li>
</ul>

<h2>Quel nettoyant pour votre type de peau ?</h2>

<h3>Peaux sèches et sensibles</h3>
<p>Préférez les textures laiteuses ou huileuses, sans tensioactifs agressifs. Les huiles démaquillantes et les laits nettoyants respectent le film hydrolipidique et ne provoquent ni tiraillements ni inconfort.</p>

<h3>Peaux mixtes à grasses</h3>
<p>Les fluides moussants et les gels nettoyants sont vos alliés. Ils éliminent efficacement l'excès de sébum tout en purifiant les pores. Recherchez des formules sans savon (syndets) qui nettoient sans décaper.</p>

<h3>Peaux normales</h3>
<p>Vous avez le choix ! Les eaux micellaires et les fluides moussants doux conviennent parfaitement au quotidien. Variez les textures selon la saison et vos envies.</p>

<h2>Les actifs nettoyants à rechercher</h2>

<ul>
<li><strong>Huile de coco</strong> : dissout le maquillage, même waterproof, tout en nourrissant la peau.</li>
<li><strong>Aloe vera</strong> : apaise et hydrate pendant le nettoyage, idéal pour les peaux sensibles.</li>
<li><strong>Extraits de fruits (papaye, mangue)</strong> : enzymes naturelles qui exfolient en douceur et illuminent le teint.</li>
<li><strong>Eau de rose</strong> : tonifiante et apaisante, elle complète parfaitement le nettoyage.</li>
</ul>

<h2>Ma sélection de nettoyants</h2>

<p>Les nettoyants que je propose ont tous été testés en institut. Je les utilise quotidiennement en cabine et je les choisis pour leur efficacité, leur douceur et leur plaisir d'utilisation. Un bon nettoyant doit laisser la peau propre, douce et confortable — jamais tiraillée ni desséchée.</p>
HTML,
            ],

            'gommage-et-masque' => [
                'meta_title' => 'Gommages et masques visage naturels — Éclat et peau nette | Corps à Cœur',
                'meta_description' => 'Gommages et masques visage naturels pour une peau nette et éclatante. Exfoliants doux, masques purifiants et hydratants. Sélection professionnelle d\'institut.',
                'content' => <<<'HTML'
<h2>Pourquoi exfolier et faire des masques ?</h2>

<p>Le renouvellement cellulaire ralentit avec l'âge : les cellules mortes s'accumulent en surface, le teint devient terne, les pores se bouchent et les soins pénètrent moins bien. Le gommage accélère ce renouvellement naturel pour révéler une peau plus fraîche, plus lisse et plus lumineuse.</p>

<p>Le masque, lui, apporte un soin intensif ciblé. En 10 à 20 minutes, il délivre une concentration d'actifs bien supérieure à celle d'une crème quotidienne. C'est le geste « coup d'éclat » par excellence.</p>

<p>En institut, le duo gommage + masque est la base de tout soin visage. Et les résultats sont toujours spectaculaires : la peau est transformée en une seule séance.</p>

<h2>Comment bien exfolier son visage ?</h2>

<ul>
<li><strong>Fréquence</strong> : 1 à 2 fois par semaine selon votre type de peau. Les peaux sensibles se limiteront à une fois par semaine, les peaux grasses peuvent aller jusqu'à deux.</li>
<li><strong>Sur peau propre et humide</strong> : nettoyez d'abord, puis appliquez le gommage sur peau mouillée pour que les grains glissent sans agresser.</li>
<li><strong>Gestes doux et circulaires</strong> : ne frottez jamais fort. Laissez les grains faire le travail. Insistez légèrement sur la zone T (front, nez, menton) où les pores sont plus dilatés.</li>
<li><strong>Rincez à l'eau tiède</strong> : puis appliquez immédiatement un masque ou un sérum pour profiter de la meilleure pénétration des actifs.</li>
</ul>

<h2>Les différents types de gommages</h2>

<h3>Gommages mécaniques (à grains)</h3>
<p>Ils contiennent des micro-particules (sucre, sel, poudre de riz, noyaux broyés) qui exfolient par friction douce. Résultat immédiat : la peau est lisse au toucher. Préférez les grains fins et ronds qui n'irritent pas.</p>

<h3>Gommages enzymatiques</h3>
<p>Sans grains, ils utilisent des enzymes de fruits (papaye, ananas) pour dissoudre les cellules mortes en douceur. Parfaits pour les peaux sensibles et réactives qui ne supportent pas la friction.</p>

<h2>Les différents types de masques</h2>

<h3>Masques hydratants</h3>
<p>Gorgés d'acide hyaluronique, d'aloe vera ou de miel, ils regonflent la peau en eau et effacent les ridules de déshydratation. Idéaux après un voyage, en hiver ou quand la peau tiraille.</p>

<h3>Masques purifiants</h3>
<p>À base d'argile, de charbon ou de zinc, ils absorbent l'excès de sébum, resserrent les pores et préviennent les imperfections. Parfaits pour les peaux mixtes à grasses.</p>

<h3>Masques éclat</h3>
<p>Riches en vitamine C, en extraits de fruits ou en AHA légers, ils unifient le teint, atténuent les taches et redonnent de la luminosité aux peaux ternes et fatiguées.</p>

<h2>Le rituel gommage + masque parfait</h2>

<p>Voici le rituel que je pratique en institut et que vous pouvez reproduire chez vous chaque semaine :</p>

<ul>
<li><strong>Étape 1</strong> : Nettoyez votre visage avec un fluide moussant doux.</li>
<li><strong>Étape 2</strong> : Appliquez le gommage sur peau humide. Massez doucement pendant 1 à 2 minutes.</li>
<li><strong>Étape 3</strong> : Rincez à l'eau tiède.</li>
<li><strong>Étape 4</strong> : Appliquez un masque adapté à votre besoin du moment. Laissez poser 10 à 15 minutes.</li>
<li><strong>Étape 5</strong> : Rincez, puis appliquez votre sérum et votre crème habituelle.</li>
</ul>

<p>Tous les gommages et masques de cette sélection ont été testés en cabine. Je les choisis pour leur efficacité, leur douceur et la qualité de leurs actifs naturels.</p>
HTML,
            ],

            'huiles-de-douche' => [
                'meta_title' => 'Huiles de douche naturelles — Nettoyage et nutrition corps | Corps à Cœur',
                'meta_description' => 'Huiles de douche naturelles qui nettoient et nourrissent la peau en un seul geste. Textures fondantes, parfums enveloppants. Sélection d\'esthéticienne.',
                'content' => <<<'HTML'
<h2>Pourquoi choisir une huile de douche ?</h2>

<p>Les gels douche classiques contiennent souvent des tensioactifs agressifs qui décapent le film hydrolipidique de la peau. Résultat : tiraillements, sécheresse, inconfort. L'huile de douche nettoie différemment — elle se transforme en une mousse légère au contact de l'eau et élimine les impuretés tout en déposant un film protecteur et nourrissant sur la peau.</p>

<p>En sortant de la douche, la différence est immédiate : la peau est douce, souple et confortable, sans avoir besoin d'appliquer un lait corporel. C'est le geste idéal pour les personnes pressées ou les peaux qui tiraillent après la douche.</p>

<h2>Pour quel type de peau ?</h2>

<ul>
<li><strong>Peaux sèches et très sèches</strong> : l'huile de douche est votre meilleure alliée. Elle nettoie sans dessécher et restaure le confort cutané.</li>
<li><strong>Peaux sensibles et atopiques</strong> : les formules sans savon à base d'huiles végétales respectent les peaux réactives et apaisent les irritations.</li>
<li><strong>Peaux normales</strong> : un geste plaisir qui prévient la déshydratation et laisse la peau satinée au quotidien.</li>
<li><strong>En hiver</strong> : quand le froid et le chauffage assèchent la peau, l'huile de douche compense en apportant nutrition et protection.</li>
</ul>

<h2>Comment utiliser une huile de douche ?</h2>

<p>Appliquez l'huile sur peau mouillée et massez : elle se transforme en mousse au contact de l'eau. Rincez normalement. Le film protecteur reste sur la peau sans sensation de gras. Pour un effet encore plus nourrissant, appliquez sur peau sèche avant de mouiller — l'émulsion sera plus riche.</p>

<h2>Les actifs qui font la différence</h2>

<ul>
<li><strong>Huile de coco vierge</strong> : ultra-nourrissante, elle laisse un voile satiné et parfumé sur la peau.</li>
<li><strong>Beurre de karité</strong> : protecteur et réparateur, il restaure les peaux les plus sèches et abîmées.</li>
<li><strong>Huile d'argan</strong> : riche en vitamine E et en oméga, elle assouplit et régénère la peau.</li>
<li><strong>Glycérine végétale</strong> : humectant naturel qui attire et retient l'eau dans l'épiderme.</li>
</ul>

<p>Mes huiles de douche sont sélectionnées pour leur pouvoir nourrissant, leurs parfums enveloppants et la qualité de leurs ingrédients naturels. Le bain et la douche deviennent un vrai moment de bien-être.</p>
HTML,
            ],

            'gommages-corps' => [
                'meta_title' => 'Gommages corps naturels — Exfoliation douce et peau satinée | Corps à Cœur',
                'meta_description' => 'Gommages corps naturels au sucre et sel marin. Exfoliation douce pour une peau lisse, douce et lumineuse. Testés en institut, résultats immédiats.',
                'content' => <<<'HTML'
<h2>Pourquoi gommer son corps régulièrement ?</h2>

<p>La peau du corps se renouvelle tous les 28 jours environ, mais ce cycle ralentit avec l'âge. Les cellules mortes s'accumulent en surface, rendant la peau rugueuse, terne et sèche. Le gommage corps accélère ce renouvellement naturel et offre des résultats immédiats : une peau incroyablement douce, lisse et lumineuse dès le premier geste.</p>

<p>Mais le gommage ne fait pas que lisser — il stimule aussi la microcirculation sanguine, favorise la pénétration des soins hydratants et prépare la peau au bronzage (un bronzage sur peau gommée est plus uniforme et dure plus longtemps).</p>

<h2>Les bienfaits du gommage corps</h2>

<ul>
<li><strong>Peau douce et lisse</strong> : les grains éliminent les cellules mortes et les rugosités, surtout sur les coudes, genoux et talons.</li>
<li><strong>Éclat retrouvé</strong> : en éliminant le voile terne des cellules mortes, la peau retrouve sa luminosité naturelle.</li>
<li><strong>Meilleure absorption des soins</strong> : après un gommage, baumes et huiles pénètrent beaucoup mieux et sont plus efficaces.</li>
<li><strong>Prévention des poils incarnés</strong> : l'exfoliation régulière libère les poils sous-cutanés et prévient les petits boutons post-épilation.</li>
<li><strong>Stimulation de la circulation</strong> : le massage pendant le gommage active la microcirculation, ce qui aide à lutter contre la peau d'orange.</li>
</ul>

<h2>Comment réaliser un bon gommage corps ?</h2>

<p>Sous la douche, sur peau mouillée, prélevez une noisette de gommage et massez en cercles larges du bas vers le haut (toujours en remontant vers le cœur). Insistez sur les zones rugueuses : coudes, genoux, pieds, cuisses. Rincez à l'eau tiède. La peau est immédiatement satinée.</p>

<p>Fréquence idéale : une fois par semaine. Avant une épilation ou un autobronzant, gommez la veille pour un résultat impeccable.</p>

<h2>Gommage au sucre ou au sel ?</h2>

<ul>
<li><strong>Gommage au sucre</strong> : plus doux, grains qui fondent au contact de la peau. Idéal pour les peaux sensibles et pour le visage. Effet hydratant car le sucre retient l'eau.</li>
<li><strong>Gommage au sel marin</strong> : plus tonique, effet drainant et reminéralisant. Parfait pour les jambes lourdes et les cuisses. Évitez après l'épilation (ça pique !).</li>
</ul>

<p>Tous les gommages corps de ma sélection sont enrichis en huiles végétales — après le rinçage, la peau est douce et nourrie sans avoir besoin d'appliquer un soin supplémentaire. C'est le geste plaisir par excellence.</p>
HTML,
            ],

            'baume-reparateur' => [
                'meta_title' => 'Baumes réparateurs naturels — Soin intense peaux sèches | Corps à Cœur',
                'meta_description' => 'Baumes réparateurs naturels pour peaux sèches et abîmées. Nutrition intense mains, pieds, corps. Au beurre de karité et huiles végétales. Testés en institut.',
                'content' => <<<'HTML'
<h2>Qu'est-ce qu'un baume réparateur ?</h2>

<p>Le baume réparateur est le soin le plus concentré et le plus nourrissant de votre routine corps. Contrairement à un lait ou une crème corporelle, le baume a une texture riche et dense qui forme un véritable bouclier protecteur sur la peau. Il répare les zones les plus sèches et abîmées là où les soins classiques ne suffisent pas.</p>

<p>C'est le produit que je recommande systématiquement en institut dès que les premiers froids arrivent, mais aussi toute l'année pour les zones chroniquement sèches : mains, pieds, coudes, lèvres. En quelques applications, les crevasses se referment, les rugosités disparaissent et la peau retrouve son confort.</p>

<h2>Pour qui et pour quoi ?</h2>

<ul>
<li><strong>Mains très sèches</strong> : lavages fréquents, froid, produits ménagers… Les mains sont les premières victimes de la sécheresse. Le baume les répare et les protège.</li>
<li><strong>Pieds abîmés</strong> : talons fendillés, callosités, sécheresse extrême. Appliquez le baume en couche épaisse le soir sous des chaussettes en coton pour un soin de nuit intensif.</li>
<li><strong>Coudes et genoux rugueux</strong> : ces zones sans glandes sébacées se dessèchent facilement. Le baume nourrit en profondeur et lisse les rugosités.</li>
<li><strong>Lèvres gercées</strong> : en version mini ou multi-usage, le baume protège et répare les lèvres desséchées par le froid ou le vent.</li>
<li><strong>Après-soleil</strong> : sur les peaux tiraillées par le soleil, le baume apaise et restaure le confort en quelques minutes.</li>
</ul>

<h2>Les ingrédients clés d'un bon baume</h2>

<ul>
<li><strong>Beurre de karité</strong> : l'ingrédient star. Riche en vitamines A, E et F, il nourrit, protège et aide à la cicatrisation.</li>
<li><strong>Cire d'abeille</strong> : forme un film protecteur qui retient l'hydratation sans étouffer la peau.</li>
<li><strong>Huile de coco vierge</strong> : pénètre rapidement pour nourrir en profondeur avec un fini satiné.</li>
<li><strong>Vitamine E</strong> : antioxydant qui protège les cellules et favorise la régénération cutanée.</li>
</ul>

<h2>Comment utiliser un baume réparateur ?</h2>

<p>Le baume s'applique en petite quantité sur les zones ciblées. Sa texture riche fond au contact de la chaleur de la peau — réchauffez-le entre vos paumes avant d'appliquer pour une meilleure absorption. Le soir, en couche épaisse sur les mains ou les pieds, il agit toute la nuit pour un résultat spectaculaire au réveil.</p>

<p>Tous les baumes de cette sélection sont des multi-usages : un seul pot remplace votre crème mains, votre soin pieds et votre baume à lèvres. C'est l'indispensable à toujours avoir dans son sac.</p>
HTML,
            ],

            'cremes-mains-huiles-lait' => [
                'meta_title' => 'Crèmes mains, huiles et laits corps — Nutrition quotidienne | Corps à Cœur',
                'meta_description' => 'Crèmes mains, huiles sèches et laits pour le corps. Nutrition quotidienne, peau douce et parfumée. Cosmétiques naturels testés en institut.',
                'content' => <<<'HTML'
<h2>Prendre soin de son corps au quotidien</h2>

<p>La peau du corps est souvent la grande oubliée des routines beauté. On pense au visage, mais le corps représente 90 % de notre surface cutanée — et il mérite autant d'attention. Un soin quotidien adapté fait toute la différence : peau douce, souple, parfumée et protégée des agressions extérieures.</p>

<p>Dans cette catégorie, vous trouverez les soins du quotidien — plus légers qu'un baume, ils s'intègrent facilement dans votre routine après la douche. Crèmes mains à glisser dans le sac, huiles sèches pour le corps, laits hydratants… Chaque produit a été testé en institut pour son efficacité et son plaisir d'utilisation.</p>

<h2>Crèmes mains : le geste essentiel</h2>

<p>Nos mains sont exposées en permanence : eau, savon, froid, ménage, gel hydroalcoolique… Elles vieillissent deux fois plus vite que le visage si on ne les protège pas. Une bonne crème mains hydrate, protège et peut même atténuer les taches brunes quand elle contient des actifs ciblés.</p>

<p>Mon conseil : appliquez votre crème mains après chaque lavage, pas seulement quand elles tiraillent. C'est le geste préventif qui fait toute la différence.</p>

<h2>Huiles sèches : la nutrition express</h2>

<p>L'huile sèche est le produit magique pour les personnes qui n'aiment pas la sensation grasse des crèmes corporelles. Elle pénètre instantanément, laisse la peau satinée et délicatement parfumée. On peut même l'utiliser sur les cheveux pour les pointes sèches ou en touche de brillance sur le décolleté.</p>

<h2>Laits corporels : l'hydratation confort</h2>

<p>Le lait corporel est le soin le plus polyvalent pour le corps. Sa texture fluide s'étale facilement sur de grandes surfaces et pénètre rapidement. Enrichi en actifs nourrissants, il maintient l'hydratation toute la journée et laisse un voile confortable sans effet collant.</p>

<h2>Quand appliquer ses soins corps ?</h2>

<ul>
<li><strong>Après la douche</strong> : sur peau encore légèrement humide, les soins pénètrent mieux et l'hydratation est optimale.</li>
<li><strong>Les mains</strong> : après chaque lavage et le soir avant de dormir en couche plus épaisse.</li>
<li><strong>Huile sèche</strong> : matin ou soir, sur peau sèche, en massage rapide. Parfaite aussi en été sur les jambes pour un effet lumineux.</li>
</ul>

<p>Tous les produits de cette sélection allient efficacité, textures agréables et parfums enveloppants. Le soin du corps devient un vrai moment de plaisir.</p>
HTML,
            ],

            'coffrets-cadeaux' => [
                'meta_title' => 'Coffrets cadeaux beauté naturelle — Idées cadeaux soins | Corps à Cœur',
                'meta_description' => 'Coffrets cadeaux beauté : soins visage, corps et bien-être. Compositions élégantes prêtes à offrir. Cosmétiques naturels sélectionnés par une esthéticienne.',
                'content' => <<<'HTML'
<h2>Offrir un coffret beauté : le cadeau qui fait toujours plaisir</h2>

<p>Un coffret beauté, c'est bien plus qu'un cadeau : c'est une invitation à prendre soin de soi. Chaque coffret réunit une sélection de produits complémentaires, pensée pour créer un rituel de soin complet. C'est le cadeau idéal pour un anniversaire, Noël, la fête des mères, ou simplement pour faire plaisir.</p>

<p>Tous les coffrets sont composés à partir des produits que j'utilise en institut. Ce ne sont pas des assemblages marketing, mais des associations que je recommande à mes clientes parce qu'elles fonctionnent ensemble et se complètent parfaitement.</p>

<h2>Pourquoi choisir un coffret plutôt qu'un produit seul ?</h2>

<ul>
<li><strong>Découverte</strong> : un coffret permet de tester plusieurs produits d'une gamme et de trouver ses favoris.</li>
<li><strong>Rituel complet</strong> : nettoyant + soin + finition — les produits sont pensés pour fonctionner ensemble.</li>
<li><strong>Présentation cadeau</strong> : emballage soigné, prêt à offrir, sans avoir à chercher du papier cadeau.</li>
<li><strong>Économie</strong> : les coffrets sont souvent plus avantageux que les produits achetés séparément.</li>
</ul>

<h2>Pour qui ?</h2>

<p>Quel que soit le profil de la personne à qui vous souhaitez offrir un coffret, vous trouverez une composition adaptée :</p>

<ul>
<li><strong>Pour elle</strong> : coffrets soins visage, rituels corps, découvertes parfumées.</li>
<li><strong>Pour les amatrices de bien-être</strong> : bougies parfumées, huiles de massage, gommages et baumes.</li>
<li><strong>Pour les peaux exigeantes</strong> : coffrets anti-âge, hydratation intense ou soins ciblés.</li>
<li><strong>Pour les voyageuses</strong> : formats mini, trousses de voyage, essentiels nomades.</li>
</ul>

<p>Si vous hésitez sur le choix du coffret, contactez-moi : je vous guiderai en fonction de la personne et de son type de peau.</p>
HTML,
            ],

            'produits-visage' => [
                'meta_title' => 'Soins visage naturels — Crèmes, nettoyants, gommages et masques | Corps à Cœur',
                'meta_description' => 'Toute notre gamme de soins visage naturels : crèmes hydratantes, nettoyants, gommages, masques. Sélectionnés par une esthéticienne pour tous types de peau.',
                'content' => <<<'HTML'
<h2>Des soins visage sélectionnés en institut</h2>

<p>Le visage est la partie du corps la plus exposée aux agressions quotidiennes : UV, pollution, froid, stress. C'est aussi la peau la plus fine et la plus fragile. Elle mérite des soins spécifiques, formulés avec des actifs concentrés et des textures adaptées.</p>

<p>En tant qu'esthéticienne, je teste chaque produit en cabine pendant plusieurs semaines avant de le proposer à la vente. Je les utilise sur des peaux différentes — sèches, grasses, sensibles, matures — pour vérifier leur efficacité et leur tolérance. Seuls les soins qui tiennent leurs promesses rejoignent cette sélection.</p>

<h2>Une routine visage en 3 étapes</h2>

<p>Pas besoin de 10 produits pour avoir une belle peau. Trois gestes suffisent :</p>

<ul>
<li><strong>Nettoyer</strong> : matin et soir, avec un nettoyant doux adapté à votre type de peau. C'est la base de tout.</li>
<li><strong>Hydrater</strong> : une crème de jour et/ou de nuit pour maintenir la barrière cutanée et protéger des agressions.</li>
<li><strong>Exfolier</strong> : 1 fois par semaine, un gommage doux suivi d'un masque pour booster l'éclat et accélérer le renouvellement cellulaire.</li>
</ul>

<p>Parcourez nos sous-catégories pour trouver les soins adaptés à chaque étape de votre routine.</p>
HTML,
            ],

            'produits-corps' => [
                'meta_title' => 'Soins corps naturels — Huiles, gommages, baumes et laits | Corps à Cœur',
                'meta_description' => 'Soins corps naturels : huiles de douche, gommages, baumes réparateurs, laits hydratants. Pour une peau douce et nourrie au quotidien. Sélection d\'institut.',
                'content' => <<<'HTML'
<h2>Prendre soin de son corps : des rituels qui changent tout</h2>

<p>La peau du corps représente près de 2 m² de surface — et chaque zone a ses besoins spécifiques. Les mains s'assèchent au contact de l'eau, les pieds subissent les frottements, les jambes ont besoin de tonification, le buste demande de la fermeté. Des soins ciblés et réguliers font une vraie différence sur le confort, l'apparence et la santé de votre peau.</p>

<p>Dans cette catégorie, vous trouverez tout ce qu'il faut pour une routine corps complète : du nettoyage à la nutrition intense, en passant par l'exfoliation et l'hydratation quotidienne.</p>

<h2>Le rituel corps idéal</h2>

<ul>
<li><strong>Sous la douche</strong> : une huile de douche qui nettoie et nourrit en un seul geste, sans sensation de tiraillement.</li>
<li><strong>1 fois par semaine</strong> : un gommage corps au sucre ou au sel pour éliminer les cellules mortes et retrouver une peau satinée.</li>
<li><strong>Après la douche</strong> : un lait corporel, une huile sèche ou un baume pour maintenir l'hydratation et protéger la peau.</li>
<li><strong>Les zones sèches</strong> : un baume réparateur concentré sur les mains, pieds, coudes et lèvres.</li>
</ul>

<p>Tous les soins corps de cette sélection sont formulés avec des ingrédients naturels, testés en institut et choisis pour leur efficacité et leur plaisir d'utilisation.</p>
HTML,
            ],

            'parfums-et-bougies-bijou' => [
                'meta_title' => 'Parfums et bougies bijou — Senteurs naturelles et cadeaux | Corps à Cœur',
                'meta_description' => 'Parfums délicats et bougies bijou artisanales. Senteurs enveloppantes, bijou surprise à l\'intérieur. Idée cadeau originale. Sélection Corps à Cœur.',
                'content' => <<<'HTML'
<h2>Des parfums et bougies pour éveiller les sens</h2>

<p>Le parfum est le prolongement naturel d'une routine beauté. Après avoir pris soin de votre peau avec des soins de qualité, le parfum vient compléter l'expérience en laissant un sillage délicat et personnel. Les parfums de notre sélection sont des eaux parfumées douces, aux notes naturelles, qui se portent au quotidien.</p>

<h2>Les bougies bijou : un cadeau deux en un</h2>

<p>Les bougies bijou sont des bougies parfumées longue durée qui cachent un bijou à l'intérieur — bague, bracelet ou collier. Au fur et à mesure que la bougie fond, le bijou se révèle dans son écrin protecteur. C'est un cadeau original qui fait toujours son effet : le plaisir d'une bougie parfumée et la surprise d'un bijou à découvrir.</p>

<ul>
<li><strong>Parfums enveloppants</strong> : vanille, fleur de tiaré, rose, santal… Des fragrances qui parfument agréablement votre intérieur pendant des dizaines d'heures.</li>
<li><strong>Bijou surprise</strong> : chaque bougie contient un bijou fantaisie de qualité (bague, bracelet, pendentif) dont la valeur peut aller jusqu'à 250€.</li>
<li><strong>Cadeau prêt à offrir</strong> : emballage soigné, présentation élégante — il ne reste qu'à emballer !</li>
</ul>

<p>Parfums à porter et bougies à offrir : retrouvez notre sélection dans les sous-catégories ci-dessous.</p>
HTML,
            ],
        ];

        foreach ($categories as $slug => $data) {
            ProductCategory::where('slug', $slug)->update($data);
        }
    }

    public function down(): void
    {
        ProductCategory::whereIn('slug', [
            'cremes-visage', 'nettoyant-visage', 'gommage-et-masque',
            'huiles-de-douche', 'gommages-corps', 'baume-reparateur',
            'cremes-mains-huiles-lait', 'coffrets-cadeaux',
            'produits-visage', 'produits-corps', 'parfums-et-bougies-bijou',
        ])->update(['content' => null, 'meta_title' => null, 'meta_description' => null]);
    }
};
