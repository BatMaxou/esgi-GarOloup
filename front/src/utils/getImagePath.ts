import { ftpUrl } from './tools';

const getImagePath = (path: string) => {
  const base = (ftpUrl || '').replace(/\/+$/, '');
  const normalized = path.replace(/^\/+/, '');
  if (!base) {
    return `/${normalized}`;
  }
  return new URL(normalized, `${base}/`).toString();
};

export { getImagePath };
