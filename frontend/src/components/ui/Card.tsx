import type { ReactNode } from 'react';
import { classNames } from './classNames';

interface ICardProps {
  title?: string;
  className?: string;
  children: ReactNode;
}

export function Card({ title, className, children }: ICardProps) {
  return (
    <section aria-label={title} className={classNames('min-w-0 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 wrap-anywhere', className)}>
      {title && <h2 className="mb-4 text-sm font-semibold tracking-wide text-slate-500 uppercase">{title}</h2>}
      {children}
    </section>
  );
}
