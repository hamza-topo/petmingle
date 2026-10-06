import { test } from 'node:test';
import assert from 'node:assert/strict';
import { validateBuildEnvironment } from './build-environment.mjs';

const valid = { VITE_API_BASE_URL: 'https://api.petmingle.test/api/v.0', VITE_REALTIME_ENABLED: 'false' };
test('accepts explicit HTTPS API and optional media origins', () => {
  validateBuildEnvironment({ ...valid, VITE_MEDIA_BASE_URL: 'https://media.petmingle.test' }, 'production');
});
test('rejects missing, insecure, ambiguous and credential-bearing API URLs', () => {
  for (const url of ['', '/api/v.0', 'http://api.petmingle.test/api/v.0', 'https://localhost/api/v.0', 'https://api.petmingle.test', 'https://user:password@api.petmingle.test/api/v.0', 'https://api.petmingle.test/api/v.0?token=secret', 'https://api.petmingle.test/api/v.0#fragment']) {
    assert.throws(() => validateBuildEnvironment({ ...valid, VITE_API_BASE_URL: url }, 'production'));
  }
});
test('does not accept an HTTP deployment by changing the mode name', () => {
  assert.throws(() => validateBuildEnvironment({ ...valid, VITE_API_BASE_URL: 'http://localhost:8000/api/v.0' }, 'staging'));
  validateBuildEnvironment({ ...valid, VITE_API_BASE_URL: 'http://127.0.0.1:4173/api/v.0' }, 'desktop-review');
  assert.throws(() => validateBuildEnvironment({ ...valid, VITE_API_BASE_URL: 'http://remote.test/api/v.0' }, 'desktop-review'));
});
test('rejects public secret variables without echoing their values', () => {
  for (const key of ['VITE_TOKEN', 'VITE_APP_KEY', 'VITE_PUSHER_APP_SECRET', 'VITE_DB_PASSWORD']) {
    assert.throws(() => validateBuildEnvironment({ ...valid, [key]: 'do-not-print-this' }, 'production'), error => !error.message.includes('do-not-print-this'));
  }
});
test('rejects media paths and insecure enabled realtime', () => {
  assert.throws(() => validateBuildEnvironment({ ...valid, VITE_MEDIA_BASE_URL: 'https://media.test/storage' }, 'production'));
  assert.throws(() => validateBuildEnvironment({ ...valid, VITE_REALTIME_ENABLED: 'true', VITE_PUSHER_APP_KEY: 'public-key', VITE_PUSHER_WS_SCHEME: 'ws' }, 'production'));
  validateBuildEnvironment({ ...valid, VITE_REALTIME_ENABLED: 'true', VITE_PUSHER_APP_KEY: 'public-key', VITE_PUSHER_WS_SCHEME: 'wss' }, 'production');
});


test('accepts public geocoding and XYZ map variables, including blank Docker defaults', () => {
  validateBuildEnvironment({ ...valid, VITE_GEOCODER_BASE_URL: '', VITE_MAP_TILE_URL: '', VITE_MAP_TILE_ATTRIBUTION: '' }, 'production');
  validateBuildEnvironment({ ...valid, VITE_GEOCODER_BASE_URL: 'https://places.test/photon', VITE_MAP_TILE_URL: 'https://tiles.test/{z}/{x}/{y}.png', VITE_MAP_TILE_ATTRIBUTION: '&copy; Provider' }, 'production');
});
test('rejects insecure or credential-bearing map services and malformed tile templates', () => {
  for (const url of ['http://places.test', 'https://user:password@places.test', 'https://places.test?secret=value', 'https://localhost']) {
    assert.throws(() => validateBuildEnvironment({ ...valid, VITE_GEOCODER_BASE_URL: url }, 'production'));
  }
  for (const url of ['http://tiles.test/{z}/{x}/{y}.png', 'https://tiles.test/map.png', 'https://tiles.test/{z}/{x}/{y}.png?token=secret']) {
    assert.throws(() => validateBuildEnvironment({ ...valid, VITE_MAP_TILE_URL: url }, 'production'));
  }
});
