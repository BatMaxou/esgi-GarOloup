import { ApiClient, BasicActionResponse } from "@/lib/api/ApiClient";
import { ApiClientError } from "@/lib/api/ApiClientError";
import { apiPaths } from "@/lib/api/paths";

export interface RegisterData {
  email: string;
  username: string;
  password: string;
}

export interface ResetPasswordData {
  token: string;
  password: string;
}

export interface ForgotPasswordResponse {
  treated: boolean;
}

export interface ResetPasswordResponse {
  treated: boolean;
}

export class UserResource {
  constructor(private apiClient: ApiClient) {}

  public async register(data: RegisterData): Promise<BasicActionResponse | ApiClientError> {
    return this.apiClient.post<BasicActionResponse>(apiPaths.user.register, data);
  }

  public async forgotPassword(email: string): Promise<ForgotPasswordResponse | ApiClientError> {
    return this.apiClient.post<ForgotPasswordResponse>(apiPaths.user.forgotPassword, { email });
  }

  public async resetPassword(data: ResetPasswordData): Promise<ResetPasswordResponse | ApiClientError> {
    return this.apiClient.post<ResetPasswordResponse>(apiPaths.user.resetPassword, data);
  }
}

