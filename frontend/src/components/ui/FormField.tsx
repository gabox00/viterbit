import type { ReactNode } from 'react';

interface IFormFieldProps {
  id: string;
  label: string;
  error?: string | undefined;
  hint?: string;
  children: ReactNode;
}

export function FormField({ id, label, error, hint, children }: IFormFieldProps) {
  return (
    <div className="space-y-1.5">
      <label htmlFor={id} className="block text-sm font-medium text-slate-700">
        {label}
      </label>
      {children}
      {error ? (
        <p id={`${id}-error`} className="text-xs text-rose-600">
          {error}
        </p>
      ) : (
        hint && <p className="text-xs text-slate-500">{hint}</p>
      )}
    </div>
  );
}
