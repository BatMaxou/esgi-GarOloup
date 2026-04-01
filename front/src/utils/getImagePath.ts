const getImagePath = (path: string) => {
  const base = (process.env.NEXT_PUBLIC_FTP_BASE_URL ?? '').replace(/\/+$/, '');
  const normalized = path.replace(/^\/+/, '');
  return `${base}/${normalized}`;
};

export default getImagePath;