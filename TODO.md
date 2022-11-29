# Challenge 24h pour réaliser l'application

# Objectifs
Mettre en place une application web qui permet l'aide à l'organisation un événement.

## Attentes
- Nom de l'événement ✅
- Date de l'événement ✅
- Nom et prénom de la personne qui propose son aide ✅
- Poste proposé
- Plage horaire proposée
- Plage des postes
- Commentaires (message lors de la soumissions des postes ( ✅ ) ou vrai commentaire ?)

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

Lors de mon inscription à l'événement:
- [ ] je ne peux pas m'inscrire à un événement qui est déjà complet.
- [x] je ne peux pas m'inscrire à un événement qui est déjà passé.
- [x] je ne peux pas m'inscrire à un événement pur lequel je suis déjà inscrit.
- [x] je ne peux pas m'inscrire à un événement avec une activité pur laquelle je suis déjà inscrit.

Lors de mon inscription les données sont enregistrées dans la BDD:
- [x] Nom + Prénom => BDD Registration
- [x] Email => BDD Registration
- [x] Téléphone => BDD Registration
- [x] Messages => BDD Registration
- [x] Activités => BDD Registration
- [x] Plages Horaire => BDD Events_Plages_Horaires
