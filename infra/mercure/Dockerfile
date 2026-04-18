FROM dunglas/mercure:latest

COPY entrypoint.sh /garoloup-entrypoint.sh
RUN chmod +x /garoloup-entrypoint.sh

ENTRYPOINT ["/garoloup-entrypoint.sh"]
CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
