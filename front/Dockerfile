ARG NODE_VERSION=22

# Base image
FROM node:${NODE_VERSION}-alpine AS base

# Main image
FROM base AS main

WORKDIR /app

COPY package.json .

RUN npm i;

# Dev image
FROM main AS dev

WORKDIR /app

COPY . .

COPY --from=main /app/node_modules /app/node_modules

CMD ["npm", "run", "dev"]

# Builder image
FROM main AS builder

WORKDIR /app

COPY . .

RUN npm run build;

# Production image
FROM base AS prod

WORKDIR /app

RUN addgroup --system --gid 1001 nodejs;
RUN adduser --system --uid 1001 nextjs;
USER nextjs

COPY --from=builder /app/public ./public

# Automatically leverage output traces to reduce image size
# https://nextjs.org/docs/advanced-features/output-file-tracing
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

CMD ["node", "server.js"]
