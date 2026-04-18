FROM python:3.12-alpine

RUN apk add --no-cache docker-cli
RUN pip install --no-cache-dir requests

COPY autoscaler.py /autoscaler.py

CMD ["python", "-u", "/autoscaler.py"]
