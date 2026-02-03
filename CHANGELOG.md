# Changelog

Tous les changements notables de ce projet sont documentés dans ce fichier.

## [1.1.0] - 2025-02-03

### Ajouté

- **Endpoint Alimentation** : `AlimentationEndpoint` et `AlimentationCategoryEnum` pour interroger l’API par catégorie (`group`, `rayon`, `popularity`).
- **ThematicsEcvEndpoint** : prise en charge des slugs en plus des IDs (ex. `'mobilier'`, `'transport'`) via `ThematicEnum::getIdFromSlug()` et la propriété `$slugs`.
- **TransportsEnum** : nouvelle constante `WALKING` (30) pour la marche à pied.
- **Entités** : méthodes `fromArray()` et `toArray()` sur `Item`, `Usage`, `Thematic`, `Transport` et `ECV` pour construction et sérialisation depuis les réponses API.
- **Validation** : contrôles dans les constructeurs des entités (`Item`, `Usage`, `Thematic`, `Transport`) avec `InvalidArgumentException` (ID positifs, valeurs non négatives, noms/slugs non vides).
- **Tests** : `AlimentationEndpointTest`, `AlimentationCategoryEnumTest`, `EndpointMultipleCallsTest` et tests unitaires pour les nouvelles validations et méthodes des entités.
- **Coverage** : `make test-coverage` utilise `XDEBUG_MODE=coverage` pour le rapport de couverture.

### Modifié

- **Entités** : `Item`, `Usage`, `Thematic`, `Transport` et `ECV` passent en `readonly` avec constructeur obligatoire ; les setters ont été supprimés.
- **TransportsEnum** : `ON_FOOT` (7) renommé en `BIKE` (vélo) ; libellé « Vélo » et emoji 🚴. « À pied » et 🚶 déplacés sur `WALKING`.
- **Endpoint** : `getPath()` ne modifie plus l’instance ; construction du path dans une variable locale et exclusion des paramètres de requête `null` dans l’URL.
- **ThematicsEcvEndpoint** : constructeur accepte `int|string` (ID ou slug) ; message d’exception : « Invalid thematic ECV identifier or slug ».
- **Enums** : `declare(strict_types=1)` ajouté dans `FoodEnum`, `HeaterEnum`, `ThematicEnum`, `TransportsEnum`.
- **Tests** : adaptations aux nouveaux constructeurs, messages d’exception et types (ex. `999` au lieu de `'invalid_type'` pour Heater/Transport).
- **Version** : 1.0.5 → 1.1.0.

### Supprimé

- **PHPStan** : retiré des dépendances (`require-dev`), de GrumPHP et de la cible `lint` du makefile.
- **Setters** : suppression de tous les setters sur les entités concernées (remplacés par des objets immuables).

[1.1.0]: https://github.com/jokod/impactco2-php/compare/v1.0.5...v1.1.0
