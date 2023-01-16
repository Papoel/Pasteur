# Pasteur

[![Symfony 6 - Pipeline CI-CD](https://github.com/Papoel/Pasteur/actions/workflows/code-quality.yml/badge.svg?event=push)](https://github.com/Papoel/Pasteur/actions/workflows/code-quality.yml)
[![wakatime](https://wakatime.com/badge/user/b00c68bd-cbed-409e-a2c0-719a6f4ea11a/project/f2d13658-b577-42e4-92ad-be17b34bbe17.svg)](https://wakatime.com/badge/user/b00c68bd-cbed-409e-a2c0-719a6f4ea11a/project/f2d13658-b577-42e4-92ad-be17b34bbe17)
> __Pasteur est un projet permettant à une association de parent d'élèves de créer des évènements et de les gérer.__

## Description des fonctionnalités

#### En tant qu'utilisateur je peux:

- Inscrire un ou plusieurs enfants à une activité
- Payer en ligne via un client Stripe
- Annuler une inscription
- Envoyer un message à l'APE

#### En tant que Président je peux:

- Créer des membres actifs qui pourront se connecter à l'application (détail plus bas)
- Consulter et répondre aux emails envoyés depuis l'application (page Contact)
- Consulter, Modifier, Créer, Supprimer un utilisateur
- Consulter, Modifier, Créer, Supprimer un événement
- Consulter, Modifier, Créer, Supprimer une inscription à un événement

#### En tant que __SECRÉTAIRE ou TRÉSORIER__

- Consulter, Modifier, Créer, Supprimer un utilisateur
- Consulter, Modifier, Créer, Supprimer un événement
- Consulter, Modifier, Créer, Supprimer une inscription à un événement

#### En tant que Membre actif (personne ayant un compte crée par le Président(e))

- Lorsqu'un événement est crée il peut être publié mais visible uniquement pour les membres de l'association.
- Certains événement demande une organisation complexe le Président à donc la possibilité lorsqu'il crée
  un événement de __demander de l'aide aux membres de l'APE__ et de choisir des creneaux d'aide.
- Un Membre pourra donc s'enregistrer à un événement, choisir une activité parmi (Installation, Vente et Rangement)
  puis sélectionner un ou plusieurs créneaux horaires définis par le Président de l'association.

#### Autre ROLE

- Un autre rôle est présent dans l'application: __WEBMASTER__, ce compte est destiné au développeur
  des fonctionnalités de gestion et consultations seront développéS ultérieurement.

#### Détails Utilisateur

- A la demande du client, un visiteur ne pourra jamais s'inscrire sur le site, en revanche lorsqu'un visiteur
  inscrit un ou plusieurs enfant à un événenment son nom, prénom, email et téléphone luis sont demandés
  dans le but d'obtenir une fiche contact dans le cas ou l'APE devrait contacter un responsable légal.

👉 L'admin donne la possibilité de s'enregistrer ou non à un événement.

## Exécuter en Local

Clone le projet

```bash
  git clone https://github.com/Papoel/Pasteur.git
```

Aller dans le repertoire du projet

```bash
  cd Pasteur
```

### Première installation (Utilisateur Linux / Mac)

Vérifier si Docker est lancé

**! Attention cette commande n'a été testée et validée que sur un Mac !**

```bash
  if curl -s --unix-socket /var/run/docker.sock http/_ping 2>&1 >/dev/null
  then
    echo "Docker Desktop est en cours d'exécution"
  else
    echo "Docker Desktop est coupé ... Démarrage ..."
    open /Applications/Docker.app
  fi
```

### Installer toutes les dépendances

```bash
  make first-install
```

### Démarrer le projet après la première installation

```bash
  make start
```

### Couper les containers Docker et fermer le server Symfony

```bash
  make stop
```

### Première installation - Utilisateur Windows [(doc ici)](https://stackoverflow.com/questions/32127524/how-to-install-and-use-make-in-windows)

`make` est une commande GNU, donc la seule façon de l'obtenir sous Windows est d'installer une version de Windows
comme celle fournie par GNUWin32.
Quoi qu'il en soit, il existe plusieurs options pour l'obtenir.

Le choix le plus simple consiste à utiliser `Chocolatey`. <br/>
[Cliquez ici](https://lecrabeinfo.net/chocolatey-gestionnaire-paquets-windows.html) pour installer `Chocolatey` :
Vous devez d'abord installer ce gestionnaire de paquets.
Une fois installé, vous devez simplement installer `make` :

```bash
  choco install make
```

Une fois `make` installé, vous pouvez l'utiliser comme vous le feriez sur Linux ou Mac, je vous invite donc
à suivre la procédure d'installation ci-dessus.

## Se connecter à l'application

| Identifiant          | Mot de passe  | Rôle      |
|----------------------|---------------|-----------|
| bruce.wayne@admin.fr | Password1234! | PRESIDENT |

Le Président est le seul à pouvoir avoir accès aux messages et pouvoir y répondre.

Reste des privilèges à définir...

## Contributions

Les contributions sont toujours les bienvenues !

## Tech Stack

**Client:** Twig, Tailwind CSS, Stripe

**Server:** PHP 8.2, Symfony 6.2, Node.js, Docker, Makefile, Recaptcha v3
