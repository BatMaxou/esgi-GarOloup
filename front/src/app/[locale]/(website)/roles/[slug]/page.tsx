import { notFound } from 'next/navigation';

import RoleDetailClient from '@/components/pages/roles/role-detail-client';
import { slugToRoleType } from '@/utils/roleSlug';

type Props = {
  params: Promise<{ slug: string }>;
};

const RoleDetailPage = async ({ params }: Props) => {
  const { slug } = await params;
  const roleType = slugToRoleType(slug);

  if (!roleType) notFound();

  return <RoleDetailClient roleType={roleType} />;
};

export default RoleDetailPage;
