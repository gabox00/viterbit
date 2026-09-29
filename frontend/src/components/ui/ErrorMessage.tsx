export function ErrorMessage({ message }: { message: string }) {
  return (
    <div role="alert" className="rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
      {message}
    </div>
  );
}
