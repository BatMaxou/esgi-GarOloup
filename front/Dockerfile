ARG NODE_VERSION=22

FROM node:${NODE_VERSION}-alpine as base

FROM base AS main

WORKDIR /app

COPY package.json .

RUN npm i;

FROM main AS dev

COPY . .

CMD ["npm", "run", "dev"]

FROM main AS builder

COPY . .

CMD ["npm", "run", "build"]

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
