import { fetchJobApplication, searchJobApplications } from '../api/jobApplicationApi';
import {
  JobApplicationStatusEnum,
  type IJobApplicationDetailDto,
  type IJobApplicationPageDto,
} from '../types/jobApplication';
import { useQuery, type IQueryState } from './useQuery';

export const ENRICHMENT_POLL_INTERVAL_MS = 3000;

const pollWhile = (isPending: boolean): number | null => (isPending ? ENRICHMENT_POLL_INTERVAL_MS : null);

export function useJobApplications(queryString: string): IQueryState<IJobApplicationPageDto> {
  return useQuery(
    `job-applications${queryString}`,
    (signal) => searchJobApplications(queryString, signal),
    (page) => pollWhile(page.items.some((item) => item.status === JobApplicationStatusEnum.Received)),
  );
}

export function useJobApplication(hash: string): IQueryState<IJobApplicationDetailDto> {
  return useQuery(
    `job-application:${hash}`,
    (signal) => fetchJobApplication(hash, signal),
    (jobApplication) => pollWhile(jobApplication.status === JobApplicationStatusEnum.Received),
  );
}
