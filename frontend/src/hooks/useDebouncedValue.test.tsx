import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { renderHook } from 'vitest-browser-react';
import { useDebouncedValue } from './useDebouncedValue';

describe('useDebouncedValue', () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  it('only emits the last value once the delay has elapsed without changes', async () => {
    const { result, rerender, act } = await renderHook(
      (props?: { value: string }) => useDebouncedValue(props?.value ?? '', 300),
      { initialProps: { value: 'a' } },
    );

    await rerender({ value: 'ad' });
    await act(() => { vi.advanceTimersByTime(200); });
    await rerender({ value: 'ada' });
    await act(() => { vi.advanceTimersByTime(200); });

    expect(result.current).toBe('a');

    await act(() => { vi.advanceTimersByTime(100); });

    expect(result.current).toBe('ada');
  });
});
