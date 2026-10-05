import {
  Camera,
  Plus,
  Trash2,
  Upload,
  X,
} from 'lucide-react';

export type PetMediaPhoto = {
  id: string;
  src: string;
  alt: string;
  isPreview?: boolean;
};

export function ProfilePhoto({
  photo,
  onChoose,
  busy,
}: {
  photo: PetMediaPhoto | null;
  onChoose: () => void;
  busy: boolean;
}) {
  return (
    <div className="own-main-photo">
      {photo ? (
        <img
          src={photo.src}
          alt={photo.alt}
        />
      ) : (
        <div
          className="own-media-empty-main"
          role="img"
          aria-label="No pet photo yet"
        >
          <Camera size={42} aria-hidden="true" />
          <span>No pet photo yet</span>
        </div>
      )}

      <button
        type="button"
        onClick={onChoose}
        disabled={busy}
        aria-label={
          photo
            ? 'Choose replacement pet photo'
            : 'Choose pet photo'
        }
      >
        <Camera size={25} aria-hidden="true" />
      </button>
    </div>
  );
}

export function PetProfileGallery({
  photos,
  activeId,
  onSelect,
  onChoose,
  onUpload,
  onDiscardPreview,
  onRemove,
  busy,
  error,
}: {
  photos: PetMediaPhoto[];
  activeId: string | null;
  onSelect: (id: string) => void;
  onChoose: () => void;
  onUpload: () => void;
  onDiscardPreview: () => void;
  onRemove: () => void;
  busy: boolean;
  error: string | null;
}) {
  const preview = photos.find(
    photo => photo.isPreview,
  );
  const persistedPhotos = photos.filter(
    photo => !photo.isPreview,
  );

  return (
    <div
      className="own-media-manager"
      role="group"
      aria-label="Pet photo gallery"
    >
      <div className="own-gallery">
        {photos.map((photo, index) => (
          <button
            key={photo.id}
            type="button"
            aria-label={
              photo.isPreview
                ? 'View pending photo preview'
                : `View saved photo ${index + 1}`
            }
            aria-pressed={activeId === photo.id}
            onClick={() => onSelect(photo.id)}
          >
            <img
              src={photo.src}
              alt={photo.alt}
            />
            {photo.isPreview && (
              <span className="own-media-preview-badge">
                Preview
              </span>
            )}
          </button>
        ))}

        <button
          type="button"
          className="own-add-photo"
          onClick={onChoose}
          disabled={busy}
        >
          <Plus size={26} aria-hidden="true" />
          <span>
            {persistedPhotos.length > 0
              ? 'Replace Photo'
              : 'Add Photo'}
          </span>
        </button>
      </div>

      {photos.length === 0 && (
        <p
          className="own-media-empty"
          role="status"
        >
          No saved pet photo yet.
        </p>
      )}

      <div className="own-media-actions">
        {preview && (
          <>
            <button
              type="button"
              className="own-media-save"
              onClick={onUpload}
              disabled={busy}
            >
              <Upload size={17} aria-hidden="true" />
              {busy ? 'Uploading...' : 'Save photo'}
            </button>

            <button
              type="button"
              onClick={onDiscardPreview}
              disabled={busy}
            >
              <X size={17} aria-hidden="true" />
              Cancel preview
            </button>
          </>
        )}

        {!preview && persistedPhotos.length > 0 && (
          <button
            type="button"
            className="own-media-remove"
            onClick={onRemove}
            disabled={busy}
          >
            <Trash2 size={17} aria-hidden="true" />
            {busy ? 'Removing...' : 'Remove photo'}
          </button>
        )}
      </div>

      {error && (
        <p
          className="own-media-error"
          role="alert"
        >
          {error}
        </p>
      )}
    </div>
  );
}
