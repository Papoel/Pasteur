# Pasteur

[![Symfony 6 - Pipeline CI-CD](https://github.com/Papoel/Pasteur/actions/workflows/code-quality.yml/badge.svg?event=push)](https://github.com/Papoel/Pasteur/actions/workflows/code-quality.yml)

Pasteur est un projet permettant à une association de parent d'élèves de créer des évènements, et de permettre à des bénévoles de s'inscrire à ces derniers afin qu'ils apportent leur aide.

👉 L'admin donne la possibilité de s'enregistrer ou non à un événement.

## 🚀 Qui suis-je ?


Développeur junior certifié depuis 2022.

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

### [Première installation (Utilisateur Windows)](https://stackoverflow.com/questions/32127524/how-to-install-and-use-make-in-windows)



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

Le Président est le seul à pouvoir avoir accès aux messages.

Reste des privilèges à définir...

## Contributions

Les contributions sont toujours les bienvenues !

## Tech Stack

**Client:** Twig, Tailwind CSS

**Server:** PHP 8.1, Symfony 6.1, Node.js, Docker, Makefile
