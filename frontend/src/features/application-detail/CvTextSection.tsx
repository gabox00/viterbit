import { Card } from '../../components/ui/Card';

export function CvTextSection({ cvText }: { cvText: string }) {
  return (
    <Card title="CV (original text)">
      <pre className="max-h-[32rem] overflow-auto font-mono text-sm leading-6 whitespace-pre-wrap text-slate-700">{cvText}</pre>
    </Card>
  );
}
