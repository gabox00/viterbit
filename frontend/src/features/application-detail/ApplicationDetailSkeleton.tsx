import { Card } from '../../components/ui/Card';
import { Skeleton } from '../../components/ui/Skeleton';

export function ApplicationDetailSkeleton() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between">
        <div>
          <Skeleton className="h-7 w-56" />
          <Skeleton className="mt-2 h-4 w-40" />
        </div>
        <Skeleton className="h-16 w-48" />
      </div>
      <div className="grid gap-6 lg:grid-cols-3">
        <div className="space-y-6">
          <Card>
            <Skeleton className="h-4 w-24" />
            <Skeleton className="mt-4 h-3 w-full" />
            <Skeleton className="mt-2 h-3 w-2/3" />
          </Card>
          <Card>
            <Skeleton className="h-4 w-28" />
            <Skeleton className="mt-4 h-3 w-full" />
            <Skeleton className="mt-2 h-3 w-5/6" />
          </Card>
        </div>
        <Card className="lg:col-span-2">
          <Skeleton className="h-4 w-32" />
          {Array.from({ length: 8 }, (_, index) => (
            <Skeleton key={index} className="mt-3 h-3 w-full" />
          ))}
        </Card>
      </div>
    </div>
  );
}
