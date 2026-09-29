export enum JobApplicationStatusEnum {
  Received = 'received',
  Enriched = 'enriched',
}

export interface IJobApplicationSummaryDto {
  hash: string;
  job_position_hash: string;
  candidate_full_name: string;
  candidate_email: string;
  status: JobApplicationStatusEnum;
  ai_score: number | null;
  applied_at: string;
}

export interface IJobApplicationDetailDto extends IJobApplicationSummaryDto {
  candidate_phone: string | null;
  notes: string | null;
  cv_text: string;
  ai_summary: string | null;
  enriched_at: string | null;
}

export interface IJobApplicationPageDto {
  items: IJobApplicationSummaryDto[];
  total: number;
  page: number;
  per_page: number;
}

export interface ISubmitJobApplicationRequestDto {
  job_position_hash: string;
  candidate_full_name: string;
  candidate_email: string;
  candidate_phone: string;
  notes: string;
  cv_text: string;
}

export interface ISubmitJobApplicationResponseDto {
  hash: string;
}

export interface IJobApplicationFilters {
  status: JobApplicationStatusEnum | '';
  jobPositionHash: string;
  search: string;
  page: number;
}
