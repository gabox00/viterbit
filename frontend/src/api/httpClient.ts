import type { IApiErrorDto } from '../types/api';

export class ApiError extends Error {
  constructor(
    readonly status: number,
    readonly body: IApiErrorDto,
  ) {
    super(body.error);
    this.name = 'ApiError';
  }

  fieldErrors(): Record<string, string> {
    return this.body.errors ?? {};
  }
}

async function request<TResponse>(path: string, init: RequestInit): Promise<TResponse> {
  const response = await fetch(path, {
    ...init,
    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
  });
  const body: unknown = await response.json().catch(() => ({ error: response.statusText }));

  if (!response.ok) {
    throw new ApiError(response.status, body as IApiErrorDto);
  }

  return body as TResponse;
}

export function getJson<TResponse>(path: string, signal?: AbortSignal): Promise<TResponse> {
  return request<TResponse>(path, signal ? { method: 'GET', signal } : { method: 'GET' });
}

export function postJson<TResponse>(path: string, body: unknown): Promise<TResponse> {
  return request<TResponse>(path, { method: 'POST', body: JSON.stringify(body) });
}
