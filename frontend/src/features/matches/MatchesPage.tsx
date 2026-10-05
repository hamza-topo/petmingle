import {
  useCallback,
  useEffect,
  useState,
} from 'react';

import { ApiError } from '../../api/errors';
import { describeApiFailure } from '../../api/presentation';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { ApiState } from '../../components/ApiState';
import { ReferenceImage } from '../../components/ReferenceImage';
import { DiscoverySidebar } from '../discovery/components/DiscoverySidebar';
import { MatchesHeader } from './MatchesHeader';
import {
  relationshipsRequest,
  type RelationshipCard,
  type RelationshipsResult,
} from './matches.api';

type MatchesStatus = 'loading' | 'ready' | 'error';

const emptyRelationships: RelationshipsResult = {
  matches: [],
  mismatches: [],
};

function RelationshipCardView({
  relationship,
}: {
  relationship: RelationshipCard;
}) {
  const past = relationship.kind === 'mismatch';

  return (
    <article
      className="relationship-card"
      aria-label={`${relationship.name} ${past ? 'past match' : 'active match'}`}
    >
      <div className="relationship-card-photo">
        <ReferenceImage asset={relationship.photo} />
      </div>

      <div className="relationship-card-body">
        <div className="relationship-card-heading">
          <div>
            <h3>{relationship.name}</h3>
            <p>{relationship.ageYears} years old</p>
          </div>

          <span
            className={
              past
                ? 'relationship-status relationship-status--past'
                : 'relationship-status'
            }
          >
            {past ? 'Past match' : 'Matched'}
          </span>
        </div>

        {relationship.about && (
          <p className="relationship-about">
            {relationship.about}
          </p>
        )}

        <p className="relationship-id-note">
          Pet #{relationship.targetPetId}
        </p>
      </div>
    </article>
  );
}

function RelationshipSection({
  title,
  description,
  relationships,
}: {
  title: string;
  description: string;
  relationships: RelationshipCard[];
}) {
  if (relationships.length === 0) {
    return null;
  }

  return (
    <section
      className="relationship-section"
      aria-labelledby={`${title.toLowerCase().replace(/\s+/g, '-')}-title`}
    >
      <div className="relationship-section-heading">
        <div>
          <h2
            id={`${title.toLowerCase().replace(/\s+/g, '-')}-title`}
          >
            {title}
          </h2>
          <p>{description}</p>
        </div>

        <strong>
          {relationships.length}
        </strong>
      </div>

      <div className="relationship-grid">
        {relationships.map(relationship => (
          <RelationshipCardView
            key={`${relationship.kind}-${relationship.relationshipId}`}
            relationship={relationship}
          />
        ))}
      </div>
    </section>
  );
}

export function MatchesPage() {
  const { pet } = useAuth();
  const [status, setStatus] =
    useState<MatchesStatus>('loading');
  const [relationships, setRelationships] =
    useState<RelationshipsResult>(
      emptyRelationships,
    );
  const [error, setError] =
    useState<unknown | null>(null);
  const [reloadKey, setReloadKey] = useState(0);

  const loadRelationships = useCallback(
    async () => {
      if (!pet) {
        return;
      }

      const token = tokenStorage.get();

      if (!token) {
        setError(
          new ApiError(
            'Authentication token is missing.',
            401,
          ),
        );
        setStatus('error');
        return;
      }

      setStatus('loading');
      setError(null);

      try {
        const result =
          await relationshipsRequest({
            token,
            currentPetId: pet.id,
          });

        setRelationships(result);
        setStatus('ready');
      } catch (caught) {
        setRelationships(emptyRelationships);
        setError(caught);
        setStatus('error');
      }
    },
    [pet],
  );

  useEffect(() => {
    void loadRelationships();
  }, [loadRelationships, reloadKey]);

  const failure =
    error !== null
      ? describeApiFailure(error)
      : null;

  const hasRelationships =
    relationships.matches.length > 0
    || relationships.mismatches.length > 0;

  return (
    <div className="matches-page">
      <a
        href="#matches-main"
        className="skip-link"
      >
        Skip to matches
      </a>

      <MatchesHeader />

      <main
        id="matches-main"
        className="matches-layout"
      >
        <DiscoverySidebar />

        <section
          className="matches-content"
          aria-labelledby="matches-title"
        >
          <div className="matches-intro">
            <div>
              <p className="matches-eyebrow">
                Persisted relationships
              </p>
              <h1 id="matches-title">
                Your Matches
              </h1>
              <p>
                Mutual pet connections are loaded from
                your saved PetMingle relationships.
              </p>
            </div>

            {status === 'ready' && (
              <div
                className="matches-summary"
                aria-label="Relationship summary"
              >
                <strong>
                  {relationships.matches.length}
                </strong>
                <span>active</span>
                <strong>
                  {relationships.mismatches.length}
                </strong>
                <span>past</span>
              </div>
            )}
          </div>

          {status === 'loading' && (
            <ApiState
              kind="loading"
              message="Loading your matches..."
            />
          )}

          {status === 'error' && failure && (
            <ApiState
              kind="error"
              title="Matches unavailable"
              message={failure.message}
              onRetry={
                failure.retryable
                  ? () =>
                    setReloadKey(
                      current => current + 1,
                    )
                  : undefined
              }
            />
          )}

          {status === 'ready'
            && !hasRelationships && (
              <ApiState
                kind="empty"
                title="No matches yet"
                message="When two pets like each other, the persisted match will appear here."
              />
            )}

          {status === 'ready' && (
            <>
              <RelationshipSection
                title="Active matches"
                description="Current mutual pet connections."
                relationships={relationships.matches}
              />

              <RelationshipSection
                title="Past matches"
                description="Previous mutual connections that are no longer active."
                relationships={relationships.mismatches}
              />
            </>
          )}
        </section>
      </main>
    </div>
  );
}
