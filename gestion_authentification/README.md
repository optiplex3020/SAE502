# 🚀 Gestion de l'Authentification - Sprint 2

## 🎯 Objectifs
L'objectif de ce sprint était d'ajouter **l'inscription et l'authentification des utilisateurs externes via LDAP** et d'améliorer la gestion de l'authentification avec :
- ✅ **Enregistrement des utilisateurs externes dans LDAP**
- ✅ **Envoi d'un email de confirmation après inscription**
- ✅ **Connexion des utilisateurs externes via LDAP**
- ✅ **Déploiement et tests sous Docker**

---

## 🎯 Sprint 3 - Intégration Keycloak (SSO, JWT, Rôles)
### ✅ Objectifs :
- Intégration de Keycloak pour l’authentification des utilisateurs externes.
- Gestion des rôles (`admin`, `user`).
- Sécurisation des requêtes avec JWT (JSON Web Token).

### 🔧 **Configuration Keycloak**
1. Création du **Realm** : `WebApp`
2. Ajout du **Client** : `webapp-client` (OpenID Connect)
3. Définition des **Redirect URIs** :
   - `http://localhost:8080/*`
   - `http://localhost:8080/home.html`
4. Ajout des **Rôles et Utilisateurs** :
   - `test_user` → `user`
   - `admin_user` → `admin`

### 📌 **Améliorations**
- 📋 **Affichage des infos Keycloak après connexion**
- 🔒 **Gestion des rôles (admin / user)**
- 🔑 **Affichage et copie du Token JWT**

---
✅ **Sprint 3 terminé avec succès !** 🚀🔥

## 🛠 **Technologies utilisées**
- **Frontend :** HTML, CSS, JavaScript
- **Backend :** PHP (Apache)
- **Base de données interne :** MySQL (pour les utilisateurs internes)
- **Annuaire externe :** OpenLDAP (pour les utilisateurs externes)
- **Docker :** Pour l'hébergement et la gestion des services

---

## 📌 **Installation & Exécution**
### **1️⃣ Cloner le projet**
```bash
git clone https://github.com/optiplex3020/SAE502.git
cd gestion_authentification