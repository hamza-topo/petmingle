type ApiStateKind =
  | 'loading'
  | 'empty'
  | 'error';

type ApiStateProps = {
  kind: ApiStateKind;
  title?: string;
  message: string;
  compact?: boolean;
  onRetry?: () => void;
  retryLabel?: string;
};

export function ApiState({
  kind,
  title,
  message,
  compact = false,
  onRetry,
  retryLabel = 'Try again',
}: ApiStateProps) {
  const className = [
    'api-state',
    `api-state--${kind}`,
    compact ? 'api-state--compact' : null,
  ]
    .filter(Boolean)
    .join(' ');

  return (
    <div className={className}>
      {title && (
        <p className="api-state-title">
          {title}
        </p>
      )}

      <p
        role={kind === 'error' ? 'alert' : 'status'}
        aria-live={
          kind === 'loading'
            ? 'polite'
            : undefined
        }
      >
        {message}
      </p>

      {onRetry && (
        <button
          type="button"
          className="api-state-retry"
          onClick={onRetry}
        >
          {retryLabel}
        </button>
      )}
    </div>
  );
}
