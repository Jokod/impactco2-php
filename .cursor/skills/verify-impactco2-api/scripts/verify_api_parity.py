#!/usr/bin/env python3
"""Compare impactco2-php library surface against the live Impact CO₂ API."""

from __future__ import annotations

import argparse
import json
import re
import sys
import urllib.error
import urllib.request
from pathlib import Path
from typing import Any

API_BASE = "https://impactco2.fr/api/v1"
OPENAPI_URL = f"{API_BASE}/doc"
GITHUB_CHAUFFAGE = (
    "https://raw.githubusercontent.com/incubateur-ademe/impactco2/develop/"
    "src/data/categories/chauffage.ts"
)

EXPECTED_PATHS = {
    "/alimentation",
    "/chauffage",
    "/fruitsetlegumes",
    "/thematiques",
    "/thematiques/ecv/{id}",
    "/transport",
}

ENDPOINT_CONST_TO_PATH = {
    "AlimentationEndpoint.php": "alimentation",
    "HeaterEndpoint.php": "chauffage",
    "FruitsVegetables.php": "fruitsetlegumes",
    "ThematicsEndpoint.php": "thematiques",
    "ThematicsEcvEndpoint.php": "thematiques/ecv",
    "TransportEndpoint.php": "transport",
}


def fetch_json(url: str, timeout: int = 30) -> Any:
    req = urllib.request.Request(url, headers={"Accept": "application/json", "User-Agent": "impactco2-php-verify/1.0"})
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        return json.loads(resp.read().decode("utf-8"))


def fetch_text(url: str, timeout: int = 30) -> str:
    req = urllib.request.Request(url, headers={"User-Agent": "impactco2-php-verify/1.0"})
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        return resp.read().decode("utf-8")


def parse_php_consts(path: Path) -> dict[str, int | str]:
    """Extract public const NAME = value from a PHP enum-like class."""
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


def parse_php_names_map(path: Path) -> dict[int | str, str]:
    """Best-effort parse of static $names = [ self::X => 'label', ... ]."""
    text = path.read_text(encoding="utf-8")
    consts = parse_php_consts(path)
    names: dict[int | str, str] = {}
    for match in re.finditer(r"self::(\w+)\s*=>\s*'((?:\\'|[^'])*)'", text):
        const_name, label = match.group(1), match.group(2).replace("\\'", "'")
        if const_name in consts:
            names[consts[const_name]] = label
    return names


def parse_endpoint_const(path: Path) -> str | None:
    text = path.read_text(encoding="utf-8")
    match = re.search(r"public\s+const\s+ENDPOINT\s*=\s*'([^']+)'\s*;", text)
    return match.group(1) if match else None


def extract_transport_ids_from_openapi(spec: dict[str, Any]) -> set[int]:
    params = spec.get("paths", {}).get("/transport", {}).get("get", {}).get("parameters", [])
    for param in params:
        if param.get("name") != "transports":
            continue
        desc = param.get("description") or ""
        return {int(x) for x in re.findall(r"^- (\d+) :", desc, flags=re.M)}
    return set()


def extract_language_enum(spec: dict[str, Any]) -> set[str]:
    languages: set[str] = set()
    for path_item in spec.get("paths", {}).values():
        for method in path_item.values():
            if not isinstance(method, dict):
                continue
            for param in method.get("parameters", []):
                if param.get("name") != "language":
                    continue
                enum = (param.get("schema") or {}).get("enum") or []
                languages.update(str(x) for x in enum)
    return languages


def extract_alimentation_categories(spec: dict[str, Any]) -> set[str]:
    params = spec.get("paths", {}).get("/alimentation", {}).get("get", {}).get("parameters", [])
    for param in params:
        if param.get("name") == "category":
            return set((param.get("schema") or {}).get("enum") or [])
    return set()


