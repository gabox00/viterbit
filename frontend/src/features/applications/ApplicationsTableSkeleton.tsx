import { Skeleton } from '../../components/ui/Skeleton';
import { ApplicationsTableLayout } from './ApplicationsTableLayout';

const ROWS = 5;

export function ApplicationsTableSkeleton() {
  return (
    <ApplicationsTableLayout>
      {Array.from({ length: ROWS }, (_, index) => (
        <tr key={index}>
          <td className="px-4 py-3">
            <Skeleton className="h-4 w-36" />
            <Skeleton className="mt-1.5 h-3 w-44" />
          </td>
          <td className="px-4 py-3">
            <Skeleton className="h-4 w-40" />
          </td>
          <td className="px-4 py-3">
            <Skeleton className="h-5 w-16 rounded-full" />
          </td>
          <td className="px-4 py-3">
            <Skeleton className="h-5 w-12" />
          </td>
          <td className="px-4 py-3">
            <Skeleton className="h-4 w-28" />
          </td>
        </tr>
      ))}
    </ApplicationsTableLayout>
  );
}
