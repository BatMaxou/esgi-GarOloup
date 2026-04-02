import { ftpUrl } from '@/utils/tools';

const getImagePath = (path: string) => {
  const normalized = path.replace(/^\/+/, '');

  return `${ftpUrl}/${normalized}`;
};

export default getImagePath;
