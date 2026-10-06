/** Two paths crossing: the approved PetMingle direction 02, drawn as scalable artwork. */
export function BrandMark({ className }: { className?: string }) {
  return (
    <svg className={className} viewBox="0 0 72 52" fill="none" aria-hidden="true" focusable="false">
      <path d="M4 23h12c4 0 5-4 5-8V9c0-6 5-7 9-3l23 25c4 4 5 7 5 11v2c0 6-5 7-9 3L25 21" stroke="var(--color-brand-blue)" strokeWidth="7" strokeLinecap="round" strokeLinejoin="round" />
      <path d="M4 36h14c4 0 7-3 10-6l10-10c3-3 5-5 9-5h21M39 28h29" stroke="var(--color-brand-pink)" strokeWidth="7" strokeLinecap="round" strokeLinejoin="round" />
      <path d="m28 12 13 14" stroke="var(--color-brand-blue)" strokeWidth="7" strokeLinecap="round" />
    </svg>
  );
}
