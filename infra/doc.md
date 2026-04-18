Master:
docker swarm init --advertise-addr <IP_MASTER> --listen-addr 0.0.0.0:2377

---

Workers:
docker swarm join --token <token> <IP_MASTER>:2377

---

Master:
docker node ls
docker node update --label-add type=database master

printf "examplegaroloupappsecret" | docker secret create garoloup_app_secret -
printf "examplegaroloupdbrootpassword" | docker secret create garoloup_db_root_password -
printf "examplegaroloupdbpassword" | docker secret create garoloup_db_password -
printf "examplegaroloupmercurejwtaaaaaaaaaaaaaaaaaaaaaaaa" | docker secret create garoloup_mercure_jwt -
printf "examplegaroloupbetterauthsecretaaaaaaaaaaaaaaaaaaaaaaaaaaaaa" | docker secret create garoloup_better_auth_secret -

┌────────────────────────┬──────────────────────────────────────────────────┐                                                                       │         Secret         │                      Valeur                      │
├────────────────────────┼──────────────────────────────────────────────────┤
│ FRONT_URL              │ <PUBLIC_ADDR>                                    │
│ FRONT_API_BASE_URL     │ <PUBLIC_ADDR>/api                                │
│ FRONT_SSR_API_BASE_URL │ http://web                                       │
│ FRONT_FTP_BASE_URL     │ <PUBLIC_ADDR>/uploads                            │
│ FRONT_SSR_FTP_BASE_URL │ http://web/uploads                               │
│ FRONT_MERCURE_URL      │ <PUBLIC_ADDR>/.well-known/mercure                │
│ FRONT_SSR_MERCURE_URL  │ http://mercure/.well-known/mercure               │
└────────────────────────┴──────────────────────────────────────────────────┘ 

-----------------------

## Auto scaling
  
### Deploy
docker stack deploy -c compose.scaling.prod.yaml scaling

### Monitoring Grafana
count by (container_label_com_docker_swarm_service_name) (container_last_seen {container_label_com_docker_swarm_service_name=~"garoloup_.*"})

### Charge (2 php -> 3 -> 2)
hey -z 2m -c 200 -disable-keepalive <PUBLIC_ADDR>/api/homepage
