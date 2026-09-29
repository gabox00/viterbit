import type { ReactNode } from 'react';
import { Card } from '../../components/ui/Card';
import type { IJobApplicationDetailDto } from '../../types/jobApplication';

export function CandidateSection({ application }: { application: IJobApplicationDetailDto }) {
  return (
    <Card title="Candidate">
      <dl className="space-y-3 text-sm">
        <DetailItem label="Email" value={<a href={`mailto:${application.candidate_email}`} className="text-indigo-600 hover:underline">{application.candidate_email}</a>} />
        <DetailItem label="Phone" value={application.candidate_phone ?? '—'} />
        <DetailItem label="Notes" value={application.notes ?? '—'} />
      </dl>
    </Card>
  );
}

function DetailItem({ label, value }: { label: string; value: ReactNode }) {
  return (
    <div>
      <dt className="text-slate-500">{label}</dt>
      <dd className="mt-0.5 whitespace-pre-line text-slate-900">{value}</dd>
    </div>
  );
}
