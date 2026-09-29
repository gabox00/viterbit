interface IPaginationProps {
  page: number;
  perPage: number;
  total: number;
  onPageChange: (page: number) => void;
}

export function Pagination({ page, perPage, total, onPageChange }: IPaginationProps) {
  const lastPage = Math.max(1, Math.ceil(total / perPage));
  if (lastPage === 1) return null;

  return (
    <nav aria-label="Pagination" className="flex items-center justify-between text-sm text-slate-600">
      <span>
        Page {page} of {lastPage}
      </span>
      <div className="flex gap-2">
        <PageButton label="Previous" disabled={page <= 1} onClick={() => { onPageChange(page - 1); }} />
        <PageButton label="Next" disabled={page >= lastPage} onClick={() => { onPageChange(page + 1); }} />
      </div>
    </nav>
  );
}

function PageButton({ label, disabled, onClick }: { label: string; disabled: boolean; onClick: () => void }) {
  return (
    <button
      type="button"
      disabled={disabled}
      onClick={onClick}
      className="rounded-lg px-3 py-1.5 ring-1 ring-slate-300 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
    >
      {label}
    </button>
  );
}
