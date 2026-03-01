import json
import time
import requests
from pathlib import Path

INPUT_JSON = "pokemon.json"
OUTPUT_JSON = "pokemon_enriched.json"
BASE_URL = "https://pokeapi.co/api/v2"

# ✅ Ultra-Chimères (PokéAPI slugs)
ULTRA_BEASTS = {
    "nihilego", "buzzwole", "pheromosa", "xurkitree", "celesteela",
    "kartana", "guzzlord", "poipole", "naganadel", "stakataka", "blacephalon",
}

# ✅ Paradox (PokéAPI slugs)
PARADOX = {
    # Scarlet
    "great-tusk", "scream-tail", "brute-bonnet", "flutter-mane", "slither-wing",
    "sandy-shocks", "roaring-moon",
    "walking-wake", "raging-bolt", "gouging-fire",
    # Violet
    "iron-treads", "iron-bundle", "iron-hands", "iron-jugulis", "iron-moth",
    "iron-thorns", "iron-valiant",
    "iron-leaves", "iron-crown", "iron-boulder",
}

session = requests.Session()
session.headers.update({"User-Agent": "pokedex-json-enricher/1.0"})

_species_cache = {}

def get_species(name: str) -> dict:
    """
    Récupère pokemon-species/{name} avec cache.
    """
    if name in _species_cache:
        return _species_cache[name]

    url = f"{BASE_URL}/pokemon-species/{name}/"
    r = session.get(url, timeout=25)
    r.raise_for_status()
    data = r.json()
    _species_cache[name] = data
    return data

def main():
    data = json.loads(Path(INPUT_JSON).read_text(encoding="utf-8"))

    total = len(data)
    for i, entry in enumerate(data, start=1):
        # On se base sur base_pokemon (espèce)
        base = entry.get("base_pokemon") or entry.get("form_name_api")
        if not base:
            continue

        # PokéAPI (legendary/mythical)
        try:
            species = get_species(base)
            entry["is_legendary"] = bool(species.get("is_legendary", False))
            entry["is_fabulous"] = bool(species.get("is_mythical", False))  # fabuleux/mythique
        except Exception as e:
            # Si une requête rate, on met false (tu peux logger si besoin)
            entry["is_legendary"] = False
            entry["is_fabulous"] = False

        # Ultra / Paradox via listes
        entry["is_ultra_beast"] = base in ULTRA_BEASTS
        entry["is_paradox"] = base in PARADOX

        if i % 25 == 0 or i == total:
            print(f"✔ {i}/{total} enrichis")

        time.sleep(0.20)  # respecte PokéAPI

    Path(OUTPUT_JSON).write_text(json.dumps(data, ensure_ascii=False, indent=2), encoding="utf-8")
    print("✅ Terminé")
    print(f"📄 Fichier généré : {OUTPUT_JSON}")

if __name__ == "__main__":
    main()
