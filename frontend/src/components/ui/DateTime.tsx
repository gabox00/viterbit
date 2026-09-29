const FORMATTER = new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' });

export function DateTime({ value }: { value: string }) {
  return <time dateTime={value}>{FORMATTER.format(new Date(value))}</time>;
}
