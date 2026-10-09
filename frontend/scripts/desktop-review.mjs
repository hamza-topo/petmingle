
import { chromium } from 'playwright';
import { mkdir, writeFile } from 'node:fs/promises';
import { spawn } from 'node:child_process';

const output = 'desktop-review';
await mkdir(output, { recursive: true });
const server = spawn('npm', ['run', 'preview', '--', '--host', '127.0.0.1', '--port', '4173'], { stdio: 'inherit' });
const base = 'http://127.0.0.1:4173';
let browser;
const results = [];
const thumbnails = [];
const longName = 'Milo Alexandre du Jardin';
const race = { id: 9, species_id: 3, name: 'Labrador Retriever — croisement' };
const pet = { id: 42, user_id: 10, species_id: 3, race_id: 9, name: longName, age: 4, sexe: 1, color: null, images: [], about: 'Friendly companion looking for gentle walks. '.repeat(12) };
const otherPet = { ...pet, id: 55, user_id: 20, name: 'Luna Belle des Montagnes' };
const pageEnvelope = data => ({ success: true, message: 'OK', data, meta: { current_page: 1, last_page: 1, per_page: 24, total: data.length }, links: { first: null, last: null, prev: null, next: null } });
const envelope = data => ({ success: true, message: 'OK', data });
const participant = p => ({ user_id: p.user_id, name: p.user_id === 10 ? 'Alexandre propriétaire' : 'Camille propriétaire', pet: { ...p, age_years: p.age, sex: p.sexe, race } });
const message = { id: 90, conversation_id: 7, sender_user_id: 20, receiver_user_id: 10, content: 'A long message with a continuous URL: https://example.test/' + 'abcdefghij'.repeat(30), is_seen: true, created_at: '2026-10-06T12:00:00Z', updated_at: '2026-10-06T12:00:00Z' };
const conversation = { id: 7, current_user_id: 10, participants: [participant(pet), participant(otherPet)], last_message: message, unread_count: 0 };
function fixture(path, state) {
  if (path === '/me') return envelope({ user: { id: 10, name: 'Alexandre propriétaire', email: 'owner@example.test' }, pet: { id: 42, user_id: 10, name: longName } });
  if (path === '/species') return envelope([{ id: 3, name: 'Dog', description: 'Dogs' }]);
  if (path === '/races') return envelope([race]);
  if (path === '/races/9') return envelope(race);
  if (path === '/locations') return envelope([{ id: 1, user_id: 10, latitude: 31.6295, longitude: -7.9811, label: 'Marrakech, Morocco' }]);
  if (path === '/pets/42/statistics') return envelope({ matches: 12, likes_sent: 28 });
  if (path === '/pets/42') return envelope(pet);
  if (path === '/pets/55') return envelope(otherPet);
  if (path === '/locations/nears') return pageEnvelope(state === 'empty' ? [] : [otherPet, { ...otherPet, id: 56, name: 'Oscar' }].map(p => ({ owner: { id: 20, name: 'Camille propriétaire' }, pet: { ...p, owner_id: 20, age_years: p.age, sex: p.sexe, race }, distance_km: 1.25, is_new: false, interaction: null })));
  if (path === '/conversations') return pageEnvelope(state === 'empty' ? [] : [conversation]);
  if (path === '/messages') return pageEnvelope(state === 'empty' ? [] : [message]);
  if (path === '/conversations/7/seen') return envelope({ conversation_id: 7, marked_count: 0, unread_count: 0 });
  throw new Error('Unexpected API path: ' + path);
}
const screens = [
  { name: 'landing', path: '/', ready: 'h1' },
  { name: 'creation', path: '/pet/create', ready: 'form[aria-label="Create pet profile"] select option[value="3"]' },
  { name: 'discovery', path: '/discover', ready: '.featured-discovery-pet' },
  { name: 'profile', path: '/profile', ready: '.own-summary h1' },
  { name: 'messaging', path: '/messages', ready: '.chat-timeline' },
];
try {
  for (let attempt = 0; attempt < 60; attempt++) {
    try { if ((await fetch(base)).ok) break; } catch {}
    await new Promise(resolve => setTimeout(resolve, 250));
  }
  browser = await chromium.launch({ headless: true });
  for (const width of [1448, 1280, 1120, 768, 390, 320]) {
    for (const screen of screens.filter(s => width >= 1120 || ['landing', 'discovery'].includes(s.name))) {
      const context = await browser.newContext({ viewport: { width, height: 1000 }, reducedMotion: 'reduce' });
      await context.addInitScript(() => localStorage.setItem('petmingle.auth.token', 'visual-test-token'));
      const page = await context.newPage();
      const errors = [];
      page.on('pageerror', error => errors.push(error.message));
      await page.route('**/api/v.0/**', async route => {
        const path = new URL(route.request().url()).pathname.replace('/api/v.0', '');
        try { await route.fulfill({ json: fixture(path, 'success') }); }
        catch (error) { errors.push(error.message); await route.fulfill({ status: 500, json: { success: false, message: 'Unexpected fixture' } }); }
      });
      await page.goto(base + screen.path);
      await page.locator(screen.ready).first().waitFor({ state: screen.name === 'creation' ? 'attached' : 'visible' });
      await page.evaluate(() => document.fonts.ready);
      if (screen.name === 'landing') {
        await page.locator('.hero-photo').evaluate(img => img.decode());
        const media = await page.locator('.hero-photo').evaluate(img => ({ width: img.naturalWidth, height: img.naturalHeight }));
        if (media.width < 500 || media.height < 500) throw new Error('Landing hero did not load');
        if (await page.getByText(/placeholder/i).count()) throw new Error('Landing contains a placeholder');
      }
      const metrics = await page.evaluate(() => ({
        viewport: innerWidth, scroll: document.documentElement.scrollWidth,
        outside: [...document.querySelectorAll('header, main, main button, main h1, main h2')].filter(el => {
          const rect = el.getBoundingClientRect();
          return rect.width > 0 && (el.scrollWidth > el.clientWidth + 1 || rect.right > innerWidth + 1 || rect.left < -1);
        }).map(el => ({ tag: el.tagName, class: el.className, text: el.textContent?.trim().slice(0,60) })).slice(0,15),
      }));
      const filename = screen.name + '-' + width + '.png';
      const shot = await page.screenshot({ path: output + '/' + filename, fullPage: true });
      thumbnails.push({ name: screen.name + ' / ' + width, image: shot.toString('base64') });
      results.push({ screen: screen.name, width, state: 'success', ...metrics, errors });
      await context.close();
    }
  }
  for (const width of [1448, 1280, 1120]) {
  for (const state of ['empty', 'error', 'loading']) {
    for (const screen of screens.filter(s => ['discovery', 'messaging'].includes(s.name))) {
      const context = await browser.newContext({ viewport: { width, height: 1000 } });
      await context.addInitScript(() => localStorage.setItem('petmingle.auth.token', 'visual-test-token'));
      const page = await context.newPage();
      await page.route('**/api/v.0/**', async route => {
        const path = new URL(route.request().url()).pathname.replace('/api/v.0', '');
        const target = path === '/locations/nears' || path === '/conversations';
        if (target && state === 'loading') {
          await new Promise(resolve => setTimeout(resolve, 2000));
          if (page.isClosed()) return;
        }
        if (target && state === 'error') await route.fulfill({ status: 500, json: { success: false, message: 'Server failure' } });
        else await route.fulfill({ json: fixture(path, state) });
      });
      await page.goto(base + screen.path);
      await page.locator('main').waitFor();
      if (state === 'loading') await page.locator('main').getByRole('status').filter({ hasText: screen.name === 'discovery' ? 'Loading nearby pets' : 'Loading conversations' }).first().waitFor();
      else if (state === 'error') await page.getByRole('alert').first().waitFor();
      else await page.getByRole('status').filter({ hasText: /No / }).first().waitFor();
      const scroll = await page.evaluate(() => document.documentElement.scrollWidth);
      const shot = await page.screenshot({ path: output + '/' + screen.name + '-' + width + '-' + state + '.png', fullPage: true });
      if (width === 1120) thumbnails.push({ name: screen.name + ' / ' + width + ' / ' + state, image: shot.toString('base64') });
      results.push({ screen: screen.name, width, state, scroll, viewport: width });
      await context.close();
    }
  }
  }
  for (const width of [1280, 768, 390, 320]) {
    const context = await browser.newContext({ viewport: { width, height: 900 }, reducedMotion: 'reduce' });
    await context.addInitScript(() => localStorage.setItem('petmingle.auth.token', 'visual-test-token'));
    const page = await context.newPage();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    let saved = { id: 1, user_id: 10, latitude: 31.6295, longitude: -7.9811, label: 'Marrakech, Morocco' };
    let writes = 0;
    let nearbyRequests = 0;
    await page.route('**/api/v.0/**', async route => {
      const request = route.request();
      const path = new URL(request.url()).pathname.replace('/api/v.0', '');
      if (path === '/locations/1' && request.method() === 'PUT') {
        saved = { ...saved, ...request.postDataJSON() }; writes++;
        return route.fulfill({ json: envelope(saved) });
      }
      if (path === '/locations') return route.fulfill({ json: envelope([saved]) });
      if (path === '/locations/nears') nearbyRequests++;
      return route.fulfill({ json: fixture(path, 'success') });
    });
    await page.route('https://photon.komoot.io/**', async route => {
      if (route.request().headers().authorization) throw new Error('Account token leaked to geocoder');
      return route.fulfill({ json: { features: [{ geometry: { coordinates: [-7.5898, 33.5731] }, properties: { city: 'Casablanca', country: 'Morocco' } }] } });
    });
    const tileFixture = '<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256"><rect width="256" height="256" fill="#edf0e5"/><path d="M0 25L256 70M45 0L90 256M0 180L256 140M190 0L150 256" fill="none" stroke="#fff" stroke-width="12"/><path d="M0 25L256 70M45 0L90 256M0 180L256 140M190 0L150 256" fill="none" stroke="#dddccf" stroke-width="2"/><rect x="110" y="80" width="45" height="40" rx="12" fill="#c9dfb7"/><text x="8" y="245" fill="#82927e" font-size="9">CI map fixture</text></svg>';
    await page.route('https://tile.openstreetmap.org/**', route => route.fulfill({ contentType: 'image/svg+xml', body: tileFixture }));
    await page.goto(base + '/discover');
    await page.locator('.featured-discovery-pet').first().waitFor({ state: 'attached' });
    await page.getByRole('button', { name: 'Marrakech, Morocco' }).click();
    const modal = page.getByRole('dialog');
    await modal.waitFor();
    await page.locator('.leaflet-container').waitFor();
    if (!(await page.getByRole('textbox', { name: 'Search city or neighbourhood' }).evaluate(el => el === document.activeElement))) throw new Error('Picker initial focus missing');
    await page.getByRole('button', { name: 'Confirm this area' }).focus();
    await page.keyboard.press('Tab');
    if (!(await page.getByRole('button', { name: 'Close location picker' }).evaluate(el => el === document.activeElement))) throw new Error('Native modal focus escaped');
    await page.keyboard.press('Escape');
    if (writes) throw new Error('Cancel persisted a location');
    if (!(await page.getByRole('button', { name: 'Marrakech, Morocco' }).evaluate(el => el === document.activeElement))) throw new Error('Location focus did not return');
    await page.getByRole('button', { name: 'Marrakech, Morocco' }).click();
    await page.getByRole('textbox', { name: 'Search city or neighbourhood' }).fill('Casablanca');
    await page.getByRole('button', { name: 'Search', exact: true }).click();
    await page.getByRole('button', { name: 'Casablanca, Morocco' }).click();
    await page.locator('.leaflet-container').focus();
    await page.keyboard.press('ArrowRight');
    await page.locator('.location-picker-selected strong').filter({ hasText: 'Selected area' }).waitFor();
    await page.locator('.location-picker-selected strong').filter({ hasText: 'Casablanca, Morocco' }).waitFor();
    const metrics = await modal.evaluate(el => {
      const rect = el.getBoundingClientRect();
      return { viewport: innerWidth, scroll: Math.ceil(rect.width), outside: [...el.querySelectorAll('input, button, h2')].filter(item => { const r = item.getBoundingClientRect(); return r.width > 0 && (r.left < -1 || r.right > innerWidth + 1); }).map(item => item.className) };
    });
    await page.screenshot({ path: output + '/location-picker-' + width + '.png', fullPage: false });
    const before = nearbyRequests;
    const refreshed = page.waitForResponse(response => response.url().includes('/locations/nears'));
    await page.getByRole('button', { name: 'Confirm this area' }).click();
    await refreshed;
    await modal.waitFor({ state: 'hidden' });
    await page.getByRole('button', { name: 'Casablanca, Morocco' }).waitFor();
    if (writes !== 1 || saved.label !== 'Casablanca, Morocco' || saved.longitude === -7.5898) throw new Error('Map movement or persistence failed');
    if (nearbyRequests <= before) throw new Error('Discovery did not refresh after location change');
    await page.reload();
    await page.getByRole('button', { name: 'Casablanca, Morocco' }).waitFor();
    results.push({ screen: 'location-picker', width, state: 'search-map-confirm-reload', ...metrics, errors });
    if (width === 1280) {
      // Extra visual capture uses real tiles; functional assertions above use deterministic fixtures.
      await page.unroute('https://tile.openstreetmap.org/**');
      await page.getByRole('button', { name: 'Casablanca, Morocco' }).click();
      await page.locator('.leaflet-container').waitFor();
      await page.waitForTimeout(4000);
      await page.screenshot({ path: output + '/location-picker-real-tiles.png', fullPage: false });
    }
    await context.close();
  }
  for (const width of [1280, 768, 390, 320]) {
    const context = await browser.newContext({ viewport: { width, height: 900 }, reducedMotion: 'reduce' });
    await context.addInitScript(() => localStorage.setItem('petmingle.auth.token', 'visual-test-token'));
    const page = await context.newPage();
    await page.route('**/api/v.0/**', route => route.fulfill({ json: fixture(new URL(route.request().url()).pathname.replace('/api/v.0', ''), 'success') }));
    await page.goto(base + '/discover');
    await page.locator('.featured-discovery-pet').waitFor();
    await page.getByRole('button', { name: 'Suivant', exact: true }).click();
    await page.getByRole('heading', { name: 'Oscar', exact: true }).waitFor();
    await page.getByRole('button', { name: 'Filtres', exact: true }).click();
    const modal = page.getByRole('dialog', { name: 'Filtres' });
    await modal.waitFor();
    await page.getByRole('combobox', { name: 'Distance' }).selectOption('10');
    await page.getByRole('button', { name: 'Appliquer', exact: true }).focus();
    await page.keyboard.press('Tab');
    if (!(await page.getByRole('button', { name: 'Fermer les filtres' }).evaluate(el => el === document.activeElement))) throw new Error('Filter focus escaped modal');
    await page.keyboard.press('Escape');
    if (await modal.count()) throw new Error('Escape did not dismiss filters');
    if (!(await page.getByRole('button', { name: 'Filtres', exact: true }).evaluate(el => el === document.activeElement))) throw new Error('Filter focus was not restored');
    await page.getByRole('button', { name: 'Filtres', exact: true }).click();
    if (await page.getByRole('combobox', { name: 'Distance' }).inputValue() !== '5') throw new Error('Cancelled filter draft was applied');
    await page.getByRole('combobox', { name: 'Distance' }).selectOption('10');
    const metrics = await modal.evaluate(el => {
      const r = el.getBoundingClientRect();
      return { viewport: innerWidth, scroll: Math.ceil(r.width), outside: [...el.querySelectorAll('button, select')].filter(item => { const b = item.getBoundingClientRect(); return b.left < -1 || b.right > innerWidth + 1; }).map(item => item.className) };
    });
    await page.screenshot({ path: output + '/discovery-filters-' + width + '.png' });
    await page.getByRole('button', { name: 'Appliquer', exact: true }).click();
    await page.getByRole('heading', { name: otherPet.name, exact: true }).waitFor();
    await page.getByRole('button', { name: 'Compte de Alexandre propriétaire' }).click();
    await page.getByRole('link', { name: 'Mon profil', exact: true }).waitFor();
    await page.keyboard.press('Escape');
    if (await page.getByRole('link', { name: 'Mon profil', exact: true }).count()) throw new Error('Account menu did not dismiss');
    results.push({ screen: 'discovery-filters', width, state: 'keyboard-cancel-apply-account', ...metrics });
    await context.close();
  }
  await writeFile(output + '/results.json', JSON.stringify(results, null, 2));
  // A compact contact sheet can be inspected from CI logs as well as artifacts.
  const sheet = await browser.newPage({ viewport: { width: 960, height: 1800 }, deviceScaleFactor: 1 });
  await sheet.setContent('<html><body style="margin:0;background:#e8edf4;font:14px Arial;display:grid;grid-template-columns:repeat(3,320px)">' + thumbnails.map(t => '<div style="padding:8px"><b>' + t.name + '</b><img style="width:304px;display:block" src="data:image/png;base64,' + t.image + '"></div>').join('') + '</body></html>');
  const sheetImage = await sheet.screenshot({ path: output + '/contact-sheet.png', fullPage: true });
  console.log('CONTACT_SHEET_BASE64=' + sheetImage.toString('base64'));
  console.log('DESKTOP_RESULTS=' + JSON.stringify(results));
  if (results.some(r => r.scroll > r.viewport + 1 || (r.errors?.length ?? 0) > 0 || (r.outside?.length ?? 0) > 0)) throw new Error('Desktop layout regression; inspect desktop-review/results.json');
} finally {
  await browser?.close();
  server.kill('SIGTERM');
}

