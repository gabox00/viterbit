import { fetchJobPosition, fetchJobPositions } from '../api/jobPositionApi';
import type { IJobPositionDto } from '../types/jobPosition';
import { useQuery, type IQueryState } from './useQuery';

export function useJobPositions(): IQueryState<IJobPositionDto[]> {
  return useQuery('positions', fetchJobPositions);
}

export function useJobPosition(hash: string): IQueryState<IJobPositionDto> {
  return useQuery(`jobPosition:${hash}`, (signal) => fetchJobPosition(hash, signal));
}
