import type { ComponentProps } from 'react';
import { classNames } from './classNames';
import { FIELD_CLASS_NAME } from './fieldStyles';

export function TextInput({ className, ...props }: ComponentProps<'input'>) {
  return <input className={classNames(FIELD_CLASS_NAME, className)} {...props} />;
}
