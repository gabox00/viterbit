import { afterEach, describe, expect, it, vi } from 'vitest';
import { render } from 'vitest-browser-react';
import { userEvent } from 'vitest/browser';
import { createMemoryRouter, RouterProvider } from 'react-router';
import { aJobApplicationSummary, aPage, BACKEND_JOB_POSITION, jsonResponse } from '../test/fixtures';
import { ApplicationsPage } from './ApplicationsPage';

function stubApi(onSearch: (url: string) => Promise<Response>) {
  const fetchMock = vi.fn((url: string) => (url.startsWith('/api/v1/job-positions') ? Promise.resolve(jsonResponse([BACKEND_JOB_POSITION])) : onSearch(url)));
  vi.stubGlobal('fetch', fetchMock);

  return () => fetchMock.mock.calls.map(([url]) => url).filter((url) => url.startsWith('/api/v1/job-applications'));
}

function renderPage() {
  const router = createMemoryRouter([{ path: '/applications', element: <ApplicationsPage /> }], { initialEntries: ['/applications'] });

  return render(<RouterProvider router={router} />);
}

describe('ApplicationsPage', () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('shows skeleton rows while loading and then the applications with their position and score', async () => {
    let respond: (response: Response) => void = () => undefined;
    stubApi(() => new Promise((resolve) => { respond = resolve; }));
    const screen = await renderPage();

    await expect.element(screen.getByTestId('skeleton').first()).toBeInTheDocument();

    respond(jsonResponse(aPage([aJobApplicationSummary()])));

    await expect.element(screen.getByRole('link', { name: 'Ada Lovelace' })).toBeVisible();
    await expect.element(screen.getByRole('cell', { name: BACKEND_JOB_POSITION.title })).toBeVisible();
    await expect.element(screen.getByText('86/100')).toBeVisible();
    expect(screen.getByTestId('skeleton').elements()).toHaveLength(0);
  });

  it('debounces typing so a fast search triggers a single request', async () => {
    const searchCalls = stubApi(() => Promise.resolve(jsonResponse(aPage([]))));
    const screen = await renderPage();
    await expect.element(screen.getByText('No applications match your filters.')).toBeVisible();

    await userEvent.type(screen.getByLabelText('Search by candidate name or email'), 'lovelace');

    await vi.waitFor(() => {
      expect(searchCalls()).toEqual(['/api/v1/job-applications', '/api/v1/job-applications?search=lovelace']);
    });
  });

  it('shows an empty state when nothing matches', async () => {
    stubApi(() => Promise.resolve(jsonResponse(aPage([]))));
    const screen = await renderPage();

    await expect.element(screen.getByText('No applications match your filters.')).toBeVisible();
  });
});
