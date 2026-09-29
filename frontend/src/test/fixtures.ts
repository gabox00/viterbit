import { JobApplicationStatusEnum, type IJobApplicationPageDto, type IJobApplicationSummaryDto } from '../types/jobApplication';
import type { IJobPositionDto } from '../types/jobPosition';

export const BACKEND_JOB_POSITION: IJobPositionDto = {
  hash: '0199a0e0-0000-7000-8000-000000000001',
  title: 'Senior Backend Engineer (PHP)',
  description: 'Design and evolve our modular monolith.',
  required_skills: ['PHP', 'Symfony'],
};

export function aJobApplicationSummary(overrides: Partial<IJobApplicationSummaryDto> = {}): IJobApplicationSummaryDto {
  return {
    hash: '0199a0e0-1111-7000-8000-000000000001',
    job_position_hash: BACKEND_JOB_POSITION.hash,
    candidate_full_name: 'Ada Lovelace',
    candidate_email: 'ada@example.com',
    status: JobApplicationStatusEnum.Enriched,
    ai_score: 86,
    applied_at: '2026-09-28T10:00:00+00:00',
    ...overrides,
  };
}

export function aPage(items: IJobApplicationSummaryDto[]): IJobApplicationPageDto {
  return { items, total: items.length, page: 1, per_page: 20 };
}

export function jsonResponse(body: unknown, status = 200): Response {
  return new Response(JSON.stringify(body), { status, headers: { 'Content-Type': 'application/json' } });
}
