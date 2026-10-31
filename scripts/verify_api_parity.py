#!/usr/bin/env python3
"""Compare impactco2-php library surface against the live Impact CO₂ API.

Discovery is dynamic from:
- OpenAPI live (paths, query/path params, enums, id lists)
- src/Endpoints/*.php (ENDPOINT, query keys, Entity::fromArray, Enum uses)
- src/Enum/*.php (const values)
- src/Entity/*.php (keys read in fromArray)
"""

from __future__ import annotations

import argparse
import json
import re
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path
from typing import Any

API_BASE = "https://impactco2.fr/api/v1"
OPENAPI_URL = f"{API_BASE}/doc"
GITHUB_CHAUFFAGE = (
    "https://raw.githubusercontent.com/incubateur-ademe/impactco2/develop/"
    "src/data/categories/chauffage.ts"
)


def fetch_json(url: str, timeout: int = 30) -> Any:
    req = urllib.request.Request(url, headers={"Accept": "application/json", "User-Agent": "impactco2-php-verify/1.0"})
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        return json.loads(resp.read().decode("utf-8"))


def fetch_json_retry(url: str, timeout: int = 45, attempts: int = 3, delay: float = 2.0) -> Any:
    """Fetch JSON with retries for transient network/timeouts."""
    last_exc: Exception | None = None
    for attempt in range(1, attempts + 1):
        try:
            return fetch_json(url, timeout=timeout)
        except Exception as exc:  # noqa: BLE001
            last_exc = exc
            if attempt < attempts:
                time.sleep(delay * attempt)
    assert last_exc is not None
    raise last_exc


def fetch_text(url: str, timeout: int = 30) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": "impactco2-php-verify/1.0"})
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        return resp.read().decode("utf-8")


def issue(level: str, code: str, message: str) -> dict[str, str]:
    return {"level": level, "code": code, "message": message}


def parse_php_consts(path: Path) -> dict[str, int | str]:
    text = path.read_text(encoding="utf-8")
    consts: dict[str, int | str] = {}
    for match in re.finditer(
        r"public\s+const\s+(\w+)\s*=\s*(\d+|'[^']*'|\"[^\"]*\")\s*;",
        text,
    ):
        name, raw = match.group(1), match.group(2)
        if raw.isdigit():
            consts[name] = int(raw)
        else:
            consts[name] = raw.strip("'\"")
    return consts


def parse_endpoint_const(path: Path) -> str | None:
    text = path.read_text(encoding="utf-8")
    match = re.search(r"public\s+const\s+ENDPOINT\s*=\s*'([^']+)'\s*;", text)
    return match.group(1) if match else None


def discover_lib_endpoints(endpoints_dir: Path) -> dict[str, str]:
    found: dict[str, str] = {}
    for path in sorted(endpoints_dir.glob("*.php")):
        if path.name == "Endpoint.php":
            continue
        const = parse_endpoint_const(path)
        if const:
            found[path.name] = const
    return found


def normalize_template_path(path: str) -> str:
    """ /thematiques/ecv/{id} -> /thematiques/ecv """
    return re.sub(r"/\{[^}]+\}", "", path).rstrip("/") or "/"


def match_openapi_path(endpoint: str, api_paths: set[str]) -> str | None:
    """Map lib ENDPOINT const to an OpenAPI path (supports {param} templates)."""
    candidate = "/" + endpoint.lstrip("/")
    if candidate in api_paths:
        return candidate
    for path in sorted(api_paths, key=lambda p: (-len(normalize_template_path(p)), p)):
        if normalize_template_path(path) == candidate:
            return path
    return None


def openapi_operation(spec: dict[str, Any], openapi_path: str) -> dict[str, Any]:
    return (spec.get("paths", {}).get(openapi_path) or {}).get("get") or {}


