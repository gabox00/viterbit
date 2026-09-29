import { useState, type ChangeEvent, type SubmitEvent } from 'react';
import { ApiError } from '../../api/httpClient';
import { Button } from '../../components/ui/Button';
import { Card } from '../../components/ui/Card';
import { ErrorMessage } from '../../components/ui/ErrorMessage';
import { FormField } from '../../components/ui/FormField';
import { TextArea } from '../../components/ui/TextArea';
import { TextInput } from '../../components/ui/TextInput';
import { useSubmitJobApplication } from '../../hooks/useSubmitJobApplication';
import type { ISubmitJobApplicationRequestDto } from '../../types/jobApplication';

type ApplyFormValues = Omit<ISubmitJobApplicationRequestDto, 'job_position_hash'>;

const EMPTY_VALUES: ApplyFormValues = {
  candidate_full_name: '',
  candidate_email: '',
  candidate_phone: '',
  notes: '',
  cv_text: '',
};

const CV_MIN_LENGTH = 50;

interface IApplyFormProps {
  jobPositionHash: string;
  onSubmitted: (jobApplicationHash: string) => void;
}

export function ApplyForm({ jobPositionHash, onSubmitted }: IApplyFormProps) {
  const [values, setValues] = useState(EMPTY_VALUES);
  const { submit, isSubmitting, error } = useSubmitJobApplication();
  const fieldErrors = error instanceof ApiError ? error.fieldErrors() : {};
  const hasFieldErrors = Object.keys(fieldErrors).length > 0;

  const update = (field: keyof ApplyFormValues) => (event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setValues((previous) => ({ ...previous, [field]: event.target.value }));
  };

  const handleSubmit = async (event: SubmitEvent<HTMLFormElement>) => {
    event.preventDefault();
    const jobApplicationHash = await submit({ job_position_hash: jobPositionHash, ...values });
    if (jobApplicationHash) onSubmitted(jobApplicationHash);
  };

  const fieldProps = (field: keyof ApplyFormValues) => ({
    id: field,
    name: field,
    value: values[field],
    onChange: update(field),
    'aria-invalid': field in fieldErrors,
    ...(field in fieldErrors && { 'aria-describedby': `${field}-error` }),
  });

  return (
    <Card title="Your application">
      <form onSubmit={(event) => void handleSubmit(event)} className="space-y-5">
        {error && !hasFieldErrors && <ErrorMessage message={error.message} />}
        <FormField id="candidate_full_name" label="Full name" error={fieldErrors.candidate_full_name}>
          <TextInput {...fieldProps('candidate_full_name')} required maxLength={150} autoComplete="name" />
        </FormField>
        <div className="grid gap-5 sm:grid-cols-2">
          <FormField id="candidate_email" label="Email" error={fieldErrors.candidate_email}>
            <TextInput {...fieldProps('candidate_email')} type="email" required maxLength={180} autoComplete="email" />
          </FormField>
          <FormField id="candidate_phone" label="Phone (optional)" error={fieldErrors.candidate_phone}>
            <TextInput {...fieldProps('candidate_phone')} type="tel" maxLength={30} autoComplete="tel" />
          </FormField>
        </div>
        <FormField id="notes" label="Notes (optional)" error={fieldErrors.notes}>
          <TextArea {...fieldProps('notes')} rows={3} maxLength={2000} className="font-sans" />
        </FormField>
        <FormField
          id="cv_text"
          label="CV (plain text)"
          error={fieldErrors.cv_text}
          hint={`Paste your CV as plain text. Minimum ${String(CV_MIN_LENGTH)} characters (${String(values.cv_text.length)} so far).`}
        >
          <TextArea {...fieldProps('cv_text')} rows={12} required minLength={CV_MIN_LENGTH} maxLength={20000} />
        </FormField>
        <div className="flex justify-end">
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? 'Submitting…' : 'Submit application'}
          </Button>
        </div>
      </form>
    </Card>
  );
}
