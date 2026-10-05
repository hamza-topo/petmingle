import { useEffect, useState } from 'react';

import { ApiError } from '../../api/errors';
import { describeApiFailure } from '../../api/presentation';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { ApiState } from '../../components/ApiState';
import { SiteHeader } from '../../components/SiteHeader';
import { currentPetProfileRequest } from './profile.api';
import type { CurrentPetProfile } from './profile.types';
import { ownPet } from './profile.fixtures';
import {
  PetProfileGallery,
  ProfilePhoto,
} from './components/PetProfileGallery';
import { PetProfileSummary } from './components/PetProfileSummary';
import {
  ProfileAbout,
  ProfileDetails,
} from './components/ProfileDetails';
import { PlusPlans } from './components/PlusPlans';
import { PetProfileEditForm } from './components/PetProfileEditForm';

export function OwnProfilePage() {
  const { user, pet, refreshIdentity } = useAuth();

  const [profile, setProfile] =
    useState<CurrentPetProfile | null>(null);

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<unknown | null>(null);
  const [reloadKey, setReloadKey] = useState(0);
  const [editing, setEditing] = useState(false);

  const [activePhotoId, setActivePhotoId] =
    useState(ownPet.gallery[0].id);

  useEffect(() => {
    let cancelled = false;

    async function loadCurrentPet() {
      setProfile(null);
      setError(null);

      if (!user || !pet) {
        setLoading(false);
        return;
      }

      const token = tokenStorage.get();

      if (!token) {
        setLoading(false);
        setError(
          new ApiError(
            'Authentication token is missing.',
            401,
          ),
        );
        return;
      }

      setLoading(true);

      try {
        const currentPet = await currentPetProfileRequest({
          petId: pet.id,
          userId: user.id,
          token,
        });

        if (!cancelled) {
          setProfile(currentPet);
        }
      } catch (caught) {
        if (!cancelled) {
          setError(caught);
        }
      } finally {
        if (!cancelled) {
          setLoading(false);
        }
      }
    }

    void loadCurrentPet();

    return () => {
      cancelled = true;
    };
  }, [pet?.id, reloadKey, user?.id]);

  if (!pet) {
    return (
      <div className="own-profile-page">
        <SiteHeader
          identity={
            user
              ? {
                  name: user.name,
                  kind: 'account',
                }
              : undefined
          }
        />

        <main className="auth-route-state">
          <ApiState
            kind="empty"
            title="No pet profile yet"
            message="Create a pet profile to continue."
          />
        </main>
      </div>
    );
  }

  const header = (
    <SiteHeader
      identity={{
        name: profile?.name ?? pet.name,
        kind: 'pet',
      }}
    />
  );

  if (loading) {
    return (
      <div className="own-profile-page">
        {header}

        <main className="auth-route-state">
          <ApiState
            kind="loading"
            message="Loading pet profile..."
          />
        </main>
      </div>
    );
  }

  if (error || !profile) {
    const failure = describeApiFailure(
      error
        ?? new Error('Current pet profile is missing.'),
    );

    return (
      <div className="own-profile-page">
        {header}

        <main className="auth-route-state">
          <ApiState
            kind="error"
            title="We couldn’t load your pet profile"
            message={failure.message}
            onRetry={
              failure.retryable
                ? () =>
                    setReloadKey(current => current + 1)
                : undefined
            }
          />
        </main>
      </div>
    );
  }

  async function handleProfileSaved() {
    setEditing(false);
    setReloadKey(current => current + 1);

    try {
      await refreshIdentity();
    } catch {
      // The profile reload below remains the source of truth for this screen.
    }
  }

  const activePhoto =
    ownPet.gallery.find(
      photo => photo.id === activePhotoId,
    ) ?? ownPet.gallery[0];

  return (
    <div className="own-profile-page">
      {header}

      <a
        className="skip-link"
        href="#own-profile-main"
      >
        Skip to pet profile
      </a>

      <main
        id="own-profile-main"
        className="own-profile-layout"
      >
        <div className="own-profile-content">
          <section
            className="own-profile-overview"
            aria-label="Pet profile overview"
          >
            <div className="own-overview-top">
              <ProfilePhoto photo={activePhoto} />
              <PetProfileSummary
                pet={profile}
                onEdit={() => setEditing(true)}
              />
            </div>

            <PetProfileGallery
              photos={ownPet.gallery}
              activeId={activePhotoId}
              onSelect={setActivePhotoId}
            />
          </section>

          {editing && (
            <PetProfileEditForm
              pet={profile}
              onCancel={() => setEditing(false)}
              onSaved={handleProfileSaved}
            />
          )}

          <ProfileAbout
            pet={profile}
            onEdit={() => setEditing(true)}
          />
          <ProfileDetails
            pet={profile}
            onEdit={() => setEditing(true)}
          />
        </div>

        <PlusPlans />
      </main>
    </div>
  );
}
