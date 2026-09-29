import { useMemo, useState } from 'react';
import { useSearchParams } from 'react-router';
import { buildSearchQuery, parseSearchQuery } from '../api/searchQuery';
import { EmptyState } from '../components/ui/EmptyState';
import { ErrorMessage } from '../components/ui/ErrorMessage';
import { Pagination } from '../components/ui/Pagination';
import { ApplicationFilters } from '../features/applications/ApplicationFilters';
import { ApplicationsTable } from '../features/applications/ApplicationsTable';
import { ApplicationsTableSkeleton } from '../features/applications/ApplicationsTableSkeleton';
import { useDebouncedValue } from '../hooks/useDebouncedValue';
import { useJobApplications } from '../hooks/useJobApplications';
import { useJobPositions } from '../hooks/useJobPositions';
import type { IJobApplicationFilters } from '../types/jobApplication';

const FILTERS_DEBOUNCE_MS = 300;

export function ApplicationsPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const [filters, setFilters] = useState(() => parseSearchQuery(searchParams));
  const debouncedQuery = useDebouncedValue(buildSearchQuery(filters), FILTERS_DEBOUNCE_MS);
  const { data: page, error, isInitialLoading } = useJobApplications(debouncedQuery);
  const { data: jobPositions = [] } = useJobPositions();
  const jobPositionTitles = useMemo(() => new Map(jobPositions.map((jobPosition) => [jobPosition.hash, jobPosition.title])), [jobPositions]);

  const changeFilters = (nextFilters: IJobApplicationFilters) => {
    setFilters(nextFilters);
    setSearchParams(new URLSearchParams(buildSearchQuery(nextFilters)), { replace: true });
  };

  return (
    <div className="space-y-6">
      <header className="flex items-end justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Applications</h1>
          <p className="mt-1 text-sm text-slate-500">Newest first. Results update as you type.</p>
        </div>
        {page && <p className="text-sm text-slate-500">{page.total} results</p>}
      </header>
      <ApplicationFilters filters={filters} jobPositions={jobPositions} onChange={changeFilters} />
      {error && <ErrorMessage message="We could not load the applications. Please try again." />}
      {isInitialLoading && <ApplicationsTableSkeleton />}
      {page && page.items.length > 0 && (
        <>
          <ApplicationsTable applications={page.items} jobPositionTitles={jobPositionTitles} />
          <Pagination
            page={page.page}
            perPage={page.per_page}
            total={page.total}
            onPageChange={(nextPage) => { changeFilters({ ...filters, page: nextPage }); }}
          />
        </>
      )}
      {page?.items.length === 0 && <EmptyState title="No applications match your filters.">Try another search or clear the filters.</EmptyState>}
    </div>
  );
}
