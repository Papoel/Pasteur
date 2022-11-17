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
  - [ ] Fixtures Event 

- BDD help
- 
- [x] Créer un compte utilisateur
- [x] Formulaire de connexion
- [ ] Administration

Evenements :
- [ ] Créer un événement
- [ ] s'inscrire à un événement ✅
- [ ] se désinscrire d'un événement
- [ ] ajouter les activités à un événement
- [ ] ajouter la sélection d'activités à un événement
