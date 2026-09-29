import type {
  IJobApplicationDetailDto,
  IJobApplicationPageDto,
  ISubmitJobApplicationRequestDto,
  ISubmitJobApplicationResponseDto,
} from '../types/jobApplication';
import { getJson, postJson } from './httpClient';

export function searchJobApplications(queryString: string, signal?: AbortSignal): Promise<IJobApplicationPageDto> {
  return getJson<IJobApplicationPageDto>(`/api/v1/job-applications${queryString}`, signal);
}

export function fetchJobApplication(hash: string, signal?: AbortSignal): Promise<IJobApplicationDetailDto> {
  return getJson<IJobApplicationDetailDto>(`/api/v1/job-applications/${encodeURIComponent(hash)}`, signal);
}

export function submitJobApplication(request: ISubmitJobApplicationRequestDto): Promise<ISubmitJobApplicationResponseDto> {
  return postJson<ISubmitJobApplicationResponseDto>('/api/v1/job-applications', request);
}
