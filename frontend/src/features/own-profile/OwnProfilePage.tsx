import { useEffect, useRef, useState } from 'react';

import { ApiError } from '../../api/errors';
import { mediaUrl } from '../../api/config';
import { describeApiFailure } from '../../api/presentation';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { ApiState } from '../../components/ApiState';
import { SiteHeader } from '../../components/SiteHeader';
import {
  currentPetProfileRequest,
  removePetImageRequest,
  replacePetImageRequest,
} from './profile.api';
import type { CurrentPetProfile } from './profile.types';
import { photoSchema } from '../profile-creation/profile.schema';
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
import {
  accountLocationLabel,
  useAccountLocation,
} from '../account-location/AccountLocationProvider';

export function OwnProfilePage() {
  const { user, pet, refreshIdentity } = useAuth();
  const accountLocation = useAccountLocation();
  const locationLabel =
    accountLocationLabel(accountLocation);

  const [profile, setProfile] =
    useState<CurrentPetProfile | null>(null);

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<unknown | null>(null);
  const [reloadKey, setReloadKey] = useState(0);
  const [editing, setEditing] = useState(false);
  const [activePhotoId, setActivePhotoId] =
    useState<string | null>(null);
  const [pendingPhoto, setPendingPhoto] =
    useState<File | null>(null);
  const [previewUrl, setPreviewUrl] =
    useState<string | null>(null);
  const [mediaError, setMediaError] =
    useState<string | null>(null);
  const [mediaBusy, setMediaBusy] = useState(false);
  const mediaInput = useRef<HTMLInputElement>(null);

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

  useEffect(() => {
    if (!pendingPhoto) {
      setPreviewUrl(null);
      return;
    }

    const url = URL.createObjectURL(pendingPhoto);

    setPreviewUrl(url);

    return () => {
      URL.revokeObjectURL(url);
    };
  }, [pendingPhoto]);

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

        <main tabIndex={-1} className="auth-route-state">
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

        <main tabIndex={-1} className="auth-route-state">
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

        <main tabIndex={-1} className="auth-route-state">
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

  const profileId = profile.id;

  async function handleProfileSaved() {
    setEditing(false);
    setReloadKey(current => current + 1);

    try {
      await refreshIdentity();
    } catch {
      // The profile reload below remains the source of truth for this screen.
    }
  }

  const persistedPhotos = profile.images.map(
    (path, index) => ({
      id: `saved-${index}-${path}`,
      src: mediaUrl(path),
      alt: `${profile.name} saved photo ${index + 1}`,
    }),
  );

  const previewPhoto = previewUrl
    ? {
        id: 'pending-preview',
        src: previewUrl,
        alt: `${profile.name} pending photo preview`,
        isPreview: true,
      }
    : null;

  const galleryPhotos = previewPhoto
    ? [previewPhoto, ...persistedPhotos]
    : persistedPhotos;

  const activePhoto =
    galleryPhotos.find(
      photo => photo.id === activePhotoId,
    )
    ?? previewPhoto
    ?? persistedPhotos[0]
    ?? null;

  function chooseMedia() {
    mediaInput.current?.click();
  }

  function selectMedia(file: File | undefined) {
    if (!file) {
      return;
    }

    const result = photoSchema.safeParse(file);

    if (!result.success) {
      setMediaError(
        result.error.issues[0]?.message
          ?? 'Choose a valid pet photo.',
      );
      setPendingPhoto(null);
      return;
    }

    setMediaError(null);
    setPendingPhoto(file);
    setActivePhotoId('pending-preview');
  }

  async function saveMedia() {
    if (!pendingPhoto || mediaBusy) {
      return;
    }

    const token = tokenStorage.get();

    if (!token) {
      setMediaError(
        'Your session is no longer valid. Sign in again to continue.',
      );
      return;
    }

    setMediaBusy(true);
    setMediaError(null);

    try {
      await replacePetImageRequest({
        petId: profileId,
        token,
        image: pendingPhoto,
      });

      setPendingPhoto(null);
      setActivePhotoId(null);
      setReloadKey(current => current + 1);
    } catch (caught) {
      setPendingPhoto(null);
      setActivePhotoId(null);
      setMediaError(
        describeApiFailure(caught).message,
      );
    } finally {
      setMediaBusy(false);
    }
  }

  async function removeMedia() {
    if (
      persistedPhotos.length === 0
      || mediaBusy
    ) {
      return;
    }

    const token = tokenStorage.get();

    if (!token) {
      setMediaError(
        'Your session is no longer valid. Sign in again to continue.',
      );
      return;
    }

    setMediaBusy(true);
    setMediaError(null);

    try {
      await removePetImageRequest({
        petId: profileId,
        token,
      });

      setActivePhotoId(null);
      setReloadKey(current => current + 1);
    } catch (caught) {
      setMediaError(
        describeApiFailure(caught).message,
      );
    } finally {
      setMediaBusy(false);
    }
  }

  return (
    <div className="own-profile-page">
      {header}

      <a
        className="skip-link"
        href="#own-profile-main"
      >
        Skip to pet profile
      </a>

      <main tabIndex={-1}
        id="own-profile-main"
        className="own-profile-layout"
      >
        <div className="own-profile-content">
          <section
            className="own-profile-overview"
            aria-label="Pet profile overview"
          >
            <div className="own-overview-top">
              <ProfilePhoto
                photo={activePhoto}
                onChoose={chooseMedia}
                busy={mediaBusy}
              />
              <PetProfileSummary
                pet={profile}
                onEdit={() => setEditing(true)}
                locationLabel={locationLabel}
              />
            </div>

            <input
              ref={mediaInput}
              type="file"
              className="sr-only"
              accept="image/jpeg,image/png"
              aria-label="Pet media upload"
              onChange={event => {
                selectMedia(event.target.files?.[0]);
                event.target.value = '';
              }}
            />

            <PetProfileGallery
              photos={galleryPhotos}
              activeId={activePhoto?.id ?? null}
              onSelect={setActivePhotoId}
              onChoose={chooseMedia}
              onUpload={() => void saveMedia()}
              onDiscardPreview={() => {
                setPendingPhoto(null);
                setActivePhotoId(null);
                setMediaError(null);
              }}
              onRemove={() => void removeMedia()}
              busy={mediaBusy}
              error={mediaError}
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
            locationLabel={locationLabel}
          />
        </div>

        <PlusPlans />
      </main>
    </div>
  );
}
