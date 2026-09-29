import type { IJobPositionDto } from '../types/jobPosition';
import { getJson } from './httpClient';

export function fetchJobPositions(signal?: AbortSignal): Promise<IJobPositionDto[]> {
  return getJson<IJobPositionDto[]>('/api/v1/job-positions', signal);
}

export function fetchJobPosition(hash: string, signal?: AbortSignal): Promise<IJobPositionDto> {
  return getJson<IJobPositionDto>(`/api/v1/job-positions/${encodeURIComponent(hash)}`, signal);
}
