
# Application technicien Tactéo

## Description de l'application

Cette application permet aux techniciens de faire leur rapport d'interventions via l'application. Cette nouvelle application permet de faire une mise à jour esthétique de celle-ci. Les informations de l'interventions sont ensuite envoyé à la base de donnée de Tactéo et aux personnes concernées.

L'application utilise Flutter en front et PHP pour le back.

## Installation

### Étape 1 : Installer Flutter

Il faut aller sur le site de flutter et installé le flutter SDK: https://docs.flutter.dev/get-started/install.

Pour Windows : Extrayez l'archive .zip téléchargée dans l'emplacement souhaité pour le SDK Flutter (par exemple, C:\src\flutter).

Pour Windows :
Ajoutez le chemin complet de flutter\bin à la variable d'environnement PATH.

Ouvrez un terminal et exécutez la commande suivante pour vérifier si Flutter est correctement installé : **flutter doctor**

### Étape 2 : Installer les outils de développement

#### Installez Android Studio :

Téléchargez et installez [Android studio](https://developer.android.com/studio?hl=fr)

Ouvrez Android Studio et suivez les instructions de configuration initiale.

Installez les plugins Flutter et Dart depuis les paramètres de Android Studio :
Allez dans **File > Settings > Plugins**.

Recherchez **Flutter** et cliquez sur **Install**.

Acceptez l'installation du plugin **Dart** lorsque demandé.

#### Configurer l'émulateur Android :

Dans Android Studio, ouvrez l'AVD Manager via **Tools > AVD Manager**.

Créez un nouvel appareil virtuel et suivez les instructions pour configurer un émulateur Android.

### Étape 3 : Installer VS Code (optionnel)

#### Téléchargez et installez Visual Studio Code :

Allez sur le site officiel de [Visual Studio Code](https://code.visualstudio.com) et téléchargez l'éditeur pour votre système d'exploitation.

#### Installez les extensions Flutter et Dart :

Ouvrez VS Code.

Allez dans **View > Extensions**.

Recherchez **Flutter** et installez l'extension officielle. L'extension Dart sera installée automatiquement.

### Vérifier l'installation

Pour vérifier que tout est bien installé il faut faire la commande ```flutter doctor```

## Structure du Projet
La structure du projet se divise en plusieurs parties :

- **Dossier `lib`** : Contient toutes les pages de l'application.
- **Dossier `assets`** : Contient les images de l'application.
- **Dossier `auth`** : Regroupe les fichiers qui gèrent la connexion et l'inscription des utilisateurs.
- **Dossier `form_steps`** : Contient tous les fichiers qui définissent les différentes étapes du formulaire.

### Pages Principales de l'Application

- **`login_page.dart`** : Page de connexion des utilisateurs.
- **`register_page.dart`** : Page d'inscription des utilisateurs.
- **`home_page.dart`** : Page d'accueil après la connexion.
- **`new_form.dart`** : Définit un formulaire à plusieurs étapes, avec la possibilité de sauvegarder et de restaurer l'état du formulaire en utilisant les préférences partagées.

### Gestion des Routes

- **`main.dart`** : Gère les routes de l'application. Ce fichier est le point d'entrée principal de l'application et définit les différentes routes vers les pages de l'application.

### Gestion de l'État du Formulaire

- **`form_state_storage.dart`** : Contient la classe `MyFormState` qui gère l'état d'un formulaire complexe, en utilisant les préférences partagées pour sauvegarder et restaurer l'état du formulaire.

### Détails sur les Étapes du Formulaire

- **`form_steps`** : Ce dossier contient tous les fichiers qui définissent les différentes étapes du formulaire. Chaque étape du formulaire est gérée dans un fichier séparé pour une meilleure organisation et une maintenance facilitée.

## Fonctionnement de Chaque Partie du Code

### `login_page.dart`
Cette page permet aux utilisateurs de se connecter à l'application. Elle inclut des champs de saisie pour le nom d'utilisateur et le mot de passe, et un bouton pour soumettre les informations de connexion.

### `register_page.dart`
Cette page permet aux nouveaux utilisateurs de s'inscrire à l'application. Elle inclut des champs de saisie pour les informations nécessaires à l'inscription (nom, email, mot de passe, etc.) et un bouton pour soumettre les informations d'inscription.

### `home_page.dart`
Cette page sert de tableau de bord principal pour les utilisateurs après leur connexion. Elle affiche les informations pertinentes et les options de navigation vers les autres parties de l'application.

### `new_form.dart`
Cette page définit un formulaire à plusieurs étapes. Elle utilise les préférences partagées pour sauvegarder et restaurer l'état du formulaire, permettant aux utilisateurs de reprendre là où ils se sont arrêtés.

### `form_state_storage.dart`
Ce fichier contient la classe `MyFormState`, qui gère l'état d'un formulaire complexe. Il utilise les préférences partagées pour stocker temporairement les informations saisies par l'utilisateur, facilitant ainsi la sauvegarde et la restauration de l'état du formulaire.

### `main.dart`
Ce fichier gère les routes de l'application. Il définit les chemins vers les différentes pages (`login_page.dart`, `register_page.dart`, `home_page.dart`, `new_form.dart`) et s'assure que l'utilisateur est redirigé vers la bonne page en fonction de l'état de l'application.

## Conclusion
Ce projet Flutter est structuré de manière à séparer les différentes fonctionnalités en dossiers et fichiers spécifiques, ce qui facilite la gestion et la maintenance du code. Chaque partie de l'application a une responsabilité bien définie, ce qui permet une meilleure organisation et une évolutivité accrue.







