const publicKeys = new Set([
  'VITE_API_BASE_URL', 'VITE_MEDIA_BASE_URL', 'VITE_REALTIME_ENABLED',
  'VITE_PUSHER_APP_KEY', 'VITE_PUSHER_APP_CLUSTER', 'VITE_PUSHER_WS_HOST',
  'VITE_PUSHER_WS_PORT', 'VITE_PUSHER_WS_SCHEME',
]);

function publicUrl(value, key, mode, api = false) {
  let url;
  try { url = new URL(value); } catch { throw new Error(key + ' must be an absolute browser-facing URL.'); }
  const localReview = mode === 'desktop-review'
    && ['127.0.0.1', 'localhost'].includes(url.hostname)
    && url.protocol === 'http:';
  if ((!localReview && url.protocol !== 'https:') || url.username || url.password || url.search || url.hash) {
    throw new Error(key + ' must use HTTPS without credentials, query or fragment.');
  }
  if (!localReview && ['localhost', '127.0.0.1', '0.0.0.0', '[::1]'].includes(url.hostname)) {
    throw new Error(key + ' cannot use a loopback host in a deployment build.');
  }
  if (api ? url.pathname.replace(/\/+$/, '') !== '/api/v.0' : !['', '/'].includes(url.pathname)) {
    throw new Error(key + (api ? ' must end in /api/v.0.' : ' must be an origin without a path.'));
  }
}

export function validateBuildEnvironment(env, mode) {
  for (const key of Object.keys(env)) {
    if (key.startsWith('VITE_') && !publicKeys.has(key)) {
      throw new Error('Unsupported public build variable: ' + key + '. Never put secrets in VITE_ variables.');
    }
  }
  if (!env.VITE_API_BASE_URL?.trim()) throw new Error('VITE_API_BASE_URL is required for a build.');
  publicUrl(env.VITE_API_BASE_URL.trim(), 'VITE_API_BASE_URL', mode, true);
  if (env.VITE_MEDIA_BASE_URL?.trim()) publicUrl(env.VITE_MEDIA_BASE_URL.trim(), 'VITE_MEDIA_BASE_URL', mode);
  const enabled = env.VITE_REALTIME_ENABLED?.trim().toLowerCase() !== 'false' && !!env.VITE_PUSHER_APP_KEY?.trim();
  if (enabled) {
    if (mode !== 'desktop-review' && env.VITE_PUSHER_WS_SCHEME?.trim() === 'ws') {
      throw new Error('Production realtime must use WSS.');
    }
    const host = env.VITE_PUSHER_WS_HOST?.trim();
    if (host && (!/^[a-z0-9.-]+$/i.test(host) || (mode !== 'desktop-review' && ['localhost', '127.0.0.1', '0.0.0.0'].includes(host)))) {
      throw new Error('VITE_PUSHER_WS_HOST must be a public hostname without a scheme or path.');
    }
    const port = env.VITE_PUSHER_WS_PORT?.trim();
    if (port && (!/^\d+$/.test(port) || Number(port) < 1 || Number(port) > 65535)) {
      throw new Error('VITE_PUSHER_WS_PORT must be a valid port.');
    }
  }
}
