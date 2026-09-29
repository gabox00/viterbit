import { afterEach, describe, expect, it, vi } from 'vitest';
import { ApiError, getJson, postJson } from './httpClient';

function stubFetch(status: number, body: unknown) {
  const fetchMock = vi.fn(() => Promise.resolve(new Response(JSON.stringify(body), { status })));
  vi.stubGlobal('fetch', fetchMock);

  return fetchMock;
}

describe('httpClient', () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('returns the parsed JSON body on success', async () => {
    stubFetch(200, { status: 'ok' });

    await expect(getJson('/api/health')).resolves.toEqual({ status: 'ok' });
  });

  it('sends JSON bodies on POST', async () => {
    const fetchMock = stubFetch(201, { id: 'abc' });

    await postJson('/api/v1/job-applications', { candidate_full_name: 'Ada' });

    expect(fetchMock).toHaveBeenCalledWith(
      '/api/v1/job-applications',
      expect.objectContaining({ method: 'POST', body: '{"candidate_full_name":"Ada"}' }),
    );
  });

  it('throws an ApiError exposing field errors on failure', async () => {
    stubFetch(422, { error: 'Validation failed.', errors: { candidate_email: 'Invalid email.' } });

    const error = await postJson('/api/v1/job-applications', {}).catch((caught: unknown) => caught);

    expect(error).toBeInstanceOf(ApiError);
    expect((error as ApiError).status).toBe(422);
    expect((error as ApiError).fieldErrors()).toEqual({ candidate_email: 'Invalid email.' });
  });

  it('passes the abort signal to fetch', async () => {
    const fetchMock = stubFetch(200, []);
    const controller = new AbortController();

    await getJson('/api/v1/job-positions', controller.signal);

    expect(fetchMock).toHaveBeenCalledWith('/api/v1/job-positions', expect.objectContaining({ signal: controller.signal }));
  });
});
