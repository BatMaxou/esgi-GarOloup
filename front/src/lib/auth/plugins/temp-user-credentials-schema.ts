import { z } from 'zod';

export const tempUserCredentialsSchema = z.object({
  username: z.string().min(1),
});

export type TempUserCredentialsType = z.infer<typeof tempUserCredentialsSchema>;
