import { Card } from '../../components/ui/Card';
import { Skeleton } from '../../components/ui/Skeleton';

export function JobPositionDescriptionSkeleton() {
  return (
    <Card>
      <Skeleton className="h-3 w-24" />
      <Skeleton className="mt-3 h-7 w-3/4" />
      <Skeleton className="mt-6 h-3 w-full" />
      <Skeleton className="mt-2 h-3 w-full" />
      <Skeleton className="mt-2 h-3 w-2/3" />
      <div className="mt-6 flex gap-1.5">
        <Skeleton className="h-5 w-14" />
        <Skeleton className="h-5 w-20" />
        <Skeleton className="h-5 w-12" />
      </div>
    </Card>
  );
}
