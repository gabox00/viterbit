import { describe, expect, it } from 'vitest';
import { render } from 'vitest-browser-react';
import { JobApplicationStatusEnum } from '../../types/jobApplication';
import { ScoreBadge } from './ScoreBadge';
import { StatusBadge } from './StatusBadge';

describe('StatusBadge', () => {
  it('shows a human readable status', async () => {
    const screen = await render(<StatusBadge status={JobApplicationStatusEnum.Received} />);

    await expect.element(screen.getByText('Received')).toBeVisible();
  });
});

describe('ScoreBadge', () => {
  it('shows the score out of 100', async () => {
    const screen = await render(<ScoreBadge score={86} />);

    await expect.element(screen.getByLabelText('AI score 86 out of 100')).toHaveTextContent('86/100');
  });

  it('shows pending while there is no score yet', async () => {
    const screen = await render(<ScoreBadge score={null} />);

    await expect.element(screen.getByText('Pending')).toBeVisible();
  });
});
