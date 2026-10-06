import { Link } from 'react-router';
import { BrandMark } from './BrandMark';

export function PetMingleLogo() {
  return (
    <Link to="/" className="brand" aria-label="PetMingle home">
      <BrandMark className="brand-mark" />
      <span className="brand-wordmark" aria-hidden="true">petmingle</span>
    </Link>
  );
}
