#!/bin/sh
set -eu

MERCURE_JWT=$(cat /run/secrets/garoloup_mercure_jwt)
export MERCURE_PUBLISHER_JWT_KEY="$MERCURE_JWT"
export MERCURE_SUBSCRIBER_JWT_KEY="$MERCURE_JWT"

exec "$@"
