import type { ComponentProps } from 'react';
import { classNames } from './classNames';
import { FIELD_CLASS_NAME } from './fieldStyles';

export function TextArea({ className, ...props }: ComponentProps<'textarea'>) {
  return <textarea className={classNames(FIELD_CLASS_NAME, 'min-h-24 font-mono', className)} {...props} />;
}
