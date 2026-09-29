import { useEffect, useEffectEvent, useState } from 'react';

export interface IQueryState<T> {
  data: T | undefined;
  error: Error | undefined;
  isInitialLoading: boolean;
}

interface IQueryResult<T> {
  data?: T;
  error?: Error;
}

export function useQuery<T>(
  key: string,
  load: (signal: AbortSignal) => Promise<T>,
  refetchIntervalMs?: (data: T) => number | null,
): IQueryState<T> {
  const [result, setResult] = useState<IQueryResult<T>>({});
  const [refetchCount, setRefetchCount] = useState(0);
  const loadData = useEffectEvent(load);
  const nextRefetchDelay = useEffectEvent((data: T) => refetchIntervalMs?.(data) ?? null);

  useEffect(() => {
    const controller = new AbortController();
    let refetchTimeoutId: number | undefined;

    loadData(controller.signal).then(
      (data) => {
        if (controller.signal.aborted) return;
        setResult({ data });

        const delay = nextRefetchDelay(data);
        if (delay !== null) {
          refetchTimeoutId = window.setTimeout(() => {
            setRefetchCount((count) => count + 1);
          }, delay);
        }
      },
      (error: unknown) => {
        if (controller.signal.aborted) return;
        setResult((previous) => ({ ...previous, error: error instanceof Error ? error : new Error(String(error)) }));
      },
    );

    return () => {
      controller.abort();
      window.clearTimeout(refetchTimeoutId);
    };
  }, [key, refetchCount]);

  return {
    data: result.data,
    error: result.error,
    isInitialLoading: result.data === undefined && result.error === undefined,
  };
}
