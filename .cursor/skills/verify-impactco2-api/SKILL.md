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
python3 .cursor/skills/verify-impactco2-api/scripts/verify_api_parity.py --root .
```

Pour un rapport machine :

```bash
python3 .cursor/skills/verify-impactco2-api/scripts/verify_api_parity.py --root . --json
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

5. Si seulement des **warnings / notes** (ex. OpenAPI en retard sur `numberOfPassenger`, libellés) :
   - Les signaler dans le rapport
   - Corriger seulement si c'est un vrai décalage fonctionnel ou de libellés enums

## Ce que le script couvre

| Contrôle | Source |
|----------|--------|
| Liste des paths `/alimentation`, `/chauffage`, `/fruitsetlegumes`, `/thematiques`, `/thematiques/ecv/{id}`, `/transport` | OpenAPI |
| `ENDPOINT` des classes PHP | `src/Endpoints/` |
| Langues `fr`/`en`/`es` | OpenAPI |
| Catégories alimentation `group`/`rayon`/`popularity` | OpenAPI |
| IDs fruits & légumes | OpenAPI |
| Thématiques (IDs + slugs) | OpenAPI + live `/thematiques` |
| Types chauffage (IDs) | GitHub `chauffage.ts` (fallback probe API) |
| Modes transport `< 100` | OpenAPI |
| Params query transport | OpenAPI + param live connu `numberOfPassenger` |
| Smoke live | `GET /transport?km=100&transports=4&numberOfPassenger=1` |

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

- La **Swagger annotée** peut être en retard sur le code Zod des routes (ex. `numberOfPassenger` accepté live mais absent de `/doc`).
- `/chauffage` ne renvoie pas les `id` dans `data` → le script lit les IDs depuis le dépôt ADEME ou par sondage `?chauffages=N`.
- Les IDs transport `100–204` sont des variantes détaillées : la lib les accepte comme entiers positifs sans toutes les constantes dans `TransportsEnum`.

## Hors scope

- Ne pas committer / pusher sauf demande explicite
- Ne pas modifier l'API distante
- Ne pas ajouter de dépendances Composer pour ce check (stdlib Python uniquement)
