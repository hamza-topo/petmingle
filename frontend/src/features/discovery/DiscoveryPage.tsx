import { PawPrint } from 'lucide-react';
import { DiscoveryHeader } from './components/DiscoveryHeader';
import { DiscoverySidebar } from './components/DiscoverySidebar';
import { FeaturedPetCard } from './components/FeaturedPetCard';
import { DiscoveryFilters } from './components/DiscoveryFilters';
import { PetGrid } from './components/PetGrid';
import { discoveryContext } from './discovery.fixtures';

export function DiscoveryPage() {
  return (
    <div className="discovery-page">
      <a href="#discovery-main" className="skip-link">Skip to content</a>
      <DiscoveryHeader />
      <main className="discovery-layout" id="discovery-main">
        <div className="discovery-results">
          <div className="discovery-upper">
            <DiscoverySidebar />
            <section className="discovery-spotlight" aria-labelledby="discovery-heading">
              <div className="discovery-intro">
                <div><h1 id="discovery-heading">Discover Amazing <span className="text-brand-pink">Pets</span></h1><p>Meet adorable pets near you who are looking for friends — and the people who love them.</p></div>
                <div className="nearby-count"><span><PawPrint size={30} fill="currentColor" aria-hidden="true" /></span><p><strong>{discoveryContext.petCount}</strong> pets near<br /><small>{discoveryContext.location}</small></p></div>
              </div>
              <FeaturedPetCard />
            </section>
          </div>
          <PetGrid />
        </div>
        <DiscoveryFilters />
      </main>
    </div>
  );
}
