import type { ReactNode } from 'react';

interface IEmptyStateProps {
  title: string;
  children?: ReactNode;
}

export function EmptyState({ title, children }: IEmptyStateProps) {
  return (
    <div className="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
      <p className="text-sm font-semibold text-slate-900">{title}</p>
      {children && <div className="mt-1 text-sm text-slate-500">{children}</div>}
    </div>
  );
}
