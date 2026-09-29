import { toStatusFilter } from '../../api/searchQuery';
import { Select, type ISelectOption } from '../../components/ui/Select';
import { TextInput } from '../../components/ui/TextInput';
import { JobApplicationStatusEnum, type IJobApplicationFilters } from '../../types/jobApplication';
import type { IJobPositionDto } from '../../types/jobPosition';

const STATUS_OPTIONS: ISelectOption[] = [
  { value: '', label: 'All statuses' },
  { value: JobApplicationStatusEnum.Received, label: 'Received' },
  { value: JobApplicationStatusEnum.Enriched, label: 'Enriched' },
];

interface IApplicationFiltersProps {
  filters: IJobApplicationFilters;
  jobPositions: IJobPositionDto[];
  onChange: (filters: IJobApplicationFilters) => void;
}

export function ApplicationFilters({ filters, jobPositions, onChange }: IApplicationFiltersProps) {
  const jobPositionOptions: ISelectOption[] = [
    { value: '', label: 'All positions' },
    ...jobPositions.map((jobPosition) => ({ value: jobPosition.hash, label: jobPosition.title })),
  ];

  const change = (partialFilters: Partial<IJobApplicationFilters>) => {
    onChange({ ...filters, ...partialFilters, page: 1 });
  };

  return (
    <div role="search" className="grid gap-3 sm:grid-cols-[1fr_12rem_16rem]">
      <TextInput
        type="search"
        aria-label="Search by candidate name or email"
        placeholder="Search by name or email…"
        value={filters.search}
        onChange={(event) => { change({ search: event.target.value }); }}
      />
      <Select
        aria-label="Filter by status"
        options={STATUS_OPTIONS}
        value={filters.status}
        onChange={(event) => { change({ status: toStatusFilter(event.target.value) }); }}
      />
      <Select
        aria-label="Filter by position"
        options={jobPositionOptions}
        value={filters.jobPositionHash}
        onChange={(event) => { change({ jobPositionHash: event.target.value }); }}
      />
    </div>
  );
}
