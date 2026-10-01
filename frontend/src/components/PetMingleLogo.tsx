import { Link } from 'react-router';
import { landingAssets } from '../assets/landingAssets';
import { ReferenceImage } from './ReferenceImage';

export function PetMingleLogo() {
  return (
    <Link to="/" className="brand" aria-label="PetMingle home">
      {landingAssets.logo.src ? (
        <ReferenceImage asset={landingAssets.logo} className="brand-artwork" />
      ) : (
        <span className="brand-placeholder" title="Placeholder — original logo artwork unavailable">
          <span className="brand-artwork-placeholder" aria-hidden="true">Logo</span>
          <span className="brand-wordmark" aria-hidden="true">
            <span className="text-brand-blue">Pet</span><span className="text-brand-pink">Mingle</span>
          </span>
        </span>
      )}
    </Link>
  );
}
