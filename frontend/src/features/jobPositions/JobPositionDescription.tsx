import { Card } from '../../components/ui/Card';
import type { IJobPositionDto } from '../../types/jobPosition';
import { SkillList } from './SkillList';

export function JobPositionDescription({ jobPosition }: { jobPosition: IJobPositionDto }) {
  return (
    <Card>
      <p className="text-xs font-semibold tracking-wide text-indigo-600 uppercase">Open position</p>
      <h1 className="mt-1 text-2xl font-bold text-slate-900">{jobPosition.title}</h1>
      <p className="mt-4 text-sm leading-6 text-slate-600">{jobPosition.description}</p>
      <h2 className="mt-6 mb-2 text-sm font-semibold text-slate-900">What we are looking for</h2>
      <SkillList skills={jobPosition.required_skills} />
    </Card>
  );
}
