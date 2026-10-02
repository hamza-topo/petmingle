import { Camera, Plus } from 'lucide-react';
import { ReferenceImage } from '../../../components/ReferenceImage';
import type { GalleryPhoto } from '../profile.fixtures';
export function ProfilePhoto({ photo }: { photo: GalleryPhoto }) {
  return <div className="own-main-photo"><ReferenceImage asset={photo.asset} /><button type="button" disabled aria-label="Change profile photo — unavailable"><Camera size={25} /></button></div>;
}
export function PetProfileGallery({ photos, activeId, onSelect }: { photos: GalleryPhoto[]; activeId: string; onSelect: (id: string) => void }) {
  return <div className="own-gallery" role="group" aria-label="Pet photo gallery">{photos.map((photo, index) => <button key={photo.id} type="button" aria-label={`View photo ${index + 1}`} aria-pressed={activeId === photo.id} onClick={() => onSelect(photo.id)}><ReferenceImage asset={photo.asset} /></button>)}
    <button type="button" className="own-add-photo" disabled title="Adding photos is not available in this preview"><Plus size={26} /><span>Add Photo</span></button>
  </div>;
}
