import type { IJobApplicationSummaryDto } from '../../types/jobApplication';
import { ApplicationRow } from './ApplicationRow';
import { ApplicationsTableLayout } from './ApplicationsTableLayout';

interface IApplicationsTableProps {
  applications: IJobApplicationSummaryDto[];
  jobPositionTitles: Map<string, string>;
}

export function ApplicationsTable({ applications, jobPositionTitles }: IApplicationsTableProps) {
  return (
    <ApplicationsTableLayout>
      {applications.map((application) => (
        <ApplicationRow
          key={application.hash}
          application={application}
          jobPositionTitle={jobPositionTitles.get(application.job_position_hash) ?? '—'}
        />
      ))}
    </ApplicationsTableLayout>
  );
}