def openapi_params(spec: dict[str, Any], openapi_path: str, location: str | None = None) -> list[dict[str, Any]]:
    params = openapi_operation(spec, openapi_path).get("parameters") or []
    if location is None:
        return [p for p in params if isinstance(p, dict)]
    return [p for p in params if isinstance(p, dict) and p.get("in") == location]


def openapi_query_param_names(spec: dict[str, Any], openapi_path: str) -> set[str]:
    return {p["name"] for p in openapi_params(spec, openapi_path, "query") if p.get("name")}


def php_endpoint_query_keys(endpoint_src: Path) -> set[str]:
    text = endpoint_src.read_text(encoding="utf-8")
    match = re.search(r"parent::__construct\((.*?)\);\s*\n\s*\}", text, flags=re.S)
    block = match.group(1) if match else text
    return set(re.findall(r"'([a-zA-Z_][a-zA-Z0-9_]*)'\s*=>", block))


def extract_id_list(description: str) -> dict[int, str]:
    """Parse '- N : label' or '- N : slug' lines from OpenAPI descriptions."""
    found: dict[int, str] = {}
    for match in re.finditer(r"^- (\d+) : (\S+)", description or "", flags=re.M):
        found[int(match.group(1))] = match.group(2)
    return found


def extract_param_values(param: dict[str, Any]) -> set[Any]:
    schema = param.get("schema") or {}
    enum = schema.get("enum")
    if enum:
        return set(enum)
    ids = extract_id_list(param.get("description") or "")
    if ids:
        return set(ids.keys())
    return set()


def parse_chauffage_ids_from_ts(source: str) -> dict[int, str]:
    ids: dict[int, str] = {}
    for block in re.finditer(
        r"\{\s*id:\s*(\d+),\s*slug:\s*'([^']+)'",
        source,
        flags=re.S,
    ):
        ids[int(block.group(1))] = block.group(2)
    return ids


def probe_heater_ids(max_id: int = 20) -> dict[int, str]:
    found: dict[int, str] = {}
    for heater_id in range(1, max_id + 1):
        try:
            payload = fetch_json(f"{API_BASE}/chauffage?chauffages={heater_id}")
        except urllib.error.HTTPError:
            continue
        data = payload.get("data") or []
        if data:
            found[heater_id] = data[0].get("slug") or data[0].get("name") or ""
    return found


def load_all_enums(enum_dir: Path) -> dict[str, dict[str, int | str]]:
    enums: dict[str, dict[str, int | str]] = {}
    for path in sorted(enum_dir.glob("*.php")):
        consts = parse_php_consts(path)
        if consts:
            enums[path.name] = consts
    return enums


def values_as_comparable(values: set[Any]) -> set[str]:
    return {str(v) for v in values}


def jaccard(a: set[str], b: set[str]) -> float:
    if not a and not b:
        return 0.0
    return len(a & b) / len(a | b)


def name_affinity(enum_file: str, openapi_path: str, param_name: str) -> float:
    """Heuristic link between Enum filename and OpenAPI path/param."""
    base = enum_file.replace("Enum.php", "")
    parts = [p.lower() for p in re.findall(r"[A-Z]?[a-z]+|[A-Z]+(?![a-z])", base) if p]
    hay = f"{openapi_path} {param_name}".lower()
    score = 0.0
    aliases = {
        "food": ("fruit", "legume", "category"),
        "heater": ("chauffage", "chauffages"),
        "transport": ("transport", "transports"),
        "thematic": ("thematique", "thematiques"),
        "language": ("language",),
        "alimentation": ("alimentation",),
        "category": ("category",),
    }
    for part in parts:
        if part in hay:
            score += 0.35
        for needle in aliases.get(part, ()):
            if needle in hay:
                score += 0.35
    if param_name.lower() in base.lower() or base.lower() in param_name.lower():
        score += 0.4
    return min(score, 1.0)


