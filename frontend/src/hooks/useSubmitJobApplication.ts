import { useState } from 'react';
import { submitJobApplication } from '../api/jobApplicationApi';
import type { ISubmitJobApplicationRequestDto } from '../types/jobApplication';

interface ISubmitJobApplicationState {
  submit: (request: ISubmitJobApplicationRequestDto) => Promise<string | null>;
  isSubmitting: boolean;
  error: Error | undefined;
}

export function useSubmitJobApplication(): ISubmitJobApplicationState {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState<Error>();

  const submit = async (request: ISubmitJobApplicationRequestDto): Promise<string | null> => {
    setIsSubmitting(true);
    setError(undefined);
    try {
      const { hash } = await submitJobApplication(request);

      return hash;
    } catch (submitError) {
      setError(submitError instanceof Error ? submitError : new Error(String(submitError)));

      return null;
    } finally {
      setIsSubmitting(false);
    }
  };

  return { submit, isSubmitting, error };
}