def extract_food_category_ids(spec: dict[str, Any]) -> set[int]:
    params = spec.get("paths", {}).get("/fruitsetlegumes", {}).get("get", {}).get("parameters", [])
    for param in params:
        if param.get("name") != "category":
            continue
        desc = param.get("description") or ""
        return {int(x) for x in re.findall(r"^- (\d+) :", desc, flags=re.M)}
    return set()


def extract_thematic_ids_from_openapi(spec: dict[str, Any]) -> dict[int, str]:
    params = (
        spec.get("paths", {})
        .get("/thematiques/ecv/{id}", {})
        .get("get", {})
        .get("parameters", [])
    )
    for param in params:
        if param.get("name") != "id":
            continue
        desc = param.get("description") or ""
        return {int(i): slug for i, slug in re.findall(r"^- (\d+) : (\S+)", desc, flags=re.M)}
    return {}


def parse_chauffage_ids_from_ts(source: str) -> dict[int, str]:
    """Parse id/slug pairs from chauffage.ts data file."""
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


def transport_query_params(endpoint_src: Path) -> set[str]:
    text = endpoint_src.read_text(encoding="utf-8")
    # Keys in the query array passed to parent::__construct
    match = re.search(r"parent::__construct\([^;]+;\s*\)", text, flags=re.S)
    if not match:
        # Fallback: collect quoted keys near query construction
        return set(re.findall(r"'([a-zA-Z]+)'\s*=>", text))
    block = match.group(0)
    return set(re.findall(r"'([a-zA-Z]+)'\s*=>", block))


def openapi_transport_params(spec: dict[str, Any]) -> set[str]:
    params = spec.get("paths", {}).get("/transport", {}).get("get", {}).get("parameters", [])
    return {p["name"] for p in params if p.get("in") == "query"}