def value_match_score(api_cmp: set[str], lib_cmp: set[str]) -> float:
    if not api_cmp or not lib_cmp:
        return 0.0
    inter = api_cmp & lib_cmp
    if not inter:
        return 0.0
    precision = len(inter) / len(lib_cmp)
    recall = len(inter) / len(api_cmp)
    f1 = 2 * precision * recall / (precision + recall)
    size_ratio = min(len(api_cmp), len(lib_cmp)) / max(len(api_cmp), len(lib_cmp))
    # Prefer similar-sized sets; pure tiny-subset-of-huge-list scores low
    return 0.7 * f1 + 0.3 * size_ratio


def match_enums_to_param_sets(
    enums: dict[str, dict[str, int | str]],
    param_sets: list[tuple[str, str, set[Any]]],
) -> list[tuple[str, str, str, set[Any], set[Any]]]:
    """
    Greedy match OpenAPI param value sets to PHP enum files.
    Returns list of (enum_file, openapi_path, param_name, api_values, lib_values).
    """
    candidates: list[tuple[float, str, str, str, set[Any], set[Any]]] = []
    for enum_file, consts in enums.items():
        lib_vals = set(consts.values())
        lib_cmp = values_as_comparable(lib_vals)
        if not lib_cmp:
            continue
        for openapi_path, param_name, api_vals in param_sets:
            api_cmp = values_as_comparable(api_vals)
            if not api_cmp:
                continue
            vscore = value_match_score(api_cmp, lib_cmp)
            nscore = name_affinity(enum_file, openapi_path, param_name)
            score = 0.55 * vscore + 0.45 * nscore
            # Strong name + decent containment still counts (TransportsEnum ⊂ API list)
            containment = len(api_cmp & lib_cmp) / len(lib_cmp)
            if nscore >= 0.5 and containment >= 0.8:
                score = max(score, 0.7)
            if score < 0.4:
                continue
            candidates.append((score, enum_file, openapi_path, param_name, api_vals, lib_vals))

    candidates.sort(key=lambda x: (-x[0], x[1], x[2], x[3]))
    used_enums: set[str] = set()
    used_params: set[tuple[str, str]] = set()
    matches: list[tuple[str, str, str, set[Any], set[Any]]] = []
    for score, enum_file, openapi_path, param_name, api_vals, lib_vals in candidates:
        key = (openapi_path, param_name)
        if enum_file in used_enums or key in used_params:
            continue
        api_cmp = values_as_comparable(api_vals)
        if api_cmp <= {"fr", "en", "es", "de", "it"} and "Language" not in enum_file:
            better = any(
                s > score and "Language" in ef and values_as_comparable(av) == api_cmp
                for s, ef, _, _, av, _ in candidates
            )
            if better:
                continue
        used_enums.add(enum_file)
        used_params.add(key)
        matches.append((enum_file, openapi_path, param_name, api_vals, lib_vals))
    return matches


def discover_entity_class(endpoint_src: Path) -> str | None:
    text = endpoint_src.read_text(encoding="utf-8")
    # Prefer Entity\X::fromArray or imported X::fromArray used in transformResponse
    match = re.search(r"(\w+)::fromArray\s*\(", text)
    if not match:
        return None
    short = match.group(1)
    use = re.search(rf"use\s+([\w\\]+\\{short})\s*;", text)
    if use:
        return use.group(1).split("\\")[-1]
    return short


def entity_fromarray_keys(entity_src: Path) -> set[str]:
    """Keys read from $data[...] in fromArray (including alternatives)."""
    text = entity_src.read_text(encoding="utf-8")
    # Limit to fromArray method body if possible
    match = re.search(r"function\s+fromArray\s*\(.*?\)\s*:\s*self\s*\{(.*)\n\s*\}", text, flags=re.S)
    body = match.group(1) if match else text
    return set(re.findall(r"\$data\[\s*['\"]([^'\"]+)['\"]\s*\]", body))


def nested_entity_refs(entity_src: Path) -> dict[str, str]:
    """Map field-ish names to nested entity classes referenced in fromArray."""
    text = entity_src.read_text(encoding="utf-8")
    refs: dict[str, str] = {}
    for match in re.finditer(r"(\w+)::fromArray\s*\(", text):
        name = match.group(1)
        if name != "self":
            refs[name.lower()] = name
    return refs


