import { JobApplicationStatusEnum, type IJobApplicationFilters } from '../types/jobApplication';

export const EMPTY_FILTERS: IJobApplicationFilters = { status: '', jobPositionHash: '', search: '', page: 1 };

export function buildSearchQuery(filters: IJobApplicationFilters): string {
  const params = new URLSearchParams();
  const search = filters.search.trim();

  if (filters.status) params.set('status', filters.status);
  if (filters.jobPositionHash) params.set('job_position_hash', filters.jobPositionHash);
  if (search) params.set('search', search);
  if (filters.page > 1) params.set('page', String(filters.page));

  const query = params.toString();

  return query ? `?${query}` : '';
}

export function parseSearchQuery(params: URLSearchParams): IJobApplicationFilters {
  const page = Number(params.get('page'));

  return {
    status: toStatusFilter(params.get('status') ?? ''),
    jobPositionHash: params.get('job_position_hash') ?? '',
    search: params.get('search') ?? '',
    page: Number.isInteger(page) && page > 1 ? page : 1,
  };
}

const STATUSES: ReadonlySet<string> = new Set(Object.values(JobApplicationStatusEnum));

function isStatus(value: string): value is JobApplicationStatusEnum {
  return STATUSES.has(value);
}

export function toStatusFilter(value: string): IJobApplicationFilters['status'] {
  return isStatus(value) ? value : '';
}
