import { classNames } from '../../components/ui/classNames';

export function SkillList({ skills, className }: { skills: string[]; className?: string }) {
  return (
    <ul aria-label="Required skills" className={classNames('flex flex-wrap gap-1.5', className)}>
      {skills.map((skill) => (
        <li key={skill} className="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">
          {skill}
        </li>
      ))}
    </ul>
  );
}