def transport_accepts_dynamic_ids(endpoint_src: Path) -> bool:
    text = endpoint_src.read_text(encoding="utf-8")
    uses_enum_whitelist = bool(
        re.search(r"TransportsEnum::toArray\(\)|in_array\(\s*\$transport\s*,\s*TransportsEnum", text)
    )
    checks_positive_int = "transport <= 0" in text or "$transport <= 0" in text
    return checks_positive_int and not uses_enum_whitelist


def default_query_value(param: dict[str, Any]) -> Any | None:
    name = param.get("name") or ""
    schema = param.get("schema") or {}
    enum = schema.get("enum")
    if name == "detail" and isinstance(enum, list) and 1 in enum:
        return 1
    if enum:
        return enum[0]
    ids = extract_id_list(param.get("description") or "")
    if ids:
        return sorted(ids.keys())[0]
    typ = schema.get("type")
    if name == "km":
        return 100
    if name == "month":
        return 6
    if name == "m2":
        return 63
    if typ in {"integer", "number"}:
        return 1
    if typ == "string":
        return "1"
    if param.get("required"):
        return 1
    return None


def build_smoke_url(
    api_base: str,
    openapi_path: str,
    spec: dict[str, Any],
    path_values: dict[str, Any] | None = None,
) -> str:
    path_values = dict(path_values or {})
    path = openapi_path
    for param in openapi_params(spec, openapi_path, "path"):
        name = param.get("name")
        if not name:
            continue
        if name not in path_values:
            ids = extract_id_list(param.get("description") or "")
            path_values[name] = sorted(ids.keys())[0] if ids else 1
        path = path.replace("{" + name + "}", str(path_values[name]))

    query: dict[str, Any] = {}
    for param in openapi_params(spec, openapi_path, "query"):
        name = param.get("name")
        if not name or name == "language":
            continue
        # Always send required params; also send params that have enums/ids so list endpoints filter sanely
        values = extract_param_values(param)
        required = bool(param.get("required"))
        if not required and not values and name not in {"km", "month", "m2", "detail", "category", "chauffages", "transports"}:
            continue
        value = default_query_value(param)
        if value is not None:
            query[name] = value

    url = api_base.rstrip("/") + path
    if query:
        url += "?" + urllib.parse.urlencode(query)
    return url


def first_data_item(payload: Any) -> dict[str, Any] | None:
    data = payload.get("data") if isinstance(payload, dict) else None
    if isinstance(data, list) and data and isinstance(data[0], dict):
        return data[0]
    if isinstance(data, dict):
        return data
    return None


def collect_openapi_param_sets(spec: dict[str, Any]) -> list[tuple[str, str, set[Any]]]:
    """Unique (path, param_name, values) with non-empty extractable values."""
    seen: dict[tuple[str, str], set[Any]] = {}
    for path in spec.get("paths", {}):
        for param in openapi_params(spec, path):
            name = param.get("name")
            if not name:
                continue
            values = extract_param_values(param)
            if not values:
                continue
            key = (path, name)
            # Prefer larger value sets (e.g. full id lists)
            if key not in seen or len(values) > len(seen[key]):
                seen[key] = values
    # Deduplicate identical language enums across paths → keep one representative
    by_values: dict[frozenset[Any], tuple[str, str, set[Any]]] = {}
    for (path, name), values in seen.items():
        fv = frozenset(values)
        # Keep path-specific id lists separate; merge only pure string language-like small enums
        if name == "language" or (all(isinstance(v, str) for v in values) and len(values) <= 10):
            by_values.setdefault(fv, (path, name, values))
        else:
            by_values[frozenset([path, name, *fv])] = (path, name, values)
    return list(by_values.values())


