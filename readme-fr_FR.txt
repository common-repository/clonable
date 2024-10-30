===== Clonable - Traduire un site Woocommerce / WordPress. Multilingue en 5 minutes. ===
Contributors : clonable
Tags : traductions, traduire, multilingue, clonable, seo
Requires at least: 5.0
6.6.2
Requires PHP: 7.2
Stable tag: 2.2.6
License: GPL v2 or later

Traduisez et maintenez vos sites Web multilingues en toute transparence. Accélérez et simplifiez votre internationalisation avec Clonable.

== Description ==
L'internationalisation en ligne sans tracas :  Accélérez et simplifiez vos processus de traduction. Votre site Web multilingue est mis à jour automatiquement.

= Le défi =
La traduction d'un site WordPress / Woocommerce peut s'avérer à la fois coûteuse et chronophage. Au-delà de l'effort de traduction initial, une maintenance continue est essentielle pour s'assurer que la version traduite reste à jour avec le nouveau contenu. Par conséquent, les traductions sont souvent négligées après leur création initiale, malgré le potentiel substantiel des marchés étrangers.

= Notre solution =
Nous vous présentons Clonable, une solution révolutionnaire pour les propriétaires de sites web WordPress. Clonable permet de créer sans effort des "clones" de vos sites Web WordPress, en veillant à ce qu'ils soient toujours synchronisés avec la version originale. Toute modification apportée au site d'origine est instantanément répercutée sur le clone, ce qui réduit considérablement les coûts de maintenance. Avec Clonable, vous pouvez générer un clone de site web en seulement 5 minutes, ce qui réduit considérablement les délais de mise sur le marché.

= Caractéristiques principales =
1. Amélioration du référencement : Notre plugin ajoute de manière transparente des balises de langue à la section d'en-tête de toutes vos pages. Cela garantit que vos clones et le site d'origine sont liés de manière appropriée pour une meilleure performance SEO. Cette fonctionnalité s'étend aux clones traduits et non traduits et prend également en charge les clones de sous-dossiers.
2. Intégration sans effort des sous-dossiers : Clonable vous permet d'incorporer sans effort un clone dans un sous-dossier de votre site web, éliminant ainsi le besoin de configurations techniques complexes au sein de WordPress.
3. Un sélecteur de langue convivial : Clonable offre également un sélecteur de langue intuitif, permettant aux utilisateurs de naviguer sans effort entre les différentes langues disponibles sur votre site Web.
4. Prise en charge de Woocommerce pour faciliter le suivi des conversions dans différentes langues.

== Installation ==
Vous aurez besoin d'un [compte Clonable] (https://app.clonable.net/register) pour utiliser le plugin. Lorsque vous avez connecté avec succès le plugin à votre compte Clonable, les paramètres se synchronisent automatiquement avec votre installation WordPress.
synchroniseront automatiquement avec votre installation WordPress.

Si les paramètres ne sont pas synchronisés correctement, vous pouvez le faire manuellement en cliquant sur le bouton 'Sync with Clonable' sur la page des paramètres généraux du plugin.

== Changelog ==
v2.2.6
Bug fix: timeouts

v2.2.5
Improve circuit breaker logic

v2.2.4
Correction d'un bug avec un emplacement d'enregistrement incorrect dans les balises de langue.

v2.2.3
Correction d'un problème dans les balises de langue des paramètres du clone

v2.2.2
Intégration de vidéos dans le plugin.
Ajout d'options de langue uniquement aux balises de langue

v2.2.1
Amélioration de la sensibilité des circuit breaker.

v2.2.0
Ajout du support de WordPress 6.6 et augmentation de la version minimale de PHP.
Ajout d'un circuit breaker pour la communication des sous-dossiers avec Clonable.
Ajout d'une fenêtre de paramétrage pour activer et désactiver des services spécifiques.

v2.1.16
Meilleures valeurs par défaut pour les options Clonable.
Ajout d'une option pour obtenir la locale des clones en utilisant la fonction WordPress get_locale().
Correction : Désactivation de la boucle de proxy pour les sites avec plusieurs sous-dossiers.

v2.1.15
Amélioration des performances pour la communication avec l'API

