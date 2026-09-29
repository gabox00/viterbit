import { Link, useLocation, useParams } from 'react-router';
import { ErrorMessage } from '../components/ui/ErrorMessage';
import { ApplicationDetailSkeleton } from '../features/application-detail/ApplicationDetailSkeleton';
import { ApplicationHeader } from '../features/application-detail/ApplicationHeader';
import { CandidateSection } from '../features/application-detail/CandidateSection';
import { CvTextSection } from '../features/application-detail/CvTextSection';
import { EnrichmentSection } from '../features/application-detail/EnrichmentSection';
import { useJobApplication } from '../hooks/useJobApplications';
import { useJobPositions } from '../hooks/useJobPositions';
import { JobApplicationStatusEnum } from '../types/jobApplication';

export function ApplicationDetailPage() {
  const { jobApplicationHash = '' } = useParams();
  const location = useLocation();
  const justSubmitted = (location.state as { justSubmitted?: boolean } | null)?.justSubmitted === true;
  const { data: application, error } = useJobApplication(jobApplicationHash);
  const { data: jobPositions = [] } = useJobPositions();

  if (error) {
    return <ErrorMessage message="This application does not exist." />;
  }

  if (!application) {
    return <ApplicationDetailSkeleton />;
  }

  const jobPositionTitle = jobPositions.find((jobPosition) => jobPosition.hash === application.job_position_hash)?.title ?? 'a position';

  return (
    <div className="space-y-6">
      <Link to="/applications" className="text-sm font-medium text-indigo-600 hover:text-indigo-500">
        ← All applications
      </Link>
      {justSubmitted && application.status === JobApplicationStatusEnum.Received && (
        <p role="status" className="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
          Application submitted. Our AI is reviewing the CV; results appear here automatically.
        </p>
      )}
      <ApplicationHeader application={application} jobPositionTitle={jobPositionTitle} />
      <div className="grid gap-6 lg:grid-cols-3">
        <div className="space-y-6">
          <EnrichmentSection application={application} />
          <CandidateSection application={application} />
        </div>
        <div className="lg:col-span-2">
          <CvTextSection cvText={application.cv_text} />
        </div>
      </div>
    </div>
  );
}
