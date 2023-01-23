
# Application Pasteur

Ce document à pour but de tracer les retours sur les fonctionnalités, ainsi que de mettre au premier
plan les axes d'amélioration de l'application?

## But et description de l'application

Avant de commencer quoi que ce soit il est important de décrire l'application et quel problèmes vient-elles
solutionner.

### But de Pasteur

#### Détails

Pasteur, est en réalité de le nom de l'école élémentaire qui a fait la demande de cette application.
L'Association des Parents d'Élèves de Rousies Pasteur se retrouve sous l'abréviation APERP un peu partout sur 
l'application.

#### Pourquoi ?

Tout à commencer avec un problème classique, un événement à organiser et des difficultés de gestion en raison des 
emplois du temps de chacun des bénévoles.
La présidente de l'APE m'a contacté pour me demander la création d'un site permettant aux membres de l'association
de choisir un événement et de s'inscrire une ou plusieurs activité avec des créneaux horaires définis.

#### Et après ?

Après avoir mis en place l'application permettant de répondre à ce besoin des demandes supplémentaires m'ont été faite:
- Donner la possibilité aux gens de voir les événements organisés.
- Qu'une personne (représentant légal) puisse inscrire un ou plusieurs enfants a un événement.
- Donner aux gens la possibilité de payer directement sur le site ou sur place.
- Pas d'inscriptions sur le site, seules les membres de l'APE ont un compte ce qui leur permet d'accéder au backoffice.
- Une page de contact permettant aux visiteurs d'envoyer un email à l'APE.
- Les messages reçues ne sont visible et répondable que par le Président de l'APE.

#### Bilan

Au final plus j'avancé sur le projet plus la demande se complexifié et plus le site devenait complexe.
Je pense avoir répondu à toutes les demandes mais j'ai des améliorations à apporter que j'ai déjà identifier, ce
qui explique l'objet de cette review, cette introduction est un peu longue mais nécessaire pour obtenir tous le contexte.

# Road Review

### Ce que j'ai identifié

#### Pour les utilisateurs du site

- Il faut créer un service pour:
    - S'inscrire en tant que participant à un événement
    - Annuler son inscription

#### Pour les membres de l'APE

- La logique métier est mal gérer dans EasyAdmin => A revoir
- Parfois j'ai un bug sans message d'erreur (en rapport avec les dates lorsque je créer un event)

#### Globalement

- Amélioration du responsive
- Créer des components réutilisable avec Tailwind (card, button etc...)

## Séance de Review

> 22/01/2023 avec Honoré.

Le Paiement et sa logique métier

Mettre creneau en français => rename en SLOT
Recreer APP Extension avec la nouvelle façon de faire (runtime)

Si paiement ok 

### QUESTIONS

Comment appliquer une logique déjà en place dans le back-office ? 





## Authors

- [@Pascal Briffard](https://github.com/Papoel)

### 🔗 Les contributeurs

- [@Aurelien Bichop](https://github.com/AurelBichop)
- [@Honoré Hounwanou](https://github.com/mercuryseries)