def parse_slug_map(enum_path: Path, consts: dict[str, int | str]) -> dict[str, int]:
    text = enum_path.read_text(encoding="utf-8")
    slugs = dict(re.findall(r"'([a-z0-9-]+)'\s*=>\s*self::(\w+)", text))
    out: dict[str, int] = {}
    for slug, const_name in slugs.items():
        if const_name in consts and isinstance(consts[const_name], int):
            out[slug] = int(consts[const_name])
    return out


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--root", type=Path, default=Path.cwd(), help="Project root (default: cwd)")
    parser.add_argument("--json", action="store_true", help="Machine-readable JSON output")
    args = parser.parse_args()
    root: Path = args.root.resolve()
    enum_dir = root / "src" / "Enum"
    endpoints_dir = root / "src" / "Endpoints"
    entity_dir = root / "src" / "Entity"

    if not enum_dir.is_dir() or not endpoints_dir.is_dir():
        print(f"ERROR: expected src/Enum and src/Endpoints under {root}", file=sys.stderr)
        return 2

    issues: list[dict[str, str]] = []
    notes: list[str] = []

    try:
        openapi = fetch_json_retry(OPENAPI_URL)
    except Exception as exc:  # noqa: BLE001
        print(f"ERROR: cannot fetch OpenAPI ({OPENAPI_URL}): {exc}", file=sys.stderr)
        return 2

    api_paths = set(openapi.get("paths", {}).keys())
    lib_endpoints = discover_lib_endpoints(endpoints_dir)
    if not lib_endpoints:
        issues.append(issue("error", "endpoints_none", "Aucun endpoint PHP avec const ENDPOINT trouvé"))

    for filename in sorted(p.name for p in endpoints_dir.glob("*.php") if p.name != "Endpoint.php"):
        if filename not in lib_endpoints:
            issues.append(issue("error", "endpoint_const_missing", f"Const ENDPOINT manquante: {filename}"))

    # Path coverage (dynamic template matching)
    endpoint_to_path: dict[str, str] = {}
    for filename, endpoint in lib_endpoints.items():
        matched = match_openapi_path(endpoint, api_paths)
        if matched:
            endpoint_to_path[endpoint] = matched
        else:
            issues.append(
                issue("error", "paths_extra_in_lib", f"Endpoint lib absent de l'OpenAPI: {endpoint}")
            )

    covered = set(endpoint_to_path.values())
    # An OpenAPI path is covered if its normalized form matches a lib endpoint mapping
    uncovered = []
    for path in sorted(api_paths):
        if path in covered:
            continue
        # Also covered if another mapping points to same template
        if any(normalize_template_path(path) == normalize_template_path(c) for c in covered):
            continue
        uncovered.append(path)
    if uncovered:
        issues.append(
            issue(
                "error",
                "paths_missing_in_lib",
                f"Paths OpenAPI non couverts par la lib: {uncovered}",
            )
        )

    # Query params for every mapped endpoint
    file_by_endpoint = {ep: name for name, ep in lib_endpoints.items()}
    for endpoint, openapi_path in sorted(endpoint_to_path.items()):
        filename = file_by_endpoint[endpoint]
        api_params = openapi_query_param_names(openapi, openapi_path)
        lib_params = php_endpoint_query_keys(endpoints_dir / filename)
        missing_params = api_params - lib_params
        extra_params = lib_params - api_params
        if missing_params:
            issues.append(
                issue(
                    "error",
                    "query_params_missing",
                    f"{endpoint}: params OpenAPI manquants dans la lib: {sorted(missing_params)}",
                )
            )
        if extra_params:
            issues.append(
                issue(
                    "error",
                    "query_params_extra",
                    f"{endpoint}: params lib absents de l'OpenAPI: {sorted(extra_params)}",
                )
            )

    # Enum ↔ OpenAPI param value sets (dynamic matching).
    # OpenAPI is the sole source of truth for enum comparison — never enrich/replace
    # OpenAPI values with GitHub or live probes (that hid heater IDs 8–9 drift).
    enums = load_all_enums(enum_dir)
    param_sets = collect_openapi_param_sets(openapi)

    # Secondary: detect OpenAPI vs upstream (GitHub / live probe) drift for chauffage
    heater_upstream: set[int] | None = None
    try:
        heater_ids = parse_chauffage_ids_from_ts(fetch_text(GITHUB_CHAUFFAGE))
        heater_upstream = set(heater_ids.keys())
        notes.append(f"Chauffage IDs GitHub develop ({len(heater_ids)} types): {sorted(heater_ids)}")
    except Exception:
        try:
            heater_ids = probe_heater_ids()
            heater_upstream = set(heater_ids.keys())
            notes.append(f"Chauffage IDs sondés via API ({len(heater_ids)} types): {sorted(heater_ids)}")
        except Exception as exc:  # noqa: BLE001
            issues.append(
                issue("warn", "heater_upstream_failed", f"Contrôle amont chauffage impossible: {exc}")
            )

    openapi_heater_ids: set[int] = set()
    for path, name, values in param_sets:
        if "chauffage" in path or name == "chauffages":
            for value in values:
                if str(value).isdigit():
                    openapi_heater_ids.add(int(value))

    if heater_upstream is not None and openapi_heater_ids:
        only_upstream = heater_upstream - openapi_heater_ids
        only_openapi = openapi_heater_ids - heater_upstream
        if only_upstream:
            issues.append(
                issue(
                    "warn",
                    "openapi_heater_lag",
                    f"OpenAPI /chauffage?chauffages en retard vs GitHub/live: "
                    f"IDs {sorted(only_upstream)} absents de l'OpenAPI "
                    f"(la lib doit suivre l'OpenAPI, pas le dépôt amont)",
                )
            )
        if only_openapi:
            issues.append(
                issue(
                    "warn",
                    "openapi_heater_stale",
                    f"OpenAPI /chauffage?chauffages liste des IDs absents de GitHub/live: "
                    f"{sorted(only_openapi)}",
                )
            )
    elif heater_upstream is not None and not openapi_heater_ids:
        issues.append(
            issue(
                "warn",
                "openapi_heater_ids_unparsed",
                "Impossible d'extraire les IDs chauffage depuis l'OpenAPI "
                f"(amont: {sorted(heater_upstream)})",
            )
        )

    matches = match_enums_to_param_sets(enums, param_sets)
    matched_enum_files = {m[0] for m in matches}
    for enum_file in sorted(enums):
        if enum_file not in matched_enum_files:
            issues.append(
                issue(
                    "warn",
                    "enum_unmatched",
                    f"{enum_file}: aucun param OpenAPI automatiquement associé",
                )
            )

    def _sort_values(values: set[str]) -> list[str]:
        return sorted(
            values,
            key=lambda x: (not str(x).isdigit(), int(x) if str(x).isdigit() else x),
        )

    for enum_file, openapi_path, param_name, api_vals, lib_vals in matches:
        api_cmp = values_as_comparable(api_vals)
        lib_cmp = values_as_comparable(lib_vals)
        missing = api_cmp - lib_cmp
        extra = lib_cmp - api_cmp

        had_gap = False
        if missing:
            had_gap = True
            issues.append(
                issue(
                    "error",
                    "enum_values_missing",
                    f"{enum_file} vs {openapi_path}?{param_name}: valeurs OpenAPI absentes de la lib: "
                    f"{_sort_values(missing)}",
                )
            )
        if extra:
            had_gap = True
            # Always error: OpenAPI is the source of truth (numeric extras used to be warns
            # and were masked further by GitHub enrichment of chauffage IDs).
            issues.append(
                issue(
                    "error",
                    "enum_values_extra",
                    f"{enum_file} vs {openapi_path}?{param_name}: valeurs lib absentes de l'OpenAPI: "
                    f"{_sort_values(extra)}",
                )
            )
        if not had_gap:
            notes.append(f"Enum {enum_file} aligné sur {openapi_path}?{param_name}")

        # Slug map check when enum exposes slug => const
        enum_path = enum_dir / enum_file
        slug_map = parse_slug_map(enum_path, enums[enum_file])
        if slug_map:
            # Prefer live list if this looks like thematics
            api_slugs: dict[str, int] = {}
            if "thematique" in openapi_path or "thematique" in enum_file.lower():
                try:
                    live = fetch_json(f"{API_BASE}/thematiques")
                    for item in live.get("data") or []:
                        if isinstance(item, dict) and "slug" in item and "id" in item:
                            api_slugs[str(item["slug"])] = int(item["id"])
                except Exception as exc:  # noqa: BLE001
                    issues.append(issue("warn", "thematiques_fetch_failed", str(exc)))
            if not api_slugs:
                # From OpenAPI id descriptions on path params
                for param in openapi_params(openapi, openapi_path, "path"):
                    api_slugs.update(
                        {slug: i for i, slug in extract_id_list(param.get("description") or "").items()}
                    )
                for param in openapi_params(openapi, openapi_path):
                    if param.get("name") == "id":
                        api_slugs.update(
                            {slug: i for i, slug in extract_id_list(param.get("description") or "").items()}
                        )
            for slug, tid in api_slugs.items():
                if slug not in slug_map:
                    issues.append(
                        issue("error", "enum_slug_missing", f"{enum_file}: slug manquant '{slug}' (id={tid})")
                    )
                elif slug_map[slug] != tid:
                    issues.append(
                        issue(
                            "error",
                            "enum_slug_id_mismatch",
                            f"{enum_file}: slug '{slug}' lib={slug_map[slug]} api={tid}",
                        )
                    )

    # Live smoke for every OpenAPI path + every lib endpoint (URL built from OpenAPI)
    smoke_failures: list[str] = []
    smoked_paths: set[str] = set()
    for openapi_path in sorted(api_paths):
        url = build_smoke_url(API_BASE, openapi_path, openapi)
        smoked_paths.add(openapi_path)
        try:
            payload = fetch_json_retry(url)
        except Exception as exc:  # noqa: BLE001
            smoke_failures.append(f"{openapi_path}: {exc}")
            continue

        if not isinstance(payload, dict) or "data" not in payload:
            issues.append(issue("error", "smoke_schema_wrapper", f"Smoke {openapi_path}: wrapper sans 'data'"))
            continue

        item = first_data_item(payload)
        if item is None:
            issues.append(issue("warn", "smoke_empty", f"Smoke {openapi_path}: data vide ({url})"))
            continue

        # Entity schema: if a lib endpoint maps here and hydrates an entity, check fromArray keys
        for endpoint, mapped in endpoint_to_path.items():
            if mapped != openapi_path:
                continue
            endpoint_file = endpoints_dir / file_by_endpoint[endpoint]
            entity_name = discover_entity_class(endpoint_file)
            if not entity_name or not entity_dir.is_dir():
                continue
            entity_path = entity_dir / f"{entity_name}.php"
            if not entity_path.exists():
                continue
            required = entity_fromarray_keys(entity_path)
            # Alternatives: footprintDetail vs items — presence of either is OK
            alternatives = []
            if "footprintDetail" in required and "items" in required:
                alternatives = ["footprintDetail", "items"]
                required = required - {"footprintDetail", "items"}
            missing = required - set(item.keys())
            # Drop keys that are nested-only constructs
            missing -= {"items", "footprintDetail"} if alternatives else set()
            if alternatives and not any(a in item for a in alternatives):
                issues.append(
                    issue(
                        "warn",
                        "entity_alt_keys_missing",
                        f"{endpoint}: aucune des clés {alternatives} présente",
                    )
                )
            if missing:
                # fromArray often has defaults for missing keys — only error if clearly primary identity fields
                primary = missing & {"id", "name", "slug", "value", "ecv"}
                if primary:
                    issues.append(
                        issue(
                            "error",
                            "entity_schema_keys",
                            f"{endpoint} ({entity_name}): clés manquantes {sorted(primary)} "
                            f"(got {sorted(item.keys())})",
                        )
                    )

            # Nested objects/lists referenced by entity
            for nested_name in nested_entity_refs(entity_path):
                nested_path = entity_dir / f"{nested_name[0].upper() + nested_name[1:]}.php"
                # Try exact class file from refs values
            for _field, class_name in nested_entity_refs(entity_path).items():
                nested_path = entity_dir / f"{class_name}.php"
                if not nested_path.exists():
                    continue
                nested_keys = entity_fromarray_keys(nested_path)
                # Find nested payload: usage dict, or first element of items/footprintDetail
                candidates: list[dict[str, Any]] = []
                for key, val in item.items():
                    if isinstance(val, dict) and class_name.lower() in key.lower():
                        candidates.append(val)
                    if isinstance(val, dict) and nested_keys and nested_keys <= {k.lower() for k in val} | set(val):
                        candidates.append(val)
                    if isinstance(val, list) and val and isinstance(val[0], dict):
                        if {"id", "value"} <= set(val[0].keys()) and class_name in {"Item"}:
                            candidates.append(val[0])
                # Usage special-case: API uses peryear/defaultyears
                if class_name == "Usage" and isinstance(item.get("usage"), dict):
                    usage = item["usage"]
                    usage_l = {k.lower() for k in usage}
                    needed = {k.lower() for k in nested_keys}
                    if not needed <= usage_l:
                        issues.append(
                            issue(
                                "error",
                                "nested_schema_keys",
                                f"{endpoint}: Usage keys manquantes (got {sorted(usage.keys())})",
                            )
                        )
                elif class_name == "Item":
                    details = item.get("footprintDetail") or item.get("items")
                    if isinstance(details, list) and details and isinstance(details[0], dict):
                        if not {"id", "value"} <= set(details[0].keys()):
                            issues.append(
                                issue(
                                    "error",
                                    "nested_schema_keys",
                                    f"{endpoint}: Item invalide {details[0]!r}",
                                )
                            )

        notes.append(f"Smoke {openapi_path} OK")

    errors = [i for i in issues if i["level"] == "error"]
    warns = [i for i in issues if i["level"] == "warn"]

    if smoke_failures and not errors:
        for failure in smoke_failures:
            print(f"ERROR: smoke échoué: {failure}", file=sys.stderr)
        return 2
    if smoke_failures:
        for failure in smoke_failures:
            issues.append(issue("warn", "smoke_failed", f"Smoke échoué: {failure}"))
        warns = [i for i in issues if i["level"] == "warn"]

    report = {
        "ok": len(errors) == 0,
        "openapi_version": (openapi.get("info") or {}).get("version"),
        "api_paths": sorted(api_paths),
        "summary": {"errors": len(errors), "warnings": len(warns)},
        "notes": notes,
        "issues": issues,
    }

    if args.json:
        print(json.dumps(report, ensure_ascii=False, indent=2))
    else:
        status = "OK" if report["ok"] else "ÉCARTS DÉTECTÉS"
        print(f"# Vérification Impact CO₂ API — {status}")
        print(f"OpenAPI version: {report['openapi_version']}")
        print(f"Paths API: {', '.join(report['api_paths'])}")
        print(f"Erreurs: {len(errors)} | Avertissements: {len(warns)}")
        if notes:
            print("\n## Notes")
            for note in notes:
                print(f"- {note}")
        if errors:
            print("\n## Erreurs")
            for item in errors:
                print(f"- [{item['code']}] {item['message']}")
        if warns:
            print("\n## Avertissements")
            for item in warns:
                print(f"- [{item['code']}] {item['message']}")
        if report["ok"] and not warns:
            print("\nLa librairie est alignée sur l'API live / OpenAPI.")

    return 0 if report["ok"] else 1


if __name__ == "__main__":
    sys.exit(main())
