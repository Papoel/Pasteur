
Mettre en place une application web qui permet l'aide à l'organisation un événement.

## Attentes
- Nom de l'événement ✅
- Date de l'événement ✅
- Nom et prénom de la personne qui propose son aide ✅
- Poste proposé ✅
- Plage horaire proposée ✅

## Fonctionnalités en attente de validation par l'équipe APE
- [ ] Ajouter une fonctionnalité de commentaire sur les événements proposés

## Fonctionnalités courantes
Evenements :
- [x] s'inscrire à un événement ✅
- [x] ajouter les activités à un événement
- [x] ajouter la sélection d'activités à un événement
- [x] Créer un événement
- [x] se désinscrire d'un événement


Lors de mon inscription à l'événement :
- [x] je ne peux pas m'inscrire à un événement qui est déjà complet.
- [x] je ne peux pas m'inscrire à un événement qui est déjà passé.
- [x] je ne peux pas m'inscrire à un événement pur lequel je suis déjà inscrit.
- [x] je ne peux pas m'inscrire à un événement avec une activité pur laquelle je suis déjà inscrit.

Lors de mon inscription les données sont enregistrées dans la BDD :
- [x] Nom + Prénom => BDD Registration
- [x] Email => BDD Registration
- [x] Téléphone => BDD Registration
- [x] Messages => BDD Registration
- [x] Activités => BDD Registration
- [x] Plages Horaire ⇒ BDD Events_Plages_Horaires

## Fonctionnalités Président
- [X] Ajouter une option consulter les messages
- [X] Ajouter une option pour répondre aux messages.

## Modification à apporter :

- [x] Modifier le type de price → float to integer et donner les prix en centimes
- [X] Rendre conditionnel l'affichage des cartes dans le footer (sinon erreur si pas de rôle correspondant)
- [X] Pas de caractères spéciaux pour le mot de passe
- [x] Ajouter un bouton publié pour gérer l'affichage des événements
- [x] Template si aucun event n'est publié
- [x] Renommer registration en registrationHelp et faire toutes les modifs
- [x] Inscription aux événements
- [x] Changer la redirection après l'envoie d'un message homepage au lieu de /contact

# Fonctionnalité à développer
- [x] Titre et design des pages Admin (Back Office)
- [x] Gestion de la capacité maximale d'inscription à un événement
- [x] Création d'une table Payment pour gérer les paiements 
- [x] Sauvegarder les données de la session Stripe dans la table Payment
- [ ] Afficher les événements au status ('PREPARATION') => Visible uniquement par les membres APE
- [ ] La carte devra être différente pour les événements au status ('PREPARATION')
- [ ] Création des pages d'erreur 404 et 500
- [ ] Logique métier Cancel Payment ?

### ADMIN
- [x] Définir les status possibles à : ['PREPARATION', 'RUNNING', 'FINISHED']
- [x] Afficher pour l'admin la liste des inscrits à chaque event
- [x] Télécharger en PDF la liste des inscrits à chaque event

### USER
- [x] Annuler un événement (avant de payer → désinscription automatique des participants)
- [x] Paiement en ligne des événements payant
- [ ] Mettre une option pour payer directement sur place ou à l'école
- [ ] Afficher la liste des événements souscrits
- [ ] Mot de passe oublié, envoie email à Admin pour changement depuis le backoffice
> __Priorité__ : Afficher la liste des événements souscrits

# Fonctionnalité abandonnée par le client
- [ ] Page du profil utilisateur

# <h1 style="color:red;">REFACTORISATION A FAIRE</h1>

## Création d'un service pour gérer les événements

- [ ] __Méthode d'inscription à un événement doit :__
  - Enregistrer dans EventRegistration l'inscription
  - Ajouter la liste des enfants dans la table Children
  - Déduire le nombre de places disponible dans l'événement (Capacity - count(Children))
  - Ajouter count(Children) dans la table registered dans Event
- [ ] __Méthode d'annulation à un événement doit :__
  - Supprimer dans EventRegistration l'inscription
  - Supprimer la liste des enfants dans la table Children
  - Ajouter le nombre de places disponible dans l'événement (Capacity + count(Children))
  - Supprimer count(Children) dans la table registered dans Event

## Création d'un service pour gérer les paiements
- [ ] __Méthode de paiement doit :__
  - Enregistrer dans Payment le paiement
  - Envoyer un email de confirmation de paiement
  - .... Vérifier les action dans "checkout_session"
