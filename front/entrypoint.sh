#!/bin/sh
set -eu

export BETTER_AUTH_SECRET=$(cat /run/secrets/garoloup_better_auth_secret)

exec "$@"