v2.1.14
Bugfix : Correction de l'algorithme Debounce
Mise à jour de la version testée

v2.1.13
Correction : Résolution du problème avec le drapeau norvégien qui ne pouvait pas être sélectionné dans le sélecteur de langue.

v2.1.12
Bug fix

v2.1.11
Prise en charge de site_url et home_url avec un sous-répertoire.

v2.1.10
Amélioration des performances de l'interface d'administration.

v2.1.9
Correction : Correction d'une erreur dans le bouton 'synchroniser avec Clonable' pour les domaines qui utilisent www. Ajout d'un système de notification pour un meilleur aperçu des tâches en arrière-plan. Amélioration de la stabilité des crochets internes.

v2.1.8
Correction : Lecture correcte des données de l'utilisateur lorsque le type de contenu est multipart/form-data

v2.1.7
Correction : Correction du comportement de redirection pour les clones de sous-dossiers.

v2.1.6
Amélioration de la traduction des clones de sous-dossiers.

v2.1.5
Bugfixes et améliorations des performances.

v2.1.4
Correction : gestion différente des méthodes HTTP et correction de l'en-tête de longueur de contenu invalide.

v2.1.3
Correction : cas limite avec le fournisseur de paiement Mollie et les clones basés sur un domaine causant des redirections indirectes

v2.1.2
Meilleure prise en charge de la traçabilité des erreurs de configuration des sous-dossiers.
Correction d'une erreur de formatage.

v2.1.1
Bug fix

v2.1.0
Plusieurs corrections de bugs
Amélioration du support de WooCommerce :
- Ajout d'exclusions de produits pour les produits WooCommerce.
- Amélioration du suivi des conversions pour les clones de sous-dossiers.
- Clarification des modules Analytics/WooCommerce existants.

v2.0.7
Correction d'un bug dans les écrans de paramétrage des balises de langue en combinaison avec certains plugins de performance.

v2.0.6
Correction d'un bug dans l'installation
Mise à jour vers le niveau de compatibilité WordPress 6.4

v2.0.5
Amélioration des performances.

v2.0.4
Correction d'un bug pour l'entrée du site original, où l'entrée n'était pas correctement nettoyée.

v2.0.3
Correction d'un bug où certains noms de fonctions pouvaient entrer en conflit avec d'autres plugins.

v2.0.2
Correction de plusieurs bogues dans les traductions des balises de langue.

v2.0.1
Correction d'un bug où les erreurs de clés api n'étaient pas gérées correctement lors de la connexion au site.
Ajout d'un bouton pour déconnecter le plugin Clonable.

v2.0.0
Nouvelle version majeure du plugin.
- Ajout des paramètres du sélecteur de langue.
- Ajout d'une connexion au panneau de contrôle.
- Amélioration de l'utilisation des paramètres de la balise de langue.
- Ajout d'un processus d'enregistrement pour les nouveaux utilisateurs.
- Ajout de la configuration automatique des sous-dossiers pour les clones de sous-dossiers.

v1.3.0
Ajout d'une option pour désactiver la traduction d'url pour les balises de langue.

v1.2.4 :
Correction d'un bug où un champ de domaine vide était considéré comme une entrée invalide.

v1.2.3 :
Correction du fait que les visiteurs ne retournaient pas à la page du panier lorsque le paiement était annulé lors de l'utilisation de Mollie.

v1.2.2 :
Correction d'un crash sur certaines installations en essayant de détecter si woocommerce est installé ou non.

v1.2.1 :
Ajout de la compatibilité avec la passerelle de paiement Mollie.

v1.2.0 :
Ajout d'une intégration avec Woocommerce pour faciliter le suivi des conversions sur les sites clonés.

v1.1.2 :
Amélioration du taux de réussite du cache et modification de l'algorithme de backoff.

v1.1.1 :
Correction d'un crash lors de la sauvegarde des paramètres

v1.1.0 :
Utilisation des versions traduites des url dans les balises de langue

v1.0.2 :
Compatibilité avec Wordpress 6.0
Correction de balises de langue incorrectes dans certains cas

v1.0.1 :
Compatibilité avec Wordpress < 5.9

v1.0.0 :
Version initiale