import { Link } from 'react-router';
import { DateTime } from '../../components/ui/DateTime';
import { ScoreBadge } from '../../components/ui/ScoreBadge';
import { StatusBadge } from '../../components/ui/StatusBadge';
import type { IJobApplicationSummaryDto } from '../../types/jobApplication';

interface IApplicationRowProps {
  application: IJobApplicationSummaryDto;
  jobPositionTitle: string;
}

export function ApplicationRow({ application, jobPositionTitle }: IApplicationRowProps) {
  return (
    <tr className="hover:bg-slate-50">
      <td className="px-4 py-3">
        <Link to={`/applications/${application.hash}`} className="font-medium text-slate-900 hover:text-indigo-600">
          {application.candidate_full_name}
        </Link>
        <p className="text-xs text-slate-500">{application.candidate_email}</p>
      </td>
      <td className="px-4 py-3 text-sm text-slate-600">{jobPositionTitle}</td>
      <td className="px-4 py-3">
        <StatusBadge status={application.status} />
      </td>
      <td className="px-4 py-3">
        <ScoreBadge score={application.ai_score} />
      </td>
      <td className="px-4 py-3 text-sm whitespace-nowrap text-slate-500">
        <DateTime value={application.applied_at} />
      </td>
    </tr>
  );
}
