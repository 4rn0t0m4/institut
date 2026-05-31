<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $page = DB::table('pages')
            ->where('slug', 'conditions-generales-de-ventes')
            ->orWhere('slug', 'cgv')
            ->orWhere('slug', 'conditions-generales-de-vente')
            ->first();

        if (! $page) {
            return;
        }

        DB::table('pages')->where('id', $page->id)->update([
            'title' => 'Conditions Générales de Vente',
            'slug' => 'conditions-generales-de-vente',
            'meta_title' => 'Conditions Générales de Vente - Institut Corps à Coeur',
            'meta_description' => 'Consultez les conditions générales de vente de la boutique en ligne Institut Corps à Coeur : livraison, paiement, retours, remboursements.',
            'content' => <<<'HTML'
<h3>Dernière mise à jour : 31 mai 2026</h3>

<p>Institut Corps à Coeur<br>
SIRET : 953 916 996 00028<br>
Siège social : 22 avenue Jean Jaurès, 14270 Mézidon-Vallée d'Auge<br>
Téléphone : 02 31 20 10 45<br>
Email : contact@institutcorpsacoeur.fr<br>
Responsable de la publication : Angélique CHANEL</p>

<p><strong>Conditions Générales de Vente des produits vendus sur institutcorpsacoeur.fr</strong></p>

<p><strong>Article 1 – Objet</strong></p>
<p>Les présentes Conditions Générales de Vente (CGV) régissent les ventes de produits cosmétiques et de bien-être réalisées par la société Institut Corps à Coeur, dont le siège social est situé 22 avenue Jean Jaurès, 14270 Mézidon-Vallée d'Auge, via le site internet <a href="https://www.institutcorpsacoeur.fr">https://www.institutcorpsacoeur.fr</a>.</p>
<p>Toute commande passée sur le site implique l'acceptation sans réserve des présentes CGV.</p>

<p><strong>Article 2 – Prix</strong></p>
<p>Les prix de nos produits sont indiqués en euros toutes taxes comprises (TVA 20 % incluse), hors frais de livraison.</p>
<p>La société Institut Corps à Coeur se réserve le droit de modifier ses prix à tout moment. Le produit sera facturé sur la base du tarif en vigueur au moment de la validation de la commande et sous réserve de disponibilité.</p>
<p>Les produits demeurent la propriété de la société Institut Corps à Coeur jusqu'au paiement complet du prix.</p>

<p><strong>Article 3 – Commandes</strong></p>
<p>Vous pouvez passer commande sur le site internet : <a href="https://www.institutcorpsacoeur.fr">https://www.institutcorpsacoeur.fr</a></p>
<p>Les informations contractuelles sont présentées en langue française et feront l'objet d'une confirmation par email au moment de la validation de votre commande.</p>
<p>La société Institut Corps à Coeur se réserve le droit de ne pas enregistrer un paiement et de ne pas confirmer une commande, notamment en cas de problème d'approvisionnement ou de difficulté concernant la commande reçue.</p>

<p><strong>Article 4 – Validation de votre commande</strong></p>
<p>Toute commande passée sur le site suppose l'adhésion aux présentes Conditions Générales de Vente. La confirmation de commande entraîne votre adhésion pleine et entière aux présentes conditions, sans exception ni réserve.</p>
<p>L'ensemble des données fournies et la confirmation enregistrée vaudront preuve de la transaction. La confirmation de commande vaudra signature et acceptation des opérations effectuées.</p>

<p><strong>Article 5 – Paiement</strong></p>
<p>Le fait de valider votre commande implique l'obligation de payer le prix indiqué.</p>
<p>Le règlement de vos achats s'effectue par carte bancaire via la plateforme de paiement sécurisé Stripe. Le débit de la carte est effectué au moment de la validation de la commande.</p>
<p>En cas d'échec du paiement, la commande est automatiquement annulée et vous en êtes informé par email.</p>

<p><strong>Article 6 – Droit de rétractation</strong></p>
<p>Conformément aux dispositions des articles L221-18 et suivants du Code de la consommation, vous disposez d'un délai de <strong>14 jours calendaires</strong> à compter de la réception de votre commande pour exercer votre droit de rétractation, sans avoir à justifier de motifs ni à payer de pénalités.</p>
<p>Pour exercer ce droit, vous devez nous notifier votre décision par email à <a href="mailto:contact@institutcorpsacoeur.fr">contact@institutcorpsacoeur.fr</a> ou par courrier à l'adresse suivante : Institut Corps à Coeur, 22 avenue Jean Jaurès, 14270 Mézidon-Vallée d'Auge.</p>
<p>Les produits doivent être retournés dans leur emballage d'origine, non ouverts et non utilisés, dans un délai de 14 jours suivant la notification de votre rétractation. Les frais de retour sont à la charge du client.</p>
<p><strong>Exceptions au droit de rétractation :</strong> Conformément à l'article L221-28 du Code de la consommation, le droit de rétractation ne peut être exercé pour :</p>
<ul>
<li>Les produits descellés après livraison et ne pouvant être renvoyés pour des raisons d'hygiène ou de protection de la santé (cosmétiques ouverts, produits de soin utilisés)</li>
<li>Les produits confectionnés selon les spécifications du consommateur ou nettement personnalisés</li>
</ul>

<p><strong>Article 7 – Remboursements et avoirs</strong></p>
<p>En cas de rétractation acceptée ou d'erreur de notre part, le remboursement est effectué dans un délai de 14 jours à compter de la réception du retour ou de la validation de votre demande.</p>
<p>Le remboursement s'effectue par le même moyen de paiement que celui utilisé lors de la commande (carte bancaire via Stripe). Un avoir vous est adressé par email avec le détail du montant remboursé.</p>
<p>En cas de produit retourné incomplet, abîmé, endommagé ou sali par le client, un remboursement partiel pourra être appliqué.</p>

<p><strong>Article 8 – Livraison</strong></p>
<p>Les produits sont livrés à l'adresse indiquée lors du processus de commande. Les délais d'expédition sont de 1 à 5 jours ouvrés.</p>
<p><strong>Modes de livraison disponibles :</strong></p>
<ul>
<li><strong>Livraison à domicile (Colissimo)</strong> : 7,90 € – Gratuite dès 60 € d'achats (France métropolitaine)</li>
<li><strong>Livraison en point relais (Mondial Relay / Chronopost)</strong> : 5,00 € – Gratuite dès 60 € d'achats (France métropolitaine). 5,90 € pour la Belgique, l'Espagne et l'Italie (gratuite dès 80 € d'achats)</li>
<li><strong>Retrait à l'institut</strong> : Gratuit – 22 avenue Jean Jaurès, 14270 Mézidon-Vallée d'Auge</li>
</ul>
<p>En cas de retard d'expédition, un email vous sera adressé pour vous en informer.</p>
<p>La société Institut Corps à Coeur ne peut être tenue responsable des retards de livraison imputables au transporteur. En cas de non-récupération du colis dans les délais impartis par le transporteur (14 jours pour La Poste, 7 jours pour les points relais), celui-ci sera retourné à l'expéditeur. Une nouvelle expédition pourra être effectuée après paiement de nouveaux frais de livraison.</p>
<p>Dès la prise de possession physique des produits, les risques de perte ou d'endommagement vous sont transférés.</p>

<p><strong>Article 9 – Disponibilité</strong></p>
<p>Nos produits sont proposés dans la limite des stocks disponibles. En cas d'indisponibilité d'un produit après passation de votre commande, nous vous en informerons par email. Vous aurez alors le choix entre un produit de substitution ou le remboursement de votre commande.</p>
<p>La société Institut Corps à Coeur se réserve le droit de refuser les commandes portant sur des quantités inhabituellement élevées.</p>

<p><strong>Article 10 – Garanties</strong></p>
<p>Tous les produits vendus sur le site bénéficient de la garantie légale de conformité (articles L217-4 et suivants du Code de la consommation) et de la garantie contre les vices cachés (articles 1641 et suivants du Code civil).</p>
<p>En cas de produit non conforme ou défectueux, vous pouvez nous contacter à <a href="mailto:contact@institutcorpsacoeur.fr">contact@institutcorpsacoeur.fr</a> pour obtenir un échange ou un remboursement.</p>

<p><strong>Article 11 – Responsabilité</strong></p>
<p>La responsabilité de la société Institut Corps à Coeur ne saurait être engagée en cas de mauvaise utilisation des produits achetés. Il est recommandé de respecter les précautions d'emploi indiquées sur chaque produit.</p>
<p>La responsabilité de la société Institut Corps à Coeur ne saurait être engagée pour les inconvénients ou dommages inhérents à l'utilisation du réseau Internet, notamment une rupture de service, une intrusion extérieure ou la présence de virus informatiques.</p>

<p><strong>Article 12 – Propriété intellectuelle</strong></p>
<p>Tous les éléments du site <a href="https://www.institutcorpsacoeur.fr">https://www.institutcorpsacoeur.fr</a> sont et restent la propriété intellectuelle et exclusive de la société Institut Corps à Coeur. Nul n'est autorisé à reproduire, exploiter, rediffuser ou utiliser à quelque titre que ce soit, même partiellement, des éléments du site qu'ils soient logiciels, visuels ou sonores.</p>

<p><strong>Article 13 – Données personnelles</strong></p>
<p>La société Institut Corps à Coeur collecte les données personnelles nécessaires à la gestion de votre commande et à l'amélioration de ses services. Ces données sont conservées de manière sécurisée et ne sont pas transmises à des tiers, sauf obligation légale ou nécessité liée à l'exécution de votre commande (transporteur, prestataire de paiement).</p>
<p>Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition aux données personnelles vous concernant. Vous pouvez exercer ces droits en nous contactant à <a href="mailto:contact@institutcorpsacoeur.fr">contact@institutcorpsacoeur.fr</a>.</p>

<p><strong>Article 14 – Droit applicable et litiges</strong></p>
<p>Les présentes conditions de vente sont soumises à la loi française. En cas de litige, les tribunaux français seront seuls compétents.</p>
<p>Conformément aux articles L.616-1 et R.616-1 du Code de la consommation, nous proposons un dispositif de médiation de la consommation. L'entité de médiation retenue est : <strong>CNPM – MEDIATION DE LA CONSOMMATION</strong>.</p>
<p>En cas de litige, vous pouvez déposer votre réclamation sur le site : <a href="https://cnpm-mediation-consommation.eu" target="_blank" rel="noopener">https://cnpm-mediation-consommation.eu</a><br>
ou par voie postale : CNPM – MEDIATION – CONSOMMATION, 27 avenue de la libération, 42400 Saint-Chamond.</p>
<p>Vous pouvez également recourir à la plateforme de Règlement en Ligne des Litiges (RLL) de la Commission européenne : <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener">https://ec.europa.eu/consumers/odr</a></p>
HTML,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Pas de rollback pour le contenu éditorial
    }
};
