import { DateTime } from '../../components/ui/DateTime';
import { StatusBadge } from '../../components/ui/StatusBadge';
import type { IJobApplicationDetailDto } from '../../types/jobApplication';

interface IApplicationHeaderProps {
  application: IJobApplicationDetailDto;
  jobPositionTitle: string;
}

export function ApplicationHeader({ application, jobPositionTitle }: IApplicationHeaderProps) {
  return (
    <header className="flex flex-wrap items-start justify-between gap-4">
      <div className="min-w-0">
        <h1 className="text-2xl font-bold wrap-anywhere text-slate-900">{application.candidate_full_name}</h1>
        <p className="mt-1 text-sm text-slate-500">Applied for {jobPositionTitle}</p>
      </div>
      <dl className="grid grid-cols-[auto_auto] gap-x-4 gap-y-1 text-sm">
        <dt className="text-slate-500">Status</dt>
        <dd>
          <StatusBadge status={application.status} />
        </dd>
        <dt className="text-slate-500">Applied at</dt>
        <dd className="text-slate-700">
          <DateTime value={application.applied_at} />
        </dd>
        <dt className="text-slate-500">Enriched at</dt>
        <dd className="text-slate-700">{application.enriched_at ? <DateTime value={application.enriched_at} /> : '—'}</dd>
      </dl>
    </header>
  );
}
