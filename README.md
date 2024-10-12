![ImpactCO2 Logo](./docs/images/impactco2_logo.webp)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jokod/impactco2-php.svg?style=flat-square)](https://packagist.org/packages/jokod/impactco2-php) [![Total Downloads](https://img.shields.io/packagist/dt/jokod/impactco2-php.svg?style=flat-square)](https://packagist.org/packages/jokod/impactco2-php) [![License](https://img.shields.io/packagist/l/jokod/impactco2-php.svg?style=flat-square)](https://packagist.org/packages/jokod/impactco2-php) ![GitHub release](https://img.shields.io/github/v/release/jokod/impactco2-php?style=flat-square)

<!-- ![Coverage Status](https://coveralls.io/repos/github/jokod/impactco2-php/badge.svg?branch=main) [![Build Status](https://travis-ci.com/jokod/impactco2-php.svg?branch=main)](https://travis-ci.com/jokod/impactco2-php) -->

Une librairie PHP permettant de comparer la consommation en CO₂e de divers équivalents.

Retrouvez le projet ainsi que la documentation officielle de l'API ImpactCO2 sur [impactco2.fr](https://impactco2.fr/).

## Installation

### Prérequis

- PHP 8.3 ou supérieur

### Composer

Vous pouvez installer cette librairie via Composer. Exécutez la commande suivante :

```bash
composer require jokod/impactco2-php
```

## Utilisation

Retrouvez l'ensemble des endpoints disponibles sur la documentation officielle de l'API ImpactCO2 : [Documentation API](https://impactco2.fr/doc/api).

```php
<?php

require 'vendor/autoload.php';

use Jokod\Impactco2Php\Client;
use Jokod\Impactco2Php\Endpoints\HeaterEnpoint;
use Jokod\Impactco2Php\Enums\LanguagesEnum;
use Jokod\Impactco2Php\Endpoints\ThematicsEcvEndpoint;
use Jokod\Impactco2Php\Enums\ThematicEnum;
use Jokod\Impactco2Php\Endpoints\TransportEndpoint;
use Jokod\Impactco2Php\Enums\TransportsEnum;

// Créer une instance du client
$client = new Client([
    'api_key' => 'votre_cle_api', // Optionnel
    'language' => LanguagesEnum::ES // Langue par défaut: FR
]);

// Utiliser l'endpoint ThematicsEcvEndpoint (/thematiques/ecv/{id})
try {
    $thematicsEcvEndpoint = new ThematicsEcvEndpoint(ThematicEnum::FURNITURE, 0); // id et détail
    $response = $client->execute($thematicsEcvEndpoint);
    echo $response;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}

// Utiliser l'endpoint TransportEndpoint (/transport)
try {
    $transportEndpoint = new TransportEndpoint(
        10, // distance
        [ // Liste des transports
            TransportsEnum::CAR,
            TransportsEnum::ELECTRIC_CAR
        ], 
        false, // Tous les transports
        0, // Taux de remplissage moyen
        3 // Inclure la construction
    );
    $response = $client->execute($transportEndpoint);
    echo $response;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
```

## Licence

Ce projet est sous licence [MIT](https://opensource.org/licenses/MIT).
