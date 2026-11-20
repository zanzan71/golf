// Tests de la fonction /api/contact : node --test tests/
import test from 'node:test';
import assert from 'node:assert/strict';
import handler, { validate, escapeHtml, buildEmail } from '../api/contact.js';

const NOW = 1_800_000_000_000;
const valid = {
  nom: 'Camille Martin',
  email: 'Camille@Club.fr',
  profil: 'president',
  club: 'AS Val-Fleuri',
  objet: 'club-fondateur',
  message: 'Nous aimerions tester ELAZYO avec deux équipes.',
  consentement: true,
  site_web: '',
  t: NOW - 60_000,
};

function mockRes() {
  return {
    statusCode: 0, headers: {}, body: '',
    setHeader(k, v) { this.headers[k.toLowerCase()] = v; },
    end(b = '') { this.body = b; },
    json() { return JSON.parse(this.body); },
  };
}
function mockReq(body, headers = {}) {
  return { method: 'POST', headers: { 'content-type': 'application/json', origin: 'https://www.elazyo.fr', 'x-forwarded-for': `10.0.0.${Math.floor(Math.random() * 250)}`, ...headers }, body };
}

test('validate : une demande correcte est normalisée', () => {
  const r = validate(valid, NOW);
  assert.ok(r.data);
  assert.equal(r.data.email, 'camille@club.fr');
  assert.equal(r.data.consentement, true);
});

test('validate : champs manquants ou invalides', () => {
  const r = validate({ ...valid, nom: '', email: 'pas-un-mail', profil: 'pirate', objet: 'x', message: 'court', consentement: false }, NOW);
  assert.deepEqual(Object.keys(r.errors).sort(), ['consentement', 'email', 'message', 'nom', 'objet', 'profil']);
});

test('validate : champ piège et saisie trop rapide = robot', () => {
  assert.equal(validate({ ...valid, site_web: 'http://spam' }, NOW).spam, true);
  assert.equal(validate({ ...valid, t: NOW - 500 }, NOW).spam, true);
});

test('validate : caractères de contrôle retirés, longueurs bornées', () => {
  const r = validate({ ...valid, nom: 'Ali\u0000ce\u0007', club: 'x'.repeat(500) }, NOW);
  assert.equal(r.data.nom, 'Alice');
  assert.equal(r.data.club.length, 120);
});

test('escapeHtml et e-mail : pas d’injection HTML', () => {
  assert.equal(escapeHtml('<script>"&\''), '&lt;script&gt;&quot;&amp;&#39;');
  const mail = buildEmail({ ...validate(valid, NOW).data, message: '<img src=x onerror=alert(1)> bonjour' });
  assert.ok(!mail.html.includes('<img src=x'));
  assert.ok(mail.subject.startsWith('[Site ELAZYO] Devenir Club Fondateur — Camille Martin'));
});

test('handler : refuse GET et une origine étrangère', async () => {
  const r1 = mockRes();
  await handler({ method: 'GET', headers: {} }, r1);
  assert.equal(r1.statusCode, 405);
  const r2 = mockRes();
  await handler(mockReq(valid, { origin: 'https://evil.example' }), r2, { env: {}, now: NOW });
  assert.equal(r2.statusCode, 403);
});

test('handler : 422 avec les erreurs par champ', async () => {
  const res = mockRes();
  await handler(mockReq({ ...valid, email: 'faux' }), res, { env: {}, now: NOW });
  assert.equal(res.statusCode, 422);
  assert.ok(res.json().errors.email);
});

test('handler : 503 tant que l’envoi n’est pas configuré (jamais de faux succès)', async () => {
  const res = mockRes();
  await handler(mockReq(valid), res, { env: {}, now: NOW });
  assert.equal(res.statusCode, 503);
  assert.equal(res.json().error, 'not_configured');
});

test('handler : envoi par Resend, réponse à la personne', async () => {
  let call;
  const fetchImpl = async (url, init) => { call = { url, init }; return { ok: true, status: 200 }; };
  const res = mockRes();
  await handler(mockReq(valid), res, { env: { RESEND_API_KEY: 'k', CONTACT_TO: 'contact@elazyo.fr', CONTACT_FROM: 'Site <site@elazyo.fr>' }, fetchImpl, now: NOW });
  assert.equal(res.statusCode, 200);
  assert.equal(call.url, 'https://api.resend.com/emails');
  const payload = JSON.parse(call.init.body);
  assert.equal(payload.reply_to, 'camille@club.fr');
  assert.deepEqual(payload.to, ['contact@elazyo.fr']);
});

test('handler : transfert vers le back-end ELAZYO si configuré', async () => {
  let call;
  const fetchImpl = async (url, init) => { call = { url, init }; return { ok: true, status: 200 }; };
  const res = mockRes();
  await handler(mockReq(valid), res, { env: { CONTACT_FORWARD_URL: 'https://app.elazyo.fr/api/public/contact', CONTACT_FORWARD_TOKEN: 's3cret' }, fetchImpl, now: NOW });
  assert.equal(res.statusCode, 200);
  assert.equal(call.init.headers.Authorization, 'Bearer s3cret');
  assert.equal(JSON.parse(call.init.body).objetLibelle, 'Devenir Club Fondateur');
});

test('handler : formulaire sans JavaScript, redirection vers la page merci', async () => {
  const fetchImpl = async () => ({ ok: true, status: 200 });
  const body = new URLSearchParams({ ...valid, consentement: 'oui', t: '' }).toString();
  const res = mockRes();
  await handler(mockReq(body, { 'content-type': 'application/x-www-form-urlencoded', accept: 'text/html' }), res, { env: { RESEND_API_KEY: 'k', CONTACT_TO: 'a@b.fr', CONTACT_FROM: 'c@d.fr' }, fetchImpl, now: NOW });
  assert.equal(res.statusCode, 303);
  assert.equal(res.headers.location, '/merci.html');
});

test('handler : limite de débit (6e envoi en 10 min)', async () => {
  const fetchImpl = async () => ({ ok: true, status: 200 });
  const env = { RESEND_API_KEY: 'k', CONTACT_TO: 'a@b.fr', CONTACT_FROM: 'c@d.fr' };
  let last;
  for (let i = 0; i < 6; i++) {
    last = mockRes();
    await handler(mockReq(valid, { 'x-forwarded-for': '192.0.2.77' }), last, { env, fetchImpl, now: NOW + i });
  }
  assert.equal(last.statusCode, 429);
});
