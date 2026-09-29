import { describe, expect, it } from 'vitest';
import { JobApplicationStatusEnum } from '../types/jobApplication';
import { buildSearchQuery, EMPTY_FILTERS, parseSearchQuery } from './searchQuery';

describe('buildSearchQuery', () => {
  it('returns an empty string when there are no filters', () => {
    expect(buildSearchQuery(EMPTY_FILTERS)).toBe('');
  });

  it('serializes filters with snake_case keys and a trimmed search', () => {
    const query = buildSearchQuery({
      status: JobApplicationStatusEnum.Enriched,
      jobPositionHash: 'job-position-1',
      search: '  ada lovelace ',
      page: 2,
    });

    expect(query).toBe('?status=enriched&job_position_hash=job-position-1&search=ada+lovelace&page=2');
  });

  it('omits blank search and the first page', () => {
    expect(buildSearchQuery({ ...EMPTY_FILTERS, search: '   ', page: 1 })).toBe('');
  });
});

describe('parseSearchQuery', () => {
  it('reads filters back from the URL', () => {
    expect(parseSearchQuery(new URLSearchParams('status=received&job_position_hash=p1&search=ada&page=3'))).toEqual({
      status: JobApplicationStatusEnum.Received,
      jobPositionHash: 'p1',
      search: 'ada',
      page: 3,
    });
  });

  it('falls back to safe defaults for unknown or invalid values', () => {
    expect(parseSearchQuery(new URLSearchParams('status=hired&page=-4'))).toEqual(EMPTY_FILTERS);
    expect(parseSearchQuery(new URLSearchParams('page=abc'))).toEqual(EMPTY_FILTERS);
  });
});
