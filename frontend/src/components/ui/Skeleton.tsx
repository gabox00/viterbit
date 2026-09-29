import { classNames } from './classNames';

export function Skeleton({ className }: { className?: string }) {
  return <div aria-hidden="true" data-testid="skeleton" className={classNames('animate-pulse rounded-md bg-slate-200', className)} />;
}
