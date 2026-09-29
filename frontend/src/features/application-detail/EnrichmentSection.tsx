import { Card } from '../../components/ui/Card';
import { ScoreBadge } from '../../components/ui/ScoreBadge';
import { Skeleton } from '../../components/ui/Skeleton';
import type { IJobApplicationDetailDto } from '../../types/jobApplication';

export function EnrichmentSection({ application }: { application: IJobApplicationDetailDto }) {
  const isEnriched = application.ai_summary !== null && application.ai_score !== null;

  return (
    <Card title="AI enrichment">
      {isEnriched ? (
        <div className="space-y-4">
          <div className="flex items-center gap-2 text-sm text-slate-500">
            Relevance score <ScoreBadge score={application.ai_score} />
          </div>
          <p className="text-sm leading-6 text-slate-700">{application.ai_summary}</p>
        </div>
      ) : (
        <div className="space-y-3" aria-live="polite">
          <p className="text-sm text-slate-500">Analyzing CV…</p>
          <Skeleton className="h-5 w-20" />
          <Skeleton className="h-3 w-full" />
          <Skeleton className="h-3 w-5/6" />
        </div>
      )}
    </Card>
  );
}
