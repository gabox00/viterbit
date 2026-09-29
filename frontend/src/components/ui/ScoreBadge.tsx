import { classNames } from './classNames';

const HIGH_SCORE = 70;
const MEDIUM_SCORE = 40;

function scoreClassName(score: number): string {
  if (score >= HIGH_SCORE) return 'bg-emerald-100 text-emerald-800';
  if (score >= MEDIUM_SCORE) return 'bg-amber-100 text-amber-800';

  return 'bg-rose-100 text-rose-800';
}

export function ScoreBadge({ score }: { score: number | null }) {
  if (score === null) {
    return <span className="text-xs text-slate-400 italic">Pending</span>;
  }

  return (
    <span
      aria-label={`AI score ${String(score)} out of 100`}
      className={classNames('inline-flex min-w-12 justify-center rounded-md px-2 py-0.5 text-xs font-semibold tabular-nums', scoreClassName(score))}
    >
      {score}/100
    </span>
  );
}
