![ImpactCO2 Logo](./docs/images/impactco2_logo.webp)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jokod/impactco2-php.svg?style=flat-square)](https://packagist.org/packages/jokod/impactco2-php) [![Total Downloads](https://img.shields.io/packagist/dt/jokod/impactco2-php.svg?style=flat-square)](https://packagist.org/packages/jokod/impactco2-php) [![License](https://img.shields.io/packagist/l/jokod/impactco2-php.svg?style=flat-square)](https://packagist.org/packages/jokod/impactco2-php) ![GitHub release](https://img.shields.io/github/v/release/jokod/impactco2-php?style=flat-square)

# 🌍 ImpactCO2 PHP Client

Une librairie PHP simple et robuste pour interagir avec l'API ImpactCO2 de l'ADEME. Calculez facilement l'empreinte carbone de vos activités (transport, chauffage, alimentation, etc.) directement depuis votre application PHP.

Retrouvez le projet ainsi que la documentation officielle de l'API ImpactCO2 sur [impactco2.fr](https://impactco2.fr/).

## 📋 Table des matières

- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Démarrage rapide](#-démarrage-rapide)
- [Utilisation détaillée](#-utilisation-détaillée)
  - [Configuration du client](#configuration-du-client)
  - [Transport](#1-transport)
  - [Chauffage](#2-chauffage)
  - [Fruits et légumes](#3-fruits-et-légumes)
  - [Thématiques](#4-thématiques)
  - [Détail d'une thématique](#5-détail-dune-thématique)
  - [Alimentation](#6-alimentation)
- [Gestion des erreurs](#-gestion-des-erreurs)
- [Tests](#-tests)
- [Contribuer](#-contribuer)
- [Licence](#-licence)

## 🔧 Prérequis

- **PHP 8.3** ou supérieur
- Extension **JSON** activée
- **Composer** pour la gestion des dépendances

## 📦 Installation

Installez la librairie via Composer :

```bash
composer require jokod/impactco2-php
```

## 🚀 Démarrage rapide

Voici un exemple simple pour calculer les émissions CO₂e d'un trajet en voiture :

```php
<?php

require 'vendor/autoload.php';

use Jokod\Impactco2Php\Client;
use Jokod\Impactco2Php\Endpoints\TransportEndpoint;
use Jokod\Impactco2Php\Enum\TransportsEnum;

// Créer le client
$client = new Client();

// Calculer les émissions pour un trajet de 100 km en voiture
$endpoint = new TransportEndpoint(100, [TransportsEnum::CAR]);
$result = $client->execute($endpoint);

print_r($result);
```

## 📚 Utilisation détaillée

### Configuration du client

Le client peut être configuré avec plusieurs options :

```php
use Jokod\Impactco2Php\Client;
use Jokod\Impactco2Php\Enum\LanguagesEnum;

$client = new Client([
    'api_key'  => 'votre_cle_api',     // Optionnel - Clé API si nécessaire
    'language' => LanguagesEnum::FR     // Optionnel - Langue (FR, EN, ES, DE)
]);
```

**Options disponibles :**

| Option | Type | Défaut | Description |
|--------|------|--------|-------------|
| `api_key` | `string\|null` | `null` | Clé API (si requis par l'API) |
| `language` | `string` | `'fr'` | Langue des résultats (fr, en, es, de) |
| `logger` | `LoggerInterface\|null` | Logger par défaut | Logger personnalisé PSR-3 |

### 1. Transport

Calculez les émissions de CO₂e pour différents moyens de transport sur une distance donnée.

#### Exemple basique

```php
use Jokod\Impactco2Php\Endpoints\TransportEndpoint;
use Jokod\Impactco2Php\Enum\TransportsEnum;

// Comparer plusieurs transports pour 50 km
$endpoint = new TransportEndpoint(
    distance: 50,
    transports: [
        TransportsEnum::CAR,
        TransportsEnum::ELECTRIC_CAR,
        TransportsEnum::TGV,
        TransportsEnum::PLANE
    ]
);

$result = $client->execute($endpoint);
```

#### Exemple avancé avec toutes les options

```php
$endpoint = new TransportEndpoint(
    distance: 100,                          // Distance en km
    transports: [TransportsEnum::CAR],      // Liste des transports (optionnel)
    displayAll: false,                      // Afficher tous les transports pertinents
    occupencyRate: 2,                       // Taux d'occupation du véhicule
    includeConstruction: 1,                 // Inclure les émissions de construction (0 ou 1)
    ignoreRadiativeForcing: 0               // Ignorer le forçage radiatif avion (0 ou 1)
);
```

#### Transports disponibles

```php
TransportsEnum::PLANE                    // Avion
TransportsEnum::TGV                      // TGV
TransportsEnum::INTERCITY                // Intercités
TransportsEnum::CAR                      // Voiture thermique
TransportsEnum::ELECTRIC_CAR             // Voiture électrique
TransportsEnum::BUS                      // Bus
TransportsEnum::THERMAL_BUS              // Bus thermique
TransportsEnum::ELECTRIC_BUS             // Bus électrique
TransportsEnum::GNV_BUS                  // Bus GNV
TransportsEnum::TRAMWAY                  // Tramway
TransportsEnum::METRO                    // Métro
TransportsEnum::RER_TRANSILIEN           // RER/Transilien
TransportsEnum::TER                      // TER
TransportsEnum::WALKING                  // Marche à pied
TransportsEnum::BIKE                    // Vélo
TransportsEnum::SCOOTER                  // Scooter thermique
TransportsEnum::ELECTRIC_SCOOTER         // Scooter électrique
TransportsEnum::MOTORCYCLE               // Moto
TransportsEnum::ELECTRIC_BIKE            // Vélo électrique
TransportsEnum::CARPOOLING_1             // Covoiturage 1 personne
TransportsEnum::CARPOOLING_2             // Covoiturage 2 personnes
TransportsEnum::CARPOOLING_3             // Covoiturage 3 personnes
TransportsEnum::CARPOOLING_4             // Covoiturage 4 personnes
TransportsEnum::ELECTRIC_CARPOOLING_1    // Covoiturage électrique 1 personne
TransportsEnum::ELECTRIC_CARPOOLING_2    // Covoiturage électrique 2 personnes
TransportsEnum::ELECTRIC_CARPOOLING_3    // Covoiturage électrique 3 personnes
TransportsEnum::ELECTRIC_CARPOOLING_4    // Covoiturage électrique 4 personnes
```

### 2. Chauffage

Calculez les émissions de CO₂e pour le chauffage d'une surface donnée.

```php
use Jokod\Impactco2Php\Endpoints\HeaterEndpoint;
use Jokod\Impactco2Php\Enum\HeaterEnum;

// Comparer différents types de chauffage pour 80 m²
$endpoint = new HeaterEndpoint(
    surface: 80,                            // Surface en m² (optionnel, défaut: 63)
    types: [
        HeaterEnum::GAS_HEATING,
        HeaterEnum::ELECTRIC_HEATING,
        HeaterEnum::HEAT_PUMP_HEATING
    ]
);

$result = $client->execute($endpoint);
```

#### Types de chauffage disponibles

```php
HeaterEnum::GAS_HEATING              // Chauffage au gaz
HeaterEnum::FUEL_OIL_HEATING         // Chauffage au fioul
HeaterEnum::ELECTRIC_HEATING         // Chauffage électrique
HeaterEnum::HEAT_PUMP_HEATING        // Pompe à chaleur
HeaterEnum::PELLET_STOVE_HEATING     // Poêle à granulés
HeaterEnum::WOOD_STOVE_HEATING       // Poêle à bois
HeaterEnum::DISTRICT_HEATING         // Réseau de chaleur
```

### 3. Fruits et légumes

Obtenez les émissions des fruits et légumes de saison.

```php
use Jokod\Impactco2Php\Endpoints\FruitsVegetables;
use Jokod\Impactco2Php\Enum\FoodEnum;

// Fruits et légumes du mois de juin
$endpoint = new FruitsVegetables(
    month: 6,                               // Mois (1-12, optionnel, défaut: mois courant)
    categories: [
        FoodEnum::FRUITS,
        FoodEnum::VEGETABLES
    ]
);

$result = $client->execute($endpoint);
```

#### Catégories disponibles

```php
FoodEnum::FRUITS                     // Fruits
FoodEnum::VEGETABLES                 // Légumes
FoodEnum::HERBS                      // Herbes aromatiques
FoodEnum::PASTA_RICE_CEREALS         // Pâtes, riz et céréales
FoodEnum::POTATOES_TUBERS            // Pommes de terre et tubercules
FoodEnum::NUTS_SEEDS                 // Fruits à coque et graines
```

### 4. Thématiques

Listez toutes les thématiques disponibles dans l'API.

```php
use Jokod\Impactco2Php\Endpoints\ThematicsEndpoint;

$endpoint = new ThematicsEndpoint();
$thematics = $client->execute($endpoint);

// Parcourir les thématiques
foreach ($thematics as $thematic) {
    echo $thematic['name'] . PHP_EOL;
}
```

### 5. Détail d'une thématique

Obtenez les émissions détaillées pour une thématique spécifique (ECV - Empreinte Carbone sur le cycle de Vie).

```php
use Jokod\Impactco2Php\Endpoints\ThematicsEcvEndpoint;
use Jokod\Impactco2Php\Enum\ThematicEnum;

// Obtenir le détail de la thématique "meubles"
$endpoint = new ThematicsEcvEndpoint(
    id: ThematicEnum::FURNITURE,
    detail: 1                               // 0 = total uniquement, 1 = détail complet
);

$result = $client->execute($endpoint);
```

#### Thématiques disponibles

```php
ThematicEnum::NUMERIC                    // Numérique
ThematicEnum::MEAL                       // Repas
ThematicEnum::DRINK                      // Boissons
ThematicEnum::TRANSPORT                  // Transport
ThematicEnum::CLOTHING                   // Habillement
ThematicEnum::APPLIANCE                  // Électroménager
ThematicEnum::FURNITURE                  // Meubles
ThematicEnum::HEATING                    // Chauffage
ThematicEnum::FRUITS_AND_VEGETABLES      // Fruits et légumes
ThematicEnum::DIGITAL_USAGE              // Usages numériques
ThematicEnum::CASE_STUDIES               // Études de cas
```

### 6. Alimentation

Obtenez les émissions par kg d'aliments, classées par catégorie.

```php
use Jokod\Impactco2Php\Endpoints\AlimentationEndpoint;
use Jokod\Impactco2Php\Enum\AlimentationCategoryEnum;

// Par groupes d'aliments (viandes, poissons, produits laitiers...)
$endpoint = new AlimentationEndpoint(AlimentationCategoryEnum::GROUP);
$result = $client->execute($endpoint);

// Par rayons du magasin (boucherie, rayon frais...)
$endpoint = new AlimentationEndpoint(AlimentationCategoryEnum::RAYON);
$result = $client->execute($endpoint);

// Les 10 aliments les plus consommés en France
$endpoint = new AlimentationEndpoint(AlimentationCategoryEnum::POPULARITY);
$result = $client->execute($endpoint);
```

#### Catégories disponibles

```php
AlimentationCategoryEnum::GROUP          // Groupes d'aliments (viandes, poissons, etc.)
AlimentationCategoryEnum::RAYON          // Rayons du magasin
AlimentationCategoryEnum::POPULARITY     // Aliments les plus consommés
```

## ⚠️ Gestion des erreurs

La librairie utilise des exceptions pour gérer les erreurs :

```php
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use Jokod\Impactco2Php\Exceptions\Exception;

try {
    // Distance négative = exception
    $endpoint = new TransportEndpoint(-100);
} catch (InvalidArgumentException $e) {
    echo "Paramètre invalide : " . $e->getMessage();
}

try {
    $result = $client->execute($endpoint);
} catch (Exception $e) {
    echo "Erreur API : " . $e->getMessage();
}
```

**Types d'exceptions :**

- `InvalidArgumentException` : Paramètres invalides (distance négative, enum inconnu, etc.)
- `Exception` : Erreurs de communication avec l'API

## 🧪 Tests

Exécutez les tests unitaires avec PHPUnit :

```bash
# Installer les dépendances
composer install

# Lancer tous les tests
composer test
# ou
./vendor/bin/phpunit

# Avec couverture de code
composer test-coverage
```

La librairie dispose de **126 tests** couvrant tous les endpoints et cas d'usage.

## 🛠️ Contribuer

Les contributions sont les bienvenues ! Voici comment participer :

1. Forkez le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

### Normes de code

Le projet utilise :
- **PHP-CS-Fixer** pour le formatage du code
- **GrumPHP** pour les hooks git
- **PHPUnit** pour les tests

```bash
# Vérifier le formatage
make lint

# Corriger automatiquement
make fix

# Lancer les tests
make test
```

## 📄 Licence

Ce projet est sous licence [MIT](https://opensource.org/licenses/MIT).

## 🔗 Liens utiles

- [Documentation officielle ImpactCO2](https://impactco2.fr/)
- [API ImpactCO2](https://impactco2.fr/doc/api)
- [Package Packagist](https://packagist.org/packages/jokod/impactco2-php)
- [Repository GitHub](https://github.com/jokod/impactco2-php)

## 💬 Support

Si vous rencontrez un problème ou avez une question :

- Ouvrez une [issue sur GitHub](https://github.com/jokod/impactco2-php/issues)
- Consultez la [documentation de l'API](https://impactco2.fr/doc/api)

---

Développé avec 💚 pour contribuer à la transition écologique
