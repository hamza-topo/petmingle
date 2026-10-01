import { useEffect, useRef, useState } from 'react';
import { Camera, Check } from 'lucide-react';
import { ReferenceImage } from '../../../components/ReferenceImage';
import { photoSchema } from '../profile.schema';

export function PhotoUploader({ value, onChange, error }: {
  value: File | null; onChange: (file: File | null) => void; error?: string;
}) {
  const input = useRef<HTMLInputElement>(null);
  const [preview, setPreview] = useState<string>();
  useEffect(() => {
    if (!value || !photoSchema.safeParse(value).success) { setPreview(undefined); return; }
    const url = URL.createObjectURL(value);
    setPreview(url);
    return () => URL.revokeObjectURL(url);
  }, [value]);
  return <>
    <div className="pet-photo-row">
      <div className="pet-photo-preview">
        {preview ? <img src={preview} alt="Selected pet photo" /> : <ReferenceImage asset={{ src: null, alt: 'Pet photo', placeholder: 'Pet photo' }} />}
        <button type="button" className="pet-photo-change" aria-label="Choose pet photo" onClick={() => input.current?.click()}><Camera size={25} aria-hidden="true" /></button>
        {value && <button type="button" className="pet-photo-remove" onClick={() => { onChange(null); if (input.current) input.current.value = ''; }}>Remove photo</button>}
      </div>
      <button type="button" className="pet-photo-drop" onClick={() => input.current?.click()}
        onDragOver={event => event.preventDefault()} onDrop={event => { event.preventDefault(); const file = event.dataTransfer.files[0]; if (file) onChange(file); }}>
        <span className="pet-camera-disk"><Camera size={32} aria-hidden="true" /></span>
        <strong>Upload a photo</strong><span>Drag and drop, or click to upload</span><small>JPG, PNG (max 10MB)</small>
      </button>
      <input ref={input} type="file" className="sr-only" tabIndex={-1} aria-label="Pet photo" accept="image/jpeg,image/png" aria-invalid={!!error} aria-describedby={error ? 'photo-error' : undefined}
        onChange={event => { const file = event.target.files?.[0]; if (file) onChange(file); event.target.value = ''; }} />
      <ul className="pet-photo-tips">{['Clear and well lit', 'Shows your pet’s face', 'A happy photo gets more matches!'].map(tip => <li key={tip}><Check size={23} aria-hidden="true" /><span>{tip}</span></li>)}</ul>
    </div>
    {error && <p id="photo-error" role="alert" className="pet-form-error">{error}</p>}
  </>;
}
