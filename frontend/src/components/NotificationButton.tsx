import { Bell } from 'lucide-react';

export function NotificationButton({ className, size }: { className: string; size: number }) {
  return (
    <button type="button" className={className} disabled aria-label="Notifications — not available yet">
      <Bell size={size} aria-hidden="true" />
    </button>
  );
}
