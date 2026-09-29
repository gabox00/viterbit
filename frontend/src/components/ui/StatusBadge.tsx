import { JobApplicationStatusEnum } from '../../types/jobApplication';
import { classNames } from './classNames';

const STATUS_STYLES: Record<JobApplicationStatusEnum, { label: string; className: string }> = {
  [JobApplicationStatusEnum.Received]: { label: 'Received', className: 'bg-amber-50 text-amber-700 ring-amber-600/20' },
  [JobApplicationStatusEnum.Enriched]: { label: 'Enriched', className: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' },
};

export function StatusBadge({ status }: { status: JobApplicationStatusEnum }) {
  const { label, className } = STATUS_STYLES[status];

  return (
    <span className={classNames('inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset', className)}>
      {label}
    </span>
  );
}
