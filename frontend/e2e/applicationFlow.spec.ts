import { expect, test } from '@playwright/test';

const CV_TEXT = [
  'Senior PHP developer with eight years of experience building Symfony applications.',
  'Designed bounded contexts with DDD, integrated RabbitMQ workers and shipped everything with Docker.',
  'Comfortable with SQL tuning and PHPUnit test suites.',
].join('\n');

test('a candidate applies, the CV gets enriched asynchronously and recruiters can find it', async ({ page }) => {
  const candidateName = `E2E Candidate ${String(Date.now())}`;

  await page.goto('/');
  await page.getByRole('link', { name: 'Apply to Senior Backend Engineer (PHP)' }).click();
  await expect(page.getByRole('heading', { name: 'Senior Backend Engineer (PHP)' })).toBeVisible();

  await page.getByLabel('Full name').fill(candidateName);
  await page.getByLabel('Email').fill('e2e.candidate@example.com');
  await page.getByLabel('Phone (optional)').fill('+34 600 000 000');
  await page.getByLabel('Notes (optional)').fill('Available in two weeks.');
  await page.getByLabel('CV (plain text)').fill(CV_TEXT);
  await page.getByRole('button', { name: 'Submit application' }).click();

  await expect(page.getByRole('heading', { name: candidateName })).toBeVisible();
  await expect(page.getByText('Analyzing CV…')).toBeVisible();
  await expect(page.getByLabel('AI score 100 out of 100')).toBeVisible({ timeout: 15_000 });
  await expect(page.getByRole('region', { name: 'AI enrichment' }).getByText(/^Senior PHP developer with eight years/)).toBeVisible();

  await page.getByRole('link', { name: 'Applications', exact: true }).click();
  await page.getByLabel('Search by candidate name or email').fill(candidateName);
  const row = page.getByRole('row', { name: new RegExp(candidateName) });
  await expect(row).toBeVisible();
  await expect(row.getByText('100/100')).toBeVisible();

  await page.getByLabel('Filter by status').selectOption('received');
  await expect(page.getByText('No applications match your filters.')).toBeVisible();

  await page.getByLabel('Filter by status').selectOption('enriched');
  await page.getByLabel('Filter by position').selectOption({ label: 'Senior Backend Engineer (PHP)' });
  await expect(row).toBeVisible();

  await row.getByRole('link', { name: candidateName }).click();
  await expect(page.getByText('Available in two weeks.')).toBeVisible();
  await expect(page.getByRole('region', { name: 'CV (original text)' })).toContainText('Comfortable with SQL tuning and PHPUnit test suites.');
  await expect(page.getByText('Enriched', { exact: true })).toBeVisible();
});

test('newest applications are listed first', async ({ page, request }) => {
  const suffix = String(Date.now());
  for (const name of [`Older ${suffix}`, `Newer ${suffix}`]) {
    const response = await request.post('/api/v1/job-applications', {
      data: {
        job_position_hash: '0199a0e0-0000-7000-8000-000000000002',
        candidate_full_name: name,
        candidate_email: 'order@example.com',
        cv_text: 'Frontend engineer building React and TypeScript interfaces with Tailwind and Vitest.',
      },
    });
    expect(response.status()).toBe(201);
  }

  await page.goto(`/applications?search=${suffix}`);

  const names = page.getByRole('row').getByRole('link');
  await expect(names).toHaveText([`Newer ${suffix}`, `Older ${suffix}`]);
});
