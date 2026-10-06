import assert from 'node:assert/strict';
import { chromium } from 'playwright';

const base = 'http://127.0.0.1:8088';
const api = 'https://api.petmingle.test/api/v.0';
const browser = await chromium.launch({ headless: true });
try {
  const context = await browser.newContext();
  const page = await context.newPage();
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  // A 401 exercises bootstrap and the actual sign-in route without fake user credentials.
  await page.route(api + '/**', route => route.fulfill({
    status: 401, json: { success: false, message: 'Unauthenticated.' },
  }));
  for (const path of ['/', '/signin', '/discover', '/profile', '/pet/create', '/messages', '/matches']) {
    const response = await page.goto(base + path);
    assert.equal(response.status(), 200, 'Direct route must return the SPA shell: ' + path);
    await page.locator('main').waitFor();
    assert.equal((await page.reload()).status(), 200, 'Refresh must return the SPA shell: ' + path);
    await page.locator('main').waitFor();
  }
  const index = await fetch(base + '/index.html');
  assert.match(index.headers.get('cache-control'), /no-store/);
  const html = await index.text();
  const asset = html.match(/src="([^"]+\.js)"/)[1];
  const script = await fetch(base + asset);
  assert.equal(script.status, 200);
  assert.match(script.headers.get('content-type'), /javascript/);
  assert.match(script.headers.get('cache-control'), /immutable/);
  assert.ok((await script.text()).includes(api), 'Bundle must contain the configured production API URL.');
  for (const path of ['/assets/missing.js', '/api/v.0/me', '/storage/missing.jpg', '/broadcasting/auth', '/.env']) {
    assert.notEqual((await fetch(base + path)).status, 200, 'Must not fall back to HTML: ' + path);
  }
  assert.deepEqual(errors, []);
  console.log('HOSTING_SMOKE: 7 direct routes and refreshes; production API URL, asset MIME/cache and missing-path isolation passed.');
} finally { await browser.close(); }
