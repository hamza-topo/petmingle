import { Navigate, Route, Routes } from 'react-router';
import { LandingPage } from '../features/landing/LandingPage';
import { DiscoveryPage } from '../features/discovery/DiscoveryPage';

import { PetCreatePage } from '../features/profile-creation/PetCreatePage';

export function App() {
  return (
    <Routes>
      <Route path="/" element={<LandingPage />} />
      <Route path="/pet/create" element={<PetCreatePage />} />
      <Route path="/discover" element={<DiscoveryPage />} />
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}
