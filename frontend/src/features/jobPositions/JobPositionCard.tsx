import { Link } from 'react-router';
import type { IJobPositionDto } from '../../types/jobPosition';
import { SkillList } from './SkillList';

export function JobPositionCard({ jobPosition }: { jobPosition: IJobPositionDto }) {
  return (
    <article className="flex flex-col rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:ring-indigo-300">
      <h2 className="text-lg font-semibold text-slate-900">{jobPosition.title}</h2>
      <p className="mt-2 line-clamp-3 flex-1 text-sm text-slate-600">{jobPosition.description}</p>
      <SkillList skills={jobPosition.required_skills} className="mt-4" />
      <Link
        to={`/job-positions/${jobPosition.hash}/apply`}
        className="mt-6 self-start text-sm font-semibold text-indigo-600 hover:text-indigo-500"
        aria-label={`Apply to ${jobPosition.title}`}
      >
        Apply now →
      </Link>
    </article>
  );
}
