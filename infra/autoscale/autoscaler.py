import os, time, requests, subprocess, logging

logging.basicConfig(level=logging.INFO, format='%(asctime)s %(message)s')

PROMETHEUS_URL = os.getenv("PROMETHEUS_URL", "http://prometheus:9090")
CPU_UP = float(os.getenv("CPU_UP_THRESHOLD", "0.85"))
CPU_DOWN = float(os.getenv("CPU_DOWN_THRESHOLD", "0.25"))
MIN_REPLICAS = int(os.getenv("MIN_REPLICAS", "2"))
MAX_REPLICAS = int(os.getenv("MAX_REPLICAS", "6"))
INTERVAL = int(os.getenv("CHECK_INTERVAL", "60"))
COOLDOWN = int(os.getenv("SCALE_COOLDOWN", "120"))

RATE_WINDOW = os.getenv("RATE_WINDOW", "30s")
QUERY = f'sum by (container_label_com_docker_swarm_service_name) (rate(container_cpu_usage_seconds_total[{RATE_WINDOW}]))'

last_scale = {}

logging.info(f"Autoscaler started — PROMETHEUS_URL={PROMETHEUS_URL} INTERVAL={INTERVAL}s COOLDOWN={COOLDOWN}s")
logging.info(f"Thresholds: UP={CPU_UP} DOWN={CPU_DOWN} MIN={MIN_REPLICAS} MAX={MAX_REPLICAS}")

def get_service_replicas(service_name):
    r = subprocess.run(["docker", "service", "inspect", "--format", "{{.Spec.Mode.Replicated.Replicas}}", service_name], capture_output=True, text=True)
    try:
        return int(r.stdout.strip())
    except:
        return None

def scale(service_name, replicas):
    logging.info(f"Scaling {service_name} to {replicas} replicas")
    subprocess.run(["docker", "service", "scale", f"{service_name}={replicas}"])
    last_scale[service_name] = time.time()

def check_autoscale_label(service_name):
    r = subprocess.run(["docker", "service", "inspect", "--format", "{{index .Spec.Labels \"swarm.autoscale\"}}", service_name], capture_output=True, text=True)
    return r.stdout.strip() == "true"

while True:
    try:
        res = requests.get(f"{PROMETHEUS_URL}/api/v1/query", params={"query": QUERY}).json()
        results = res.get("data", {}).get("result", [])
        logging.info(f"Prometheus returned {len(results)} series")
        for item in results:
            svc = item["metric"].get("container_label_com_docker_swarm_service_name")
            cpu_total = float(item["value"][1])
            if not svc:
                continue
            if not check_autoscale_label(svc):
                continue
            replicas = get_service_replicas(svc)
            if replicas is None or replicas == 0:
                continue
            cpu_per_replica = cpu_total / replicas
            logging.info(f"{svc}: CPU_total={cpu_total:.3f} CPU/replica={cpu_per_replica:.3f} replicas={replicas}")

            if time.time() - last_scale.get(svc, 0) < COOLDOWN:
                continue

            if cpu_per_replica > CPU_UP and replicas < MAX_REPLICAS:
                scale(svc, replicas + 1)
            elif cpu_per_replica < CPU_DOWN and replicas > MIN_REPLICAS:
                scale(svc, replicas - 1)
    except Exception as e:
        logging.error(f"Error: {e}")
    time.sleep(INTERVAL)
