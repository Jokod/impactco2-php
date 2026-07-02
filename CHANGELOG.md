# Changelog

Tous les changements notables de ce projet sont documentés dans ce fichier.

## [1.3.0] - 2026-07-02

### Ajouté

- **TransportsEnum** : nouveaux modes renvoyés par l'API — `CAMPER_VAN` (31), `LIGHT_MOTORCYCLE` (32), `ELECTRIC_MOPED` (33), `CARGO_BIKE` (34) et `VAN` (35), avec libellés et emojis associés.
- **TransportEndpoint** : nouveau paramètre `numberOfPassenger` (0 à 10) pour le calcul du covoiturage, aligné sur l'API `/transport`.
- **HeaterEnum** : nouveau type `PELLET_BOILER_HEATING` (8) « Chauffage avec une chaudière à granulés ».
- **Tests** : couverture des nouveaux modes de transport, des identifiants détaillés (100-204), du paramètre `numberOfPassenger` et du nouveau type de chauffage.

### Modifié

- **TransportEndpoint** : la validation des transports accepte désormais tout identifiant entier positif (dont les variantes détaillées `100-204` exposées par l'API) au lieu de se limiter aux constantes de `TransportsEnum`. Le message d'erreur reste « Invalid transport identifier: X » pour un identifiant non positif.
- **README** : documentation des nouveaux modes de transport, du paramètre `numberOfPassenger`, du type de chauffage « chaudière à granulés » et des langues réellement supportées.

### Supprimé

- **LanguagesEnum** : retrait de la langue `de` (`DE`), rejetée par l'API (`400`) sur la majorité des endpoints. Langues supportées : `fr`, `en`, `es`.

[1.3.0]: https://github.com/jokod/impactco2-php/compare/1.2.5...1.3.0

## [1.2.5] - 2026-07-02

### Corrigé

- **Sécurité** : mise à jour des dépendances (`composer.lock`) pour corriger une vulnérabilité.

[1.2.5]: https://github.com/jokod/impactco2-php/compare/1.2.4...1.2.5

## [1.2.4] - 2026-06-11

### Modifié

- **Dépendances** : mise à jour de `composer` (montée de `phpunit/phpunit` vers `^12`).
- **Tests** : adaptation de `ClientTest` et `EndpointInterfaceTest` à PHPUnit 12.

[1.2.4]: https://github.com/jokod/impactco2-php/compare/1.2.3...1.2.4

## [1.2.3] - 2026-04-19

### Modifié

- **Dépendances** : montée de `phpunit/phpunit` (Dependabot) et mise à jour de `composer.lock`.

[1.2.3]: https://github.com/jokod/impactco2-php/compare/v1.2.2...1.2.3

## [1.2.2] - 2026-02-03

### Modifié

- **ThematicsEcvEndpoint::transformResponse()** : prise en charge des réponses API en liste (tableau indexé) ; si `data` est une liste, retourne un tableau d'objets `ECV[]`, sinon un seul `ECV`. Gestion explicite de `data` vide ou non-tableau (retour des données brutes).

### Ajouté

- **Tests** : `testTransformResponseWithListDataReturnsArrayOfEcv` dans `ThematicsEcvEndpointTest`.

[1.2.2]: https://github.com/jokod/impactco2-php/compare/v1.2.1...v1.2.2

## [1.2.1] - 2026-02-03

### Modifié

- **ECV::fromArray()** : parsing assoupli pour les réponses API sans champ `name` ou `slug` ; utilisation du slug comme nom de repli, ou valeurs par défaut (`'ecv'`, `'—'`) pour éviter « ECV name cannot be empty » lorsque l'API `/thematiques/ecv/{id}` renvoie une structure incomplète.

### Ajouté

- **Tests** : tests de `transformResponse` dans `TransportEndpointTest`, `ThematicsEcvEndpointTest` ; tests ECV `fromArray` sans `name`/`slug` dans `ECVTest`.

[1.2.1]: https://github.com/jokod/impactco2-php/compare/v1.2.0...v1.2.1

## [1.2.0] - 2026-02-03

### Ajouté

- **ApiResponse** : objet de réponse normalisé avec `getData()` (données hydratées en objets de la librairie) et `getWarning()` (message d'avertissement éventuel).
- **Endpoint::transformResponse()** : méthode pour convertir la réponse API en `ApiResponse`, surchargée dans `ThematicsEndpoint`, `TransportEndpoint` et `ThematicsEcvEndpoint` pour retourner des objets de la librairie.
- **Tests** : `ApiResponseTest`, tests de `transformResponse` dans `EndpointTest` et `ThematicsEndpointTest`.

### Modifié

- **Client::execute()** : retourne désormais une `ApiResponse` (au lieu du tableau brut) ; la réponse est transformée via `$endpoint->transformResponse()`. Les endpoints Thématiques, Transport et Détail thématique (ECV) hydratent automatiquement les données en `Thematic[]`, `Transport[]` et `ECV`.
- **README** : exemples avec `$result->getData()` et `$result->getWarning()` ; format des réponses documenté ; gestion des erreurs corrigée (variable `$endpoint` définie, alias `Impactco2Exception`) ; valeurs de `language` précisées (minuscules / constantes).

[1.2.0]: https://github.com/jokod/impactco2-php/compare/v1.1.0...v1.2.0

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
