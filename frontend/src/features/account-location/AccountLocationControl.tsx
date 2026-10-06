import {
  type FormEvent,
  useEffect,
  useState,
  useRef,
  useId,
} from 'react';
import {
  ChevronDown,
  MapPin,
} from 'lucide-react';

import { ApiError } from '../../api/errors';
import { describeApiFailure } from '../../api/presentation';
import {
  accountLocationLabel,
  useAccountLocation,
} from './AccountLocationProvider';

type CoordinateErrors = {
  latitude?: string;
  longitude?: string;
};

export function AccountLocationControl() {
  const location = useAccountLocation();
  const editorId = useId();
  const trigger = useRef<HTMLButtonElement>(null);
  const editor = useRef<HTMLFormElement>(null);
  const wasOpen = useRef(false);



  const [open, setOpen] = useState(false);

  useEffect(() => {
    if (open) editor.current?.querySelector<HTMLInputElement>('input')?.focus();
    else if (wasOpen.current) trigger.current?.focus();
    wasOpen.current = open;
  }, [open]);
  const [latitude, setLatitude] = useState('');
  const [longitude, setLongitude] = useState('');
  const [errors, setErrors] =
    useState<CoordinateErrors>({});
  const [saveError, setSaveError] =
    useState<string | null>(null);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (!open) {
      return;
    }

    setLatitude(
      location.currentLocation
        ? String(location.currentLocation.latitude)
        : '',
    );
    setLongitude(
      location.currentLocation
        ? String(location.currentLocation.longitude)
        : '',
    );
    setErrors({});
    setSaveError(null);
  }, [location.currentLocation, open]);

  useEffect(() => {
    if (Object.values(errors).some(Boolean)) editor.current?.querySelector<HTMLElement>('[aria-invalid="true"]')?.focus();
  }, [errors]);

  function validate():
    | {
        latitude: number;
        longitude: number;
      }
    | null {
    const nextErrors: CoordinateErrors = {};
    const parsedLatitude = Number(latitude);
    const parsedLongitude = Number(longitude);

    if (
      !latitude.trim()
      || !Number.isFinite(parsedLatitude)
      || parsedLatitude < -90
      || parsedLatitude > 90
    ) {
      nextErrors.latitude =
        'Latitude must be between -90 and 90.';
    }

    if (
      !longitude.trim()
      || !Number.isFinite(parsedLongitude)
      || parsedLongitude < -180
      || parsedLongitude > 180
    ) {
      nextErrors.longitude =
        'Longitude must be between -180 and 180.';
    }

    setErrors(nextErrors);

    if (Object.keys(nextErrors).length > 0) {
      return null;
    }

    return {
      latitude: parsedLatitude,
      longitude: parsedLongitude,
    };
  }

  function mapServerValidation(
    caught: ApiError,
  ): boolean {
    const validationErrors =
      caught.status === 422
        ? caught.payload?.errors
        : undefined;

    if (!validationErrors) {
      return false;
    }

    const nextErrors: CoordinateErrors = {
      latitude:
        validationErrors.latitude?.[0],
      longitude:
        validationErrors.longitude?.[0],
    };

    if (
      !nextErrors.latitude
      && !nextErrors.longitude
    ) {
      return false;
    }

    setErrors(nextErrors);
    return true;
  }

  async function handleSubmit(
    event: FormEvent<HTMLFormElement>,
  ) {
    event.preventDefault();

    if (saving) {
      return;
    }

    setSaveError(null);

    const coordinates = validate();

    if (!coordinates) {
      return;
    }

    setSaving(true);

    try {
      await location.saveCoordinates(coordinates);
      setOpen(false);
    } catch (caught) {
      if (
        caught instanceof ApiError
        && mapServerValidation(caught)
      ) {
        return;
      }

      setSaveError(
        describeApiFailure(caught).message,
      );
    } finally {
      setSaving(false);
    }
  }

  const label = accountLocationLabel(location);

  return (
    <div className="discovery-location-shell">
      <button
        className="discovery-location"
        ref={trigger}
        type="button"
        onClick={() => setOpen(current => !current)}
        disabled={location.status !== 'ready'}
        aria-expanded={open}
        aria-controls={open ? editorId : undefined}
      >
        <MapPin size={22} aria-hidden="true" />
        <span>{label}</span>
        <ChevronDown size={18} aria-hidden="true" />
      </button>

      {location.status === 'error' && (
        <button
          type="button"
          className="discovery-location-retry"
          onClick={() => void location.reload()}
        >
          Retry location
        </button>
      )}

      {open && location.status === 'ready' && (
        <form
          id={editorId}
          ref={editor}
          aria-busy={saving}
          onKeyDown={event => {
            if (event.key === 'Escape' && !saving) {
              event.preventDefault();
              setOpen(false);
            }
          }}
          className="discovery-location-editor"
          aria-label="Account location"
          noValidate
          onSubmit={event => void handleSubmit(event)}
        >
          <h2>
            {location.currentLocation
              ? 'Update location'
              : 'Set location'}
          </h2>

          <p>
            PetMingle stores coordinates only. No city or
            address is inferred.
          </p>

          <label>
            <span>Latitude</span>
            <input
              inputMode="decimal"
              value={latitude}
              onChange={event => {
                setLatitude(event.target.value);
                setErrors(current => ({
                  ...current,
                  latitude: undefined,
                }));
              }}
              aria-invalid={!!errors.latitude}
              aria-describedby={errors.latitude ? editorId + '-latitude-error' : undefined}
            />
            {errors.latitude && (
              <small id={editorId + '-latitude-error'} role="alert">
                {errors.latitude}
              </small>
            )}
          </label>

          <label>
            <span>Longitude</span>
            <input
              inputMode="decimal"
              value={longitude}
              onChange={event => {
                setLongitude(event.target.value);
                setErrors(current => ({
                  ...current,
                  longitude: undefined,
                }));
              }}
              aria-invalid={!!errors.longitude}
              aria-describedby={errors.longitude ? editorId + '-longitude-error' : undefined}
            />
            {errors.longitude && (
              <small id={editorId + '-longitude-error'} role="alert">
                {errors.longitude}
              </small>
            )}
          </label>

          {saveError && (
            <p
              className="discovery-location-error"
              role="alert"
            >
              {saveError}
            </p>
          )}

          <div className="discovery-location-actions">
            <button
              type="submit"
              disabled={saving}
            >
              {saving
                ? 'Saving...'
                : 'Save coordinates'}
            </button>

            <button
              type="button"
              onClick={() => setOpen(false)}
              disabled={saving}
            >
              Cancel
            </button>
          </div>
        </form>
      )}
    </div>
  );
}
