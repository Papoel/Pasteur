# Pasteur

Pasteur est un projet permettant à une association de parent d'élèves de créer des évènements, et de permettre à des bénévoles de s'inscrire à ces dernier afin qu'ils apportent leur aide.

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
### Première installation

Vérifier si Docker est lancé

**! Attention cette commande n'a été testée et validé que sur un Mac !**


```bash
  if curl -s --unix-socket /var/run/docker.sock http/_ping 2>&1 >/dev/null
  then
    echo "Docker Desktop est en cours d'éxécution"
  else
    echo "Docker Desktop est coupé ... Demarrage ..."
    open /Applications/Docker.app
  fi
```

Installer toutes les dépendances

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

## Contributions

Les contributions sont toujours les bienvenues !

## Tech Stack

**Client:** Twig, Tailwind CSS

**Server:** PHP 8.1, Symfony 6.1
