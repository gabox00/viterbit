import { afterEach, describe, expect, it, vi } from 'vitest';
import { render } from 'vitest-browser-react';
import { BACKEND_JOB_POSITION, jsonResponse } from '../../test/fixtures';
import { ApplyForm } from './ApplyForm';

const CV_TEXT = 'Senior PHP developer with eight years of experience building Symfony applications.';

describe('ApplyForm', () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  async function renderFilledForm(onSubmitted = vi.fn()) {
    const screen = await render(<ApplyForm jobPositionHash={BACKEND_JOB_POSITION.hash} onSubmitted={onSubmitted} />);
    await screen.getByLabelText('Full name').fill('Ada Lovelace');
    await screen.getByLabelText('Email').fill('ada@example.com');
    await screen.getByLabelText('CV (plain text)').fill(CV_TEXT);

    return screen;
  }

  it('submits the application with snake_case fields and reports the new hash', async () => {
    const fetchMock = vi.fn(() => Promise.resolve(jsonResponse({ hash: 'new-hash' }, 201)));
    vi.stubGlobal('fetch', fetchMock);
    const onSubmitted = vi.fn();
    const screen = await renderFilledForm(onSubmitted);

    await screen.getByRole('button', { name: 'Submit application' }).click();

    await vi.waitFor(() => {
      expect(onSubmitted).toHaveBeenCalledWith('new-hash');
    });
    expect(fetchMock).toHaveBeenCalledWith(
      '/api/v1/job-applications',
      expect.objectContaining({
        method: 'POST',
        body: JSON.stringify({
          job_position_hash: BACKEND_JOB_POSITION.hash,
          candidate_full_name: 'Ada Lovelace',
          candidate_email: 'ada@example.com',
          candidate_phone: '',
          notes: '',
          cv_text: CV_TEXT,
        }),
      }),
    );
  });

  it('shows the field errors returned by the API', async () => {
    vi.stubGlobal('fetch', vi.fn(() => Promise.resolve(jsonResponse({ error: 'Validation failed.', errors: { candidate_email: 'This value is not a valid email address.' } }, 422))));
    const onSubmitted = vi.fn();
    const screen = await renderFilledForm(onSubmitted);

    await screen.getByRole('button', { name: 'Submit application' }).click();

    await expect.element(screen.getByText('This value is not a valid email address.')).toBeVisible();
    await expect.element(screen.getByLabelText('Email')).toHaveAttribute('aria-invalid', 'true');
    expect(onSubmitted).not.toHaveBeenCalled();
  });

  it('does not submit while required fields are missing', async () => {
    const fetchMock = vi.fn();
    vi.stubGlobal('fetch', fetchMock);
    const screen = await render(<ApplyForm jobPositionHash={BACKEND_JOB_POSITION.hash} onSubmitted={vi.fn()} />);

    await screen.getByRole('button', { name: 'Submit application' }).click();

    expect(fetchMock).not.toHaveBeenCalled();
  });
});
