import { describe, expect, it, vi } from 'vitest';
import { render } from 'vitest-browser-react';
import { EMPTY_FILTERS } from '../../api/searchQuery';
import { BACKEND_JOB_POSITION } from '../../test/fixtures';
import { JobApplicationStatusEnum, type IJobApplicationFilters } from '../../types/jobApplication';
import { ApplicationFilters } from './ApplicationFilters';

describe('ApplicationFilters', () => {
  it('reports the selected status and position and goes back to the first page', async () => {
    const onChange = vi.fn();
    const screen = await render(
      <ApplicationFilters filters={{ ...EMPTY_FILTERS, page: 3 }} jobPositions={[BACKEND_JOB_POSITION]} onChange={onChange} />,
    );

    await screen.getByLabelText('Filter by status').selectOptions('Enriched');
    await screen.getByLabelText('Filter by position').selectOptions(BACKEND_JOB_POSITION.title);

    const expectedStatusFilters: IJobApplicationFilters = { ...EMPTY_FILTERS, status: JobApplicationStatusEnum.Enriched };
    expect(onChange).toHaveBeenNthCalledWith(1, expectedStatusFilters);
    expect(onChange).toHaveBeenNthCalledWith(2, { ...EMPTY_FILTERS, jobPositionHash: BACKEND_JOB_POSITION.hash, page: 1 });
  });

  it('reports the search text', async () => {
    const onChange = vi.fn();
    const screen = await render(<ApplicationFilters filters={EMPTY_FILTERS} jobPositions={[]} onChange={onChange} />);

    await screen.getByLabelText('Search by candidate name or email').fill('ada');

    expect(onChange).toHaveBeenLastCalledWith({ ...EMPTY_FILTERS, search: 'ada' });
  });
});
