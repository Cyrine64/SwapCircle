# ♻️ EcoSwap - Plateforme d'Échange et Recyclage

EcoSwap est une application complète de gestion d'échange d'objets et de recyclage développée avec **Symfony 6.4**. Elle facilite la circulation des objets entre utilisateurs et encourage le recyclage responsable.

## 📚 Table des Matières

- [✨ Fonctionnalités](#-fonctionnalités)  
- [🛠️ Installation](#-installation)  
- [🚀 Utilisation](#-utilisation)  
- [👥 Rôles Utilisateurs](#-rôles-utilisateurs)  
- [🧰 Technologies Utilisées](#-technologies-utilisées)  
- [🤝 Contribution](#-contribution)  
- [👥 Contributeurs](#-contributeurs)  
- [🏫 École](#-école)  
- [📄 Licence](#-licence)  

---

## ✨ Fonctionnalités

✅ Gestion complète des utilisateurs et profils  
✅ Système d'échange et de don d'objets entre membres  
✅ Plateforme de recyclage avec points de collecte  
✅ Blog éducatif sur l'écologie et le recyclage  
✅ Système de réclamations et modération  
✅ Recherche avancée d'objets par catégorie  
✅ Système de notation des utilisateurs  
✅ Géolocalisation des points d'échange/recyclage  
✅ ✨ Intégration de Gemini AI pour suggestions intelligentes  
✅ 📧 Notifications par email  
✅ 🔍 Filtres avancés pour trouver des objets  
✅ 🚫 Modération de contenu via l'API Bad Words 

---

## 🛠️ Installation

### Prérequis

- PHP >= 8.1  
- Composer  
- Symfony CLI (optionnel mais recommandé)  
- MySQL ou PostgreSQL  

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/Cyrine64/SwapCircle.git
cd SwapCircle-Symfony

# 2. Installer les dépendances PHP
composer install

# 3. Copier et configurer les variables d’environnement
cp .env.example .env
# Modifier .env avec votre configuration DB et vos clés API

# 4. Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Lancer le serveur
symfony server:start

```

---

## 🚀 Utilisation

Accédez à l’application sur: [http://localhost:8000](http://localhost:8000)

Vous pouvez vous inscrire et vous connecter en tant que :

- **Client**
- **Personnel**
- **Administrateur**

Explorez l’application selon votre rôle attribué.

---

## 👥 Rôles Utilisateurs

### 👤 Membre
- Publier et gérer des objets à échanger
- Rechercher et réserver des objets disponibles
- Donner des objets à recycler
- Laisser des avis et commentaires
- Accéder à l'historique des transactions

### 👨‍💼 Administrateur
- Gérer toutes les entités (objets, utilisateurs, points de recyclage, etc.)
- Modérer le contenu et superviser les échanges
- Configurer les catégories d'objets
- Générer des statistiques globales
- Gérer le système de récompenses écologiques
---

## 🧰 Technologies Utilisées

| Category        | Technology                                                       |
|----------------|-------------------------------------------------------------------|
| **Framework**   | Symfony 6.4                                                      |
| **Langages**   | PHP, JavaScript                                                  |
| **Frontend**    | Twig, Stimulus                                                   |
| **Base de Données**    | Doctrine ORM                                                     |
| **APIs**        | Gemini AI, Stripe, Google Calendar, Weather, Maps, Bad Words API |
| **Outils Divers** | QR/Barcode Generators, Email Notifications, Advanced Search      |

---

## 🤝 contribution

1. Forkez le dépôt  
2. Créez une branche pour votre fonctionnalité : 
   ```bash
   git checkout -b feature/my-feature
   ```
3. Validez vos modifications : 
   ```bash
   git commit -m "Add my feature"
   ```
4. Poussez vers votre branche :  
   ```bash
   git push origin feature/my-feature
   ```
5. Ouvrez une pull request ✅

---

---

## 👥 Contributeurs

| Nom              | Profile  Github                                            |
|-------------------|--------------------------------------------------------------|
| [oussema zemzem](https://github.com/oussemazemzem)         | 🔗 |
| [yassine saoud](https://github.com/yassinesaoud)       | 🔗 |
| [cyrine merie](https://github.com/cyrine64)       | 🔗 |
| [chaima fraj](https://github.com/chaimafraj)       | 🔗 |
| [hamza omar](https://github.com/OmarHamza044)         | 🔗 |
| **Coach:** [belkneni maroua](https://github.com/BenKhalifaGHADA) | 🔗 |

---

## 🏫 École

**ESPRIT - École Supérieure Privée d'Ingénierie et de Technologies**

![Logo ESPRIT](esprit.jpg)


## 📄 License

Ce projet est soumis à une licence propriétaire.
Veuillez contacter l’auteur pour plus d’informations.
