import { RouteScroll } from './RouteScroll';
import { Navigate, Route, Routes } from 'react-router';
import { LandingPage } from '../features/landing/LandingPage';
import { DiscoveryPage } from '../features/discovery/DiscoveryPage';

import { PetCreatePage } from '../features/profile-creation/PetCreatePage';

import { MessagingPage } from '../features/messaging/MessagingPage';

import { OwnProfilePage } from '../features/own-profile/OwnProfilePage';

export function App() {
  return (
    <>
    <RouteScroll />
    <Routes>
      <Route path="/" element={<LandingPage />} />
      <Route path="/profile" element={<OwnProfilePage />} />
      <Route path="/messages" element={<MessagingPage />} />
      <Route path="/pet/create" element={<PetCreatePage />} />
      <Route path="/discover" element={<DiscoveryPage />} />
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
    </>
  );
}
