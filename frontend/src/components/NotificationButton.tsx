import { Bell } from 'lucide-react';
export function NotificationButton({ className, dotClassName, size }: { className: string; dotClassName: string; size: number }) {
  return <button type="button" className={className} disabled aria-label="Notifications — not available yet">
    <Bell size={size} aria-hidden="true" /><span className={dotClassName} aria-hidden="true" />
  </button>;
}
