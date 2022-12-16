# Challenge 24 h pour réaliser l'application

# Objectifs
Mettre en place une application web qui permet l'aide à l'organisation un événement.

## Attentes
- Nom de l'événement ✅
- Date de l'événement ✅
- Nom et prénom de la personne qui propose son aide ✅
- Poste proposé
- Plage horaire proposée
- Plage des postes
- Commentaires (message lors de la soumission des postes (✅) ou vrai commentaire ?)

## Fonctionnalités
- [x] BDD User
  - [x] firstname
  - [x] lastname
  - [x] email
  - [x] password
  - [x] createdAt
- [x] Fixtures User

- [x] BDD Event
  - [x] name
    - string
  - [x] description
    - string
  - [x] location
    - string
  - [x] startsAt
    - DateTimeImmutable
  - [x] price
    - string
  - [x] status
    - integer
  - [x] capacity
    - integer
  - [x] helpNeeded
    - boolean
  - [x] createdAt
    - dateTimeImmutable
  - [x] updatedAt
    - dateTimeImmutable
  - [x] imageFileName
    - string
  - [x] Fixtures Event 

- BDD help
- 
- [x] Créer un compte utilisateur
- [x] Formulaire de connexion
- [ ] Administration

Evenements :
- [ ] Créer un événement
- [x] s'inscrire à un événement ✅
- [ ] se désinscrire d'un événement
- [x] ajouter les activités à un événement
- [x] ajouter la sélection d'activités à un événement

😱...

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

Ajouter dans Entity User:
- [x] Telephone
- [x] Adresse

## Fonctionnalités Admin
- [ ] Utiliser la logique de vérification lors de l'inscription a un event dans l'admin que dans le controller
  <div style="background-color: #B0413E; padding: 2px 6px; border-radius: 10px; margin-top: 10px;"> 
    <p style="color: #fff"> 
      <strong> 
        <i class="fas fa-exclamation-triangle"></i> 
        Attention 
      </strong> 
      : 
      Actuellement, un User peut s'inscrire plusieurs fois à un même event et la même activité.
    </p>
  </div>

- [X] Ajouter une option consulter les messages.
- [X] Ajouter une option pour répondre aux messages.

## Modifier le type de price => float to integer et donner les prix en centimes

Tests :
  - [X] Rendre conditionnel l'affichage des cartes dans le footer (sinon erreur si pas de rôle correspondant)
  - [X] Pas de caractères spécial pour le mot de passe
  - [x] Ajouter un bouton publié pour gérer l'affichage des événements
  - [x] Template si aucun event n'est publié
  - [] Inscription aux événements
  - [] ?? Paiement en ligne des événements payant
