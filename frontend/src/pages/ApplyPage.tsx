import { useNavigate, useParams } from 'react-router';
import { ErrorMessage } from '../components/ui/ErrorMessage';
import { ApplyForm } from '../features/apply/ApplyForm';
import { JobPositionDescription } from '../features/jobPositions/JobPositionDescription';
import { JobPositionDescriptionSkeleton } from '../features/jobPositions/JobPositionDescriptionSkeleton';
import { useJobPosition } from '../hooks/useJobPositions';

export function ApplyPage() {
  const { jobPositionHash = '' } = useParams();
  const navigate = useNavigate();
  const { data: jobPosition, error, isInitialLoading } = useJobPosition(jobPositionHash);

  if (error) {
    return <ErrorMessage message="This position does not exist or is no longer available." />;
  }

  return (
    <div className="grid gap-6 lg:grid-cols-5">
      <div className="lg:col-span-2">
        {isInitialLoading || !jobPosition ? <JobPositionDescriptionSkeleton /> : <JobPositionDescription jobPosition={jobPosition} />}
      </div>
      <div className="lg:col-span-3">
        <ApplyForm
          jobPositionHash={jobPositionHash}
          onSubmitted={(jobApplicationHash) => void navigate(`/applications/${jobApplicationHash}`, { state: { justSubmitted: true } })}
        />
      </div>
    </div>
  );
}
