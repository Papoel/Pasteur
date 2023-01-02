# Challenge 24 h pour réaliser l'application

# Objectifs
Mettre en place une application web qui permet l'aide à l'organisation un événement.

## Attentes
- Nom de l'événement ✅
- Date de l'événement ✅
- Nom et prénom de la personne qui propose son aide ✅
- Poste proposé ✅
- Plage horaire proposée ✅
- [?] Commentaires (Commentaires pouyr chaque event ? -> Team APE only ou Public ?)


## Fonctionnalités courantes

Evenements :
- [x] s'inscrire à un événement ✅
- [x] ajouter les activités à un événement
- [x] ajouter la sélection d'activités à un événement
- [ ] Créer un événement
- [ ] se désinscrire d'un événement


Lors de mon inscription à l'événement :
- [ ] je ne peux pas m'inscrire à un événement qui est déjà complet.
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

  - [x] Modifier le type de price => float to integer et donner les prix en centimes
  - [X] Rendre conditionnel l'affichage des cartes dans le footer (sinon erreur si pas de rôle correspondant)
  - [X] Pas de caractères spéciaux pour le mot de passe
  - [x] Ajouter un bouton publié pour gérer l'affichage des événements
  - [x] Template si aucun event n'est publié
  - [x] Renommer registration en registrationHelp et faire toutes les modifs

  - [x] Créer Entity Registration Event
    - [x] firstname
    - [x] lastname
    - [x] email
    - [x] quantity
    - [x] childrens -> Nouvelle Entity relation OneToMany
      - [x] firstname
      - [x] lastname
      - [x] classroom

  - [x] Inscription aux événements
  - [ ] Changer la redirection après l'envoie d'un message homepage au lieu de /contact

# Fonctionnalité à développer
  - [x] Titre et design des pages Admin (Back Office)
  - [-] Page du profil utilisateur -> Abandonné, le client ne souhaite pas cette fonctionnalité

# Fonctionnalité à traiter rapidement
  - [x] Gestion de la capacité maximale d'inscription à un événement
  - [ ] Afficher pour l'admin la liste des inscrits à chaque event
  - [x] Paiement en ligne des événements payant
  - [x] Annuler un événement (avant de payer => désinscription automatique des participants)
  - [x] Mettre une option pour payer directement sur place ou à l'école

# Bug rencontré et à corriger

- [ ] Utiliser la logique de vérification lors de l'inscription a un event dans l'admin que dans le controller
  <div style="background-color: #B0413E; padding: 2px 6px; border-radius: 10px; margin-top: 10px;"> 
    <p style="color: #fff"> 
      <strong> 
        <i class="fas fa-exclamation-triangle"></i> 
        1 
      </strong> 
      : 
      Actuellement, un User peut s'inscrire plusieurs fois à un même event et la même activité.
    </p>
  </div>
