import RoleDetailClient from '@/components/pages/roles/role-detail-client';

type Props = {
  params: Promise<{ roleRef: string }>;
};

const RoleDetailPage = async ({ params }: Props) => {
  const { roleRef } = await params;

  return <RoleDetailClient roleRef={roleRef} />;
};

export default RoleDetailPage;
