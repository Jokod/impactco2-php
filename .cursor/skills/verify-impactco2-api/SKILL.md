---
name: verify-impactco2-api
description: >-
  Vérifie que impactco2-php est aligné sur l'API officielle Impact CO₂
  (OpenAPI + endpoints live). À utiliser quand l'utilisateur demande de
  vérifier l'API, l'alignement des specs, la parité OpenAPI, ou de lancer
  verify-impactco2-api / audit API Impact CO2.
disable-model-invocation: true
---

# Vérifier l'alignement Impact CO₂ API

Compare la librairie locale `jokod/impactco2-php` à la source de vérité :

- OpenAPI : `https://impactco2.fr/api/v1/doc`
- Doc interactive : `https://impactco2.fr/doc/api`
- Endpoints live sous `https://impactco2.fr/api/v1/`

## Quand lancer

- Demande explicite de vérifier / auditer l'API
- Avant une release ou un bump de version
- Après un changelog mentionnant un alignement API

## Workflow

1. Se placer à la racine du dépôt `impactco2-php`.
2. Exécuter le script de parité (obligatoire, ne pas réinventer le check à la main) :

```bash
make verify-api
```

Équivalent direct :

```bash
python3 scripts/verify_api_parity.py --root .
```

Pour un rapport machine :

```bash
python3 scripts/verify_api_parity.py --root . --json
```

3. Interpréter le code de sortie :
   - `0` : pas d'erreur bloquante (warnings possibles)
   - `1` : écarts à corriger
   - `2` : échec technique (réseau / arborescence)

4. Si des **erreurs** apparaissent, corriger la lib **sans demander** :
   - IDs manquants → enums (`HeaterEnum`, `TransportsEnum`, `ThematicEnum`, `FoodEnum`, …)
   - Nouveaux paths OpenAPI → nouveaux endpoints dans `src/Endpoints/`
   - Params transport manquants → `TransportEndpoint`
   - Mettre à jour tests, `README.md` et `CHANGELOG.md` (version courante)
   - Relancer le script jusqu'à `exit 0`

5. Si seulement des **warnings / notes** (libellés, etc.) :
   - Les signaler dans le rapport
   - Corriger seulement si c'est un vrai décalage fonctionnel ou de libellés enums

## Ce que le script couvre

| Contrôle | Source |
|----------|--------|
| Paths OpenAPI vs `ENDPOINT` (templates `{id}` inclus) | OpenAPI + scan `src/Endpoints/` |
| Params query | OpenAPI vs `parent::__construct` |
| Enums (valeurs) | matching auto **OpenAPI uniquement** ↔ `src/Enum/*.php` (extras = erreur) |
| Slugs thématiques | live `/thematiques` + map slug enum |
| Drift chauffage amont | GitHub `chauffage.ts` / sondage live vs OpenAPI (**warning** si OpenAPI en retard) |
| Transports (tous les IDs OpenAPI, y compris ≥100) | OpenAPI ↔ `TransportsEnum` |
| Smoke live (URL auto) | tous les paths OpenAPI |
| Schémas entités | `Entity::*::fromArray` découvert via endpoints |

## Rapport à l'utilisateur (français)

Structure courte :

```markdown
## Verdict
Aligné | Écarts détectés

## Résumé
- Erreurs : N
- Avertissements : N
- OpenAPI : x.y.z

## Écarts
- …

## Actions effectuées
- … (si corrections)
```

Ne pas dump le JSON brut sauf si demandé. Citer la doc : [API Impact CO₂](https://impactco2.fr/doc/api).

## Pièges connus

- La source de vérité des paramètres / enums est l'**OpenAPI** (`/api/v1/doc`), pas le dépôt ADEME ni les IDs acceptés en live hors doc.
- Ne **jamais** enrichir les valeurs OpenAPI avec GitHub/live pour la comparaison enums (ça masquait les IDs chauffage `8`/`9`).
- GitHub/live peut lister des IDs absents de l'OpenAPI → **warning** `openapi_heater_lag` ; la lib reste calée sur l'OpenAPI.
- `/chauffage` ne renvoie pas les `id` dans `data` → le contrôle amont lit GitHub ou sonde `?chauffages=N` uniquement pour le drift doc.
- Les IDs transport `100–204` sont des variantes détaillées : elles sont toutes dans `TransportsEnum` (ex. `CAR_SMALL_PETROL`, `CAR_HYBRID_5`).

## Hors scope

- Ne pas committer / pusher sauf demande explicite
- Ne pas modifier l'API distante
- Ne pas ajouter de dépendances Composer pour ce check (stdlib Python uniquement)
