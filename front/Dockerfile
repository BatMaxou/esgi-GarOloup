ARG NODE_VERSION=22

# Base image
FROM node:${NODE_VERSION}-alpine AS base

ENV PNPM_HOME="/pnpm"
ENV PATH="$PNPM_HOME:$PATH"

RUN corepack enable;

# Main image
FROM base AS main

WORKDIR /app

COPY package.json pnpm-lock.yaml ./
RUN pnpm fetch;
RUN pnpm install --frozen-lockfile --ignore-scripts=false;

# Dev image
FROM main AS dev

COPY . .

CMD ["pnpm", "run", "dev"]

# Builder image
FROM main AS builder

ARG NEXT_PUBLIC_URL
ARG NEXT_PUBLIC_API_BASE_URL
ARG NEXT_PUBLIC_SSR_API_BASE_URL
ARG NEXT_PUBLIC_FTP_BASE_URL
ARG NEXT_PUBLIC_SSR_FTP_BASE_URL
ARG NEXT_PUBLIC_MERCURE_URL
ARG NEXT_PUBLIC_SSR_MERCURE_URL

ENV NEXT_PUBLIC_URL=$NEXT_PUBLIC_URL
ENV NEXT_PUBLIC_API_BASE_URL=$NEXT_PUBLIC_API_BASE_URL
ENV NEXT_PUBLIC_SSR_API_BASE_URL=$NEXT_PUBLIC_SSR_API_BASE_URL
ENV NEXT_PUBLIC_FTP_BASE_URL=$NEXT_PUBLIC_FTP_BASE_URL
ENV NEXT_PUBLIC_SSR_FTP_BASE_URL=$NEXT_PUBLIC_SSR_FTP_BASE_URL
ENV NEXT_PUBLIC_MERCURE_URL=$NEXT_PUBLIC_MERCURE_URL
ENV NEXT_PUBLIC_SSR_MERCURE_URL=$NEXT_PUBLIC_SSR_MERCURE_URL

COPY . .

RUN pnpm run build;

# Production image
FROM base AS prod

WORKDIR /app

RUN addgroup --system --gid 1001 nodejs;
RUN adduser --system --uid 1001 nextjs;
USER nextjs

COPY --from=builder /app/public ./public
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

CMD ["node", "server.js"]
