# GarOloup

Plateforme de jeu du Loup-Garou en temps réel.

![Coverage (develop)](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/BatMaxou/9c87f49c2923a77cba04c7d1fc59e5d1/raw/garoloup_api_tests_coverage_develop.json)
![Coverage (main)](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/BatMaxou/b7be98f6f48056d6a5ce4b9610f58b49/raw/garoloup_api_tests_coverage_main.json)

---

## Development - Installation

### Prerequisites

#### API

Copy `api/.env.example` to `api/.env` and edit it if needed.
```bash
cp api/.env.example api/.env.local
```

#### Front

Copy `front/.env.example` to `front.env` and edit it if needed.
```bash
cp front/.env.example front/.env.local
```

#### Docker services

Copy `compose.override.example.yaml` to `compose.override.yaml` and edit it if needed.
```bash
cp compose.override.example.yaml compose.override.yaml
```

Front : port 3000

### Installation

```bash
make install
```

### Fixtures

```bash
make fixtures
```

### Fixtures

compte admin : `admin@garoloup.com`

compte test : `test@garoloup.com`

### Yaak

Yaak collection is available in the `.yaak` folder to test the API endpoints.

---

## Global game test scenario

### Composition (11 players)

- 3 villagers
- 1 werewolf
- 1 seer
- 1 witch
- 1 wild child
- 1 hunter
- 1 infect father
- 1 cupid
- 1 assassin

### Setup

- wild child model : villager 3
- cupid couple : witch + hunter

### Night 1

- werewolfs kill assassin (immune)
- witch pass

### Vote 1

- vote villager 1

### Night 2

- werewolfs kill witch
- infect father infect witch
- assassin kill infect father

### Vote 2

- vote werewolf

### Night 3

- werewolfs kill seer
- witch kill villager 3
- assassin kill seer

### Vote 3

- no vote

### Night 4

- werewolfs vote (1 vote cupid, 1 vote hunter)
- witch save (cupid or hunter)
- assassin kill cupid

### Vote 4

- vote hunter

### Night 5

- werewolfs kill assassin (immune)
- assassin kill wild child

### Result

- assassin win
