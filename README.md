# GarOloup

Plateforme de jeu du Loup-Garou en temps réel.

![Coverage (develop)](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/BatMaxou/9c87f49c2923a77cba04c7d1fc59e5d1/raw/garoloup_api_tests_coverage_develop.json)
![Coverage (main)](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/BatMaxou/b7be98f6f48056d6a5ce4b9610f58b49/raw/garoloup_api_tests_coverage_main.json)

---

## Swam

### Images

Les images sont build dans la CI depuis des actions github dispo dans le dossier `.github/workflows`.

- BACK: action `api-image`
- FRONT: action `front-image`
- MERCURE: action `mercure-image`
- AUTOSCALER: action `autoscaler-image`

> [!WARNING]
> Les images sont normalement marquées `publique` [ici](https://github.com/BatMaxou?tab=packages), si vous rencontrez un problème lors du pull, merci de nous contacter.

### Stacks

Pour déployer la stack seulement les fichiers suivants sont nécessaires:

- compose.stack.prod.yaml: stack de l'app
- compose.scaling.prod.yaml: stack pour l'autoscaler et le monitoring
- Makefile.swarm.prod: /!\ A renommer en Makefile pour une utilisation simplifier
- prometheus.yaml: config prometheus

### Installation

#### Nodes

Master:

```bash
docker swarm init --advertise-addr <IP_MASTER> --listen-addr 0.0.0.0:2377
```

---

Workers:

```bash
docker swarm join --token <TOKEN> <IP_MASTER>:2377
```

---

Master:

Verification  des nodes dans le cluster et tag du master pour qu'il soit le node qui héberge les BDD.

```bash
docker node ls
docker node update --label-add type=database master
```

#### Secrets

Création des secrets nécessaires à l'app:

```bash
printf "examplegaroloupappsecret" | docker secret create garoloup_app_secret -
printf "examplegaroloupdbrootpassword" | docker secret create garoloup_db_root_password -
printf "examplegaroloupdbpassword" | docker secret create garoloup_db_password -
printf "examplegaroloupmercurejwtaaaaaaaaaaaaaaaaaaaaaaaa" | docker secret create garoloup_mercure_jwt -
printf "examplegaroloupbetterauthsecretaaaaaaaaaaaaaaaaaaaaaaaaaaaaa" | docker secret create garoloup_better_auth_secret -
```

#### Variables d'environnement

Compléter les variables d'env dans `compose.stack.prod.yaml` (l'env de l'app est mis en `dev` pour pouvoir, si besoin, lancer les fixtures)

- MERCURE_PUBLIC_URL    <PUBLIC_MERCURE_ADDR>/.well-known/mercure
- CORS_ALLOW_ORIGIN     '^https://mon-domaine\.fr$$'
- FRONT_RESET_URL       <PUBLIC_FRONT_ADDR>/reset-password
- BETTER_AUTH_URL       <PUBLIC_FRONT_ADDR>

Pour le front, les variables d'env sont consommées au build, il sera donc nécessaire de build l'image en local en faisant un `make install` et en suivant les étapes du workflow `front-image.yaml` car l'image node publique est branchée sur notre instance de l'app.

Cette étape n'est pas nécessaire si vous authorizez votre instance du front à appeler notre instance du back (demonstration de l'auto scaling ci-après non impactée).

Dans le cas échéans, Les secrets github devront être remplacés par les valeurs suivantes:

- FRONT_URL                 <PUBLIC_FRONT_ADDR>
- FRONT_API_BASE_URL        <PUBLIC_API_ADDR>/api
- FRONT_SSR_API_BASE_URL    http://web
- FRONT_FTP_BASE_URL        <PUBLIC_API_ADDR>/uploads
- FRONT_MERCURE_URL         <PUBLIC_MERCURE_ADDR>/.well-known/mercure
- FRONT_SSR_MERCURE_URL     http://mercure/.well-known/mercure

### Déployer la stack principale

> [!NOTE]
> La commande suivante execute un `docker system prune -f` sur nos serveurs, sur la versions du Makefile rendu, cette commande n'y figure pas car jugée inconfortable pour vous.

```bash
make deploy
```


### Déployer la stack de scaling / monitoring

> [!NOTE]
> La commande suivante execute un `docker system prune -f` sur nos serveurs, sur la versions du Makefile rendu, cette commande n'y figure pas car jugée inconfortable pour vous.

```bash
make deploy-scaling
```

### Reverse Proxy

Le fichier de configuration de nginx est disponible dans le dossier `infra`: `nginx.conf`.

### Schéma d'architecture

![Architecture](https://github.com/BatMaxou/esgi-GarOloup/blob/release/swarm/docs/archi.png?raw=true)

### Réplication

![Réplication](https://github.com/BatMaxou/esgi-GarOloup/blob/release/swarm/docs/replication.png?raw=true)

### Toléance

![Toléance](https://github.com/BatMaxou/esgi-GarOloup/blob/release/swarm/docs/tolerance.png?raw=true)

### Bonus

#### Context

- Mise en place sur un projet réel
- Applicaton déployer sur 3 vps

#### Resource Requests & Limits

- Limites de ressources sur le container php (cas de la montée en charge ci-après)

#### Node Affinity / Taints & Tolerations

- Mercure et la base de données sont sur un node spécifique taggé `database`
- Le monitoring tourne sur master

#### CI/CD

- Mise en place de Github Actions pour Pipelines + Déploiement auto

#### Autoscaling

- Mise en place de l'autoscaling via prometheus + cadvisor + script python

Pour tester la montée en charge sur le php (par exemple) avec `hey`:

```bash
hey -z 2m -c 200 -disable-keepalive <PUBLIC_API_ADDR>/api/homepage
```

Exemple de commande pour suivre les stats dans Grafana:

```
count by (container_label_com_docker_swarm_service_name) (container_last_seen {container_label_com_docker_swarm_service_name=~"garoloup_.*"})
```

![Montée en charge](https://github.com/BatMaxou/esgi-GarOloup/blob/release/swarm/docs/montee-en-charge.png?raw=true)
