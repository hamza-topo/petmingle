import { useState } from 'react';
import { SiteHeader } from '../../components/SiteHeader';
import { ownPet } from './profile.fixtures';
import { PetProfileGallery, ProfilePhoto } from './components/PetProfileGallery';
import { PetProfileSummary } from './components/PetProfileSummary';
import { ProfileAbout, ProfileDetails } from './components/ProfileDetails';
import { PlusPlans } from './components/PlusPlans';
export function OwnProfilePage() {
  const [activePhotoId, setActivePhotoId] = useState(ownPet.gallery[0].id);
  const activePhoto = ownPet.gallery.find(photo => photo.id === activePhotoId)!;
  return <div className="own-profile-page"><a className="skip-link" href="#own-profile-main">Skip to pet profile</a><SiteHeader petIdentity={{ name: ownPet.name, photo: ownPet.gallery[0].asset }} />
    <main id="own-profile-main" className="own-profile-layout"><div className="own-profile-content">
      <section className="own-profile-overview" aria-label="Pet profile overview"><div className="own-overview-top"><ProfilePhoto photo={activePhoto} /><PetProfileSummary /></div><PetProfileGallery photos={ownPet.gallery} activeId={activePhotoId} onSelect={setActivePhotoId} /></section>
      <ProfileAbout /><ProfileDetails />
    </div><PlusPlans /></main>
  </div>;
}
