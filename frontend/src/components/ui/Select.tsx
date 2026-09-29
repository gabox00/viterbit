import type { ComponentProps } from 'react';
import { classNames } from './classNames';
import { FIELD_CLASS_NAME } from './fieldStyles';

export interface ISelectOption {
  value: string;
  label: string;
}

interface ISelectProps extends Omit<ComponentProps<'select'>, 'children'> {
  options: ISelectOption[];
}

export function Select({ options, className, ...props }: ISelectProps) {
  return (
    <select className={classNames(FIELD_CLASS_NAME, 'pr-8', className)} {...props}>
      {options.map((option) => (
        <option key={option.value} value={option.value}>
          {option.label}
        </option>
      ))}
    </select>
  );
}
