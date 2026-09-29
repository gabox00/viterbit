import { describe, expect, it } from 'vitest';
import { render } from 'vitest-browser-react';
import { aJobApplicationSummary } from '../../test/fixtures';
import type { IJobApplicationDetailDto } from '../../types/jobApplication';
import { EnrichmentSection } from './EnrichmentSection';

const UNBREAKABLE_SUMMARY = 'asdasdsa'.repeat(40);

describe('EnrichmentSection', () => {
  it('wraps a summary without spaces inside its box instead of overflowing it', async () => {
    const application: IJobApplicationDetailDto = {
      ...aJobApplicationSummary(),
      candidate_phone: null,
      notes: null,
      cv_text: UNBREAKABLE_SUMMARY,
      ai_summary: UNBREAKABLE_SUMMARY,
      enriched_at: '2026-09-28T10:00:05+00:00',
    };

    const screen = await render(
      <div style={{ width: '320px' }}>
        <EnrichmentSection application={application} />
      </div>,
    );

    const box = screen.getByRole('region', { name: 'AI enrichment' }).element();
    expect(box.scrollWidth).toBeLessThanOrEqual(box.clientWidth);
  });
});
