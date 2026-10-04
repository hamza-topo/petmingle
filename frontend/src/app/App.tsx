import { Navigate, Route, Routes } from 'react-router';

import { PetRequiredRoute } from '../auth/PetRequiredRoute';
import { ProtectedRoute } from '../auth/ProtectedRoute';
import { DiscoveryPage } from '../features/discovery/DiscoveryPage';
import { SignInPage } from '../features/auth/SignInPage';
import { LandingPage } from '../features/landing/LandingPage';
import { MessagingPage } from '../features/messaging/MessagingPage';
import { OwnProfilePage } from '../features/own-profile/OwnProfilePage';
import { PetCreatePage } from '../features/profile-creation/PetCreatePage';
import { RouteScroll } from './RouteScroll';

export function App() {
  return (
    <>
      <RouteScroll />

      <Routes>
        <Route path="/" element={<LandingPage />} />
        <Route path="/signin" element={<SignInPage />} />

        <Route element={<ProtectedRoute />}>
          <Route
            path="/pet/create"
            element={<PetCreatePage />}
          />

          <Route element={<PetRequiredRoute />}>
            <Route
              path="/discover"
              element={<DiscoveryPage />}
            />
            <Route
              path="/messages"
              element={<MessagingPage />}
            />
            <Route
              path="/profile"
              element={<OwnProfilePage />}
            />
          </Route>
        </Route>

        <Route
          path="*"
          element={<Navigate to="/" replace />}
        />
      </Routes>
    </>
  );
}