def issue(level: str, code: str, message: str) -> dict[str, str]:
    return {"level": level, "code": code, "message": message}


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "--root",
        type=Path,
        default=Path.cwd(),
        help="Project root (default: cwd)",
    )
    parser.add_argument("--json", action="store_true", help="Machine-readable JSON output")
    args = parser.parse_args()
    root: Path = args.root.resolve()
    enum_dir = root / "src" / "Enum"
    endpoints_dir = root / "src" / "Endpoints"

    if not enum_dir.is_dir() or not endpoints_dir.is_dir():
        print(f"ERROR: expected src/Enum and src/Endpoints under {root}", file=sys.stderr)
        return 2

    issues: list[dict[str, str]] = []
    notes: list[str] = []

    try:
        openapi = fetch_json(OPENAPI_URL)
    except Exception as exc:  # noqa: BLE001
        print(f"ERROR: cannot fetch OpenAPI ({OPENAPI_URL}): {exc}", file=sys.stderr)
        return 2

    api_paths = set(openapi.get("paths", {}).keys())
    if api_paths != EXPECTED_PATHS:
        missing = EXPECTED_PATHS - api_paths
        extra = api_paths - EXPECTED_PATHS
        if missing:
            issues.append(issue("error", "paths_missing_in_api", f"Paths absents de l'OpenAPI: {sorted(missing)}"))
        if extra:
            issues.append(issue("error", "paths_extra_in_api", f"Nouveaux paths OpenAPI non couverts: {sorted(extra)}"))

    # Library endpoint paths
    lib_paths: set[str] = set()
    for filename, expected in ENDPOINT_CONST_TO_PATH.items():
        path = endpoints_dir / filename
        if not path.exists():
            issues.append(issue("error", "endpoint_file_missing", f"Fichier endpoint manquant: {filename}"))
            continue
        const = parse_endpoint_const(path)
        if const != expected:
            issues.append(
                issue(
                    "error",
                    "endpoint_const_mismatch",
                    f"{filename}: ENDPOINT='{const}' (attendu '{expected}')",
                )
            )
        if const:
            lib_paths.add("/" + const if not const.startswith("thematiques/ecv") else "/thematiques/ecv/{id}")
            if const == "thematiques":
                lib_paths.add("/thematiques")
            elif const == "thematiques/ecv":
                lib_paths.add("/thematiques/ecv/{id}")
            else:
                lib_paths.add(f"/{const}")

    # Languages
    api_langs = extract_language_enum(openapi)
    lib_langs = set(str(v) for v in parse_php_consts(enum_dir / "LanguagesEnum.php").values())
    if api_langs and lib_langs != api_langs:
        issues.append(
            issue(
                "error",
                "languages_mismatch",
                f"LanguagesEnum={sorted(lib_langs)} vs API={sorted(api_langs)}",
            )
        )

    # Alimentation categories
    api_cats = extract_alimentation_categories(openapi)
    lib_cats = set(str(v) for v in parse_php_consts(enum_dir / "AlimentationCategoryEnum.php").values())
    if api_cats and lib_cats != api_cats:
        issues.append(
            issue(
                "error",
                "alimentation_category_mismatch",
                f"AlimentationCategoryEnum={sorted(lib_cats)} vs API={sorted(api_cats)}",
            )
        )

    # Food categories
    api_food = extract_food_category_ids(openapi)
    lib_food = set(int(v) for v in parse_php_consts(enum_dir / "FoodEnum.php").values() if isinstance(v, int))
    if api_food and lib_food != api_food:
        missing = api_food - lib_food
        extra = lib_food - api_food
        if missing:
            issues.append(issue("error", "food_ids_missing", f"FoodEnum IDs manquants: {sorted(missing)}"))
        if extra:
            issues.append(issue("warn", "food_ids_extra", f"FoodEnum IDs en trop vs OpenAPI: {sorted(extra)}"))

    # Thematics
    api_thematics = extract_thematic_ids_from_openapi(openapi)
    try:
        live_thematics = {
            int(item["id"]): item.get("slug", "")
            for item in (fetch_json(f"{API_BASE}/thematiques").get("data") or [])
        }
    except Exception as exc:  # noqa: BLE001
        live_thematics = {}
        issues.append(issue("warn", "thematiques_fetch_failed", f"Impossible de fetch /thematiques: {exc}"))

    thematic_source = live_thematics or api_thematics
    lib_thematics = {
        int(v): k
        for k, v in parse_php_consts(enum_dir / "ThematicEnum.php").items()
        if isinstance(v, int)
    }
    # Invert: id -> const name already; compare id sets
    lib_thematic_ids = set(lib_thematics.keys())
    api_thematic_ids = set(thematic_source.keys())
    if api_thematic_ids - lib_thematic_ids:
        issues.append(
            issue(
                "error",
                "thematic_ids_missing",
                f"ThematicEnum IDs manquants: {sorted(api_thematic_ids - lib_thematic_ids)}",
            )
        )
    if lib_thematic_ids - api_thematic_ids:
        issues.append(
            issue(
                "warn",
                "thematic_ids_extra",
                f"ThematicEnum IDs absents de l'API: {sorted(lib_thematic_ids - api_thematic_ids)}",
            )
        )

    # Slugs
    thematic_file = (enum_dir / "ThematicEnum.php").read_text(encoding="utf-8")
    lib_slugs = dict(re.findall(r"'([a-z]+)'\s*=>\s*self::(\w+)", thematic_file))
    consts = parse_php_consts(enum_dir / "ThematicEnum.php")
    lib_slug_to_id = {slug: int(consts[const]) for slug, const in lib_slugs.items() if const in consts}
    for tid, slug in thematic_source.items():
        if not slug:
            continue
        if slug not in lib_slug_to_id:
            issues.append(issue("error", "thematic_slug_missing", f"Slug thématique manquant: {slug} (id={tid})"))
        elif lib_slug_to_id[slug] != tid:
            issues.append(
                issue(
                    "error",
                    "thematic_slug_id_mismatch",
                    f"Slug {slug}: lib={lib_slug_to_id[slug]} api={tid}",
                )
            )

    # Heaters — OpenAPI often lags; prefer live + GitHub data
    heater_ids: dict[int, str] = {}
    try:
        heater_ids = parse_chauffage_ids_from_ts(fetch_text(GITHUB_CHAUFFAGE))
        notes.append(f"Chauffage IDs lus depuis GitHub develop ({len(heater_ids)} types)")
    except Exception:
        heater_ids = probe_heater_ids()
        notes.append(f"Chauffage IDs sondés via API ({len(heater_ids)} types)")

    lib_heaters = {
        int(v): k
        for k, v in parse_php_consts(enum_dir / "HeaterEnum.php").items()
        if isinstance(v, int)
    }
    api_heater_ids = set(heater_ids.keys())
    lib_heater_ids = set(lib_heaters.keys())
    if api_heater_ids - lib_heater_ids:
        missing = sorted(api_heater_ids - lib_heater_ids)
        detail = ", ".join(f"{i} ({heater_ids[i]})" for i in missing)
        issues.append(issue("error", "heater_ids_missing", f"HeaterEnum IDs manquants: {detail}"))
    if lib_heater_ids - api_heater_ids:
        issues.append(
            issue(
                "warn",
                "heater_ids_extra",
                f"HeaterEnum IDs absents de l'API: {sorted(lib_heater_ids - api_heater_ids)}",
            )
        )

    # Transport IDs (base modes from OpenAPI; detailed 100-204 may be listed too)
    api_transport_ids = extract_transport_ids_from_openapi(openapi)
    lib_transport = {
        int(v): k
        for k, v in parse_php_consts(enum_dir / "TransportsEnum.php").items()
        if isinstance(v, int)
    }
    # Compare only "base" modes (< 100) — detailed variants are accepted dynamically
    api_base = {i for i in api_transport_ids if i < 100}
    lib_base = {i for i in lib_transport if i < 100}
    if api_base - lib_base:
        issues.append(
            issue(
                "error",
                "transport_ids_missing",
                f"TransportsEnum IDs manquants (<100): {sorted(api_base - lib_base)}",
            )
        )
    if lib_base - api_base:
        issues.append(
            issue(
                "warn",
                "transport_ids_extra",
                f"TransportsEnum IDs absents de l'OpenAPI (<100): {sorted(lib_base - api_base)}",
            )
        )

    # Transport query params: OpenAPI may omit numberOfPassenger (implemented in zod)
    transport_endpoint = endpoints_dir / "TransportEndpoint.php"
    lib_params = transport_query_params(transport_endpoint) - {"language"}
    api_params = openapi_transport_params(openapi) - {"language"}
    # Known live param not always in swagger annotations
    known_live_only = {"numberOfPassenger"}
    missing_in_lib = (api_params | known_live_only) - lib_params
    if missing_in_lib:
        issues.append(
            issue(
                "error",
                "transport_params_missing",
                f"Paramètres transport manquants dans la lib: {sorted(missing_in_lib)}",
            )
        )
    swagger_lag = lib_params - api_params
    if swagger_lag:
        notes.append(
            "Paramètres présents dans la lib mais absents de l'OpenAPI "
            f"(souvent doc en retard): {sorted(swagger_lag)}"
        )

    # Live smoke: transport
    try:
        smoke = fetch_json(f"{API_BASE}/transport?km=100&transports=4&numberOfPassenger=1")
        if not smoke.get("data"):
            issues.append(issue("warn", "transport_smoke_empty", "Smoke /transport a renvoyé data vide"))
        else:
            notes.append("Smoke /transport?km=100&transports=4&numberOfPassenger=1 OK")
    except Exception as exc:  # noqa: BLE001
        issues.append(issue("error", "transport_smoke_failed", f"Smoke /transport échoué: {exc}"))

    errors = [i for i in issues if i["level"] == "error"]
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
