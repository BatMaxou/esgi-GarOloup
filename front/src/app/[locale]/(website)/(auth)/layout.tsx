import { getLocale } from "next-intl/server";
import { getSession } from "@/utils/server/clients";
import { redirect } from "@/i18n/navigation";
import { paths } from "@/utils/paths";

const AuthLayout = async ({ children }: { children: React.ReactNode }) => {
  const session = await getSession();
  const locale = await getLocale();

  if (session?.user?.token) {
    redirect({ href: paths.game, locale });
  }
  return children;
};

export default AuthLayout;