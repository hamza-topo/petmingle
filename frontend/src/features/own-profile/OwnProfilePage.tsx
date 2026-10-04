import { useEffect, useState } from 'react';

import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
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

export function OwnProfilePage() {
  const { user, pet } = useAuth();

  const [profile, setProfile] =
    useState<CurrentPetProfile | null>(null);

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [reloadKey, setReloadKey] = useState(0);

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
        setError('Unable to load the current pet profile.');
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
      } catch {
        if (!cancelled) {
          setError('Unable to load the current pet profile.');
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
          <p role="status">
            No current pet profile is available.
          </p>
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
          <p role="status">
            Loading pet profile...
          </p>
        </main>
      </div>
    );
  }

  if (error || !profile) {
    return (
      <div className="own-profile-page">
        {header}

        <main className="auth-route-state">
          <div className="auth-route-message">
            <h1>We couldn’t load your pet profile</h1>

            <p role="alert">
              {error ?? 'Unable to load the current pet profile.'}
            </p>

            <button
              type="button"
              onClick={() =>
                setReloadKey(current => current + 1)
              }
            >
              Try again
            </button>
          </div>
        </main>
      </div>
    );
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
              <PetProfileSummary pet={profile} />
            </div>

            <PetProfileGallery
              photos={ownPet.gallery}
              activeId={activePhotoId}
              onSelect={setActivePhotoId}
            />
          </section>

          <ProfileAbout pet={profile} />
          <ProfileDetails pet={profile} />
        </div>

        <PlusPlans />
      </main>
    </div>
  );
}
