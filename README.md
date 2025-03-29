# BorrowMyStuff

Application web de gestion de prêts d'objets entre utilisateurs.

## Fonctionnalités

- Inscription et connexion des utilisateurs
- Gestion des objets (ajouter, modifier, supprimer)
- Demandes de prêt avec dates de début et de fin
- Tableau de bord administrateur
- Tableau de bord utilisateur

## Prérequis

- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Composer
- Serveur web (Apache ou Nginx)

## Installation en local

1. Clonez le dépôt
```bash
git clone https://github.com/votre-utilisateur/borrowmystuff.git
cd borrowmystuff
```

2. Installez les dépendances via Composer
```bash
composer install
```

3. Créez un fichier .env à la racine en vous basant sur .env.example
```
DB_HOST=127.0.0.1
DB_NAME=borrowmystuff
DB_USER=votre_utilisateur
DB_PASS=votre_mot_de_passe
```

4. Créez la base de données et importez le schéma
```bash
mysql -u votre_utilisateur -p -e "CREATE DATABASE borrowmystuff"
mysql -u votre_utilisateur -p borrowmystuff < migrations.sql
```

5. Configurez les permissions des dossiers
```bash
chmod -R 755 .
chmod -R 777 public/img/items public/uploads
```

6. Lancez l'application via le serveur intégré de PHP ou configurez un serveur web
```bash
php -S localhost:8000
```

## Déploiement sur un serveur

### Hébergement partagé

1. Téléchargez tous les fichiers sur votre hébergement via FTP
2. Créez une base de données MySQL et importez le fichier migrations.sql
3. Configurez le fichier .env avec les informations de connexion à la base de données
4. Assurez-vous que le document root pointe vers le dossier racine du projet

### Hébergement gratuit (comme 000webhost)

1. Créez un compte sur la plateforme d'hébergement
2. Créez un nouveau site dans votre tableau de bord
3. Téléchargez votre code via le gestionnaire de fichiers web ou FTP
4. Créez une base de données MySQL dans le panneau de contrôle
5. Importez le fichier migrations.sql via phpMyAdmin
6. Modifiez le fichier .env pour correspondre aux paramètres de la base de données fournie

## Sécurité

Ce projet implémente plusieurs mesures de sécurité :
- Protection CSRF
- Validation des entrées utilisateur
- Téléchargement sécurisé des fichiers
- Limitation des tentatives de connexion
- Échappement des données affichées (XSS)
- Utilisation de requêtes préparées (protection contre les injections SQL)

## Maintenance

Pour mettre à jour l'application :
1. Faites une sauvegarde de la base de données
2. Tirez les dernières modifications depuis le dépôt Git
3. Mettez à jour les dépendances via Composer
4. Appliquez les migrations si nécessaire

## Licence

Ce projet est distribué sous licence MIT. 