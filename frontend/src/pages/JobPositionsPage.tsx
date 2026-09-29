import { EmptyState } from '../components/ui/EmptyState';
import { ErrorMessage } from '../components/ui/ErrorMessage';
import { JobPositionCard } from '../features/jobPositions/JobPositionCard';
import { JobPositionCardSkeleton } from '../features/jobPositions/JobPositionCardSkeleton';
import { useJobPositions } from '../hooks/useJobPositions';

const SKELETON_CARDS = 4;

export function JobPositionsPage() {
  const { data: jobPositions, error, isInitialLoading } = useJobPositions();

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-bold text-slate-900">Open positions</h1>
        <p className="mt-1 text-sm text-slate-500">Pick a role and send us your CV as plain text.</p>
      </header>
      {error && <ErrorMessage message="We could not load the open positions. Please try again later." />}
      <div className="grid gap-6 md:grid-cols-2">
        {isInitialLoading && Array.from({ length: SKELETON_CARDS }, (_, index) => <JobPositionCardSkeleton key={index} />)}
        {jobPositions?.map((jobPosition) => <JobPositionCard key={jobPosition.hash} jobPosition={jobPosition} />)}
      </div>
      {jobPositions?.length === 0 && <EmptyState title="There are no open positions right now." />}
    </div>
  );
}
