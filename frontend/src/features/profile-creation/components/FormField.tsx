import type { ReactNode } from 'react';
import type { LucideIcon } from 'lucide-react';

export function FormField({ id, label, required, icon: Icon, error, children }: {
  id: string; label: string; required?: boolean; icon?: LucideIcon; error?: string; children: ReactNode;
}) {
  return <div className="pet-field-group">
    <div className={`pet-field${Icon ? ' pet-field--icon' : ''}`}>
      {Icon && <Icon className="pet-field-icon" size={28} aria-hidden="true" />}
      <label htmlFor={id}>{label}{required && <span aria-hidden="true"> *</span>}</label>
      {children}
    </div>
    {error && <p className="pet-form-error" id={`${id}-error`} role="alert">{error}</p>}
  </div>;
}
