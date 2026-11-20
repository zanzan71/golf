// POST /api/contact — traitement du formulaire « Parlons de votre club ».
//
// Fonction serverless au format Vercel (Node 20+), sans dépendance.
// Validation complète côté serveur, champ piège, délai minimal de saisie,
// limite de débit par adresse IP, contrôle de l'origine, échappement HTML.
//
// Variables d'environnement (jamais dans le code client) :
//   CONTACT_FORWARD_URL    si défini : la demande est transmise en JSON à cette adresse
//                          (par exemple la route du back-end ELAZYO qui envoie déjà
//                          le mail interne « [Site ELAZYO] Contact — … »)
//   CONTACT_FORWARD_TOKEN  jeton envoyé dans l'en-tête Authorization: Bearer (facultatif)
//   RESEND_API_KEY         sinon : envoi d'un e-mail par l'API Resend
//   CONTACT_TO             destinataire de l'e-mail (ex. contact@elazyo.fr)
//   CONTACT_FROM           expéditeur vérifié chez Resend (ex. "Site ELAZYO <site@elazyo.fr>")
//   ALLOWED_ORIGINS        origines autorisées, séparées par des virgules
//                          (défaut : https://www.elazyo.fr,https://elazyo.fr)

const PROFILS = {
  president: 'Président ou dirigeant',
  coach: 'Coach ou éducateur',
  tresorerie: 'Secrétaire ou trésorier',
  parent: 'Parent',
  benevole: 'Bénévole',
  partenaire: 'Partenaire, association ou collectivité',
  autre: 'Autre',
};
const OBJETS = {
  'club-fondateur': 'Devenir Club Fondateur',
  demo: 'Demander une démo',
  question: 'Poser une question',
  partenariat: 'Proposer un partenariat',
};
const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const CONTROL_RE = /[\u0000-\u0008\u000B\u000C\u000E-\u001F\u007F]/g;
const MIN_FILL_MS = 2500;
const MAX_BODY_BYTES = 10_000;
const RATE_LIMIT = { max: 5, windowMs: 10 * 60 * 1000 };

const hits = new Map(); // limite de débit en mémoire (par instance, au mieux)

export function clean(value, max) {
  if (typeof value !== 'string') return '';
  return value.replace(CONTROL_RE, '').replace(/\r\n?/g, '\n').trim().slice(0, max);
}

export function escapeHtml(text) {
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// Valide et normalise une demande. Renvoie { data } ou { errors } ou { spam: true }.
export function validate(input, now = Date.now()) {
  if (!input || typeof input !== 'object' || Array.isArray(input)) {
    return { errors: { form: 'Requête illisible.' } };
  }
  // Champ piège rempli, ou formulaire rempli trop vite : robot.
  if (clean(input.site_web, 200)) return { spam: true };
  const t = Number(input.t);
  if (Number.isFinite(t) && t > 0 && now - t < MIN_FILL_MS) return { spam: true };

  const data = {
    nom: clean(input.nom, 80),
    email: clean(input.email, 254).toLowerCase(),
    profil: clean(input.profil, 20),
    club: clean(input.club, 120),
    objet: clean(input.objet, 20),
    message: clean(input.message, 2000),
    consentement: input.consentement === true || input.consentement === 'oui' || input.consentement === 'on' || input.consentement === 'true',
  };

  const errors = {};
  if (data.nom.length < 2) errors.nom = 'Indiquez votre nom et prénom.';
  if (!EMAIL_RE.test(data.email)) errors.email = 'Cette adresse e-mail n’est pas valide.';
  if (!(data.profil in PROFILS)) errors.profil = 'Choisissez votre profil.';
  if (!(data.objet in OBJETS)) errors.objet = 'Choisissez l’objet de votre demande.';
  if (data.message.length < 10) errors.message = 'Votre message doit compter 10 caractères au moins.';
  if (typeof input.message === 'string' && input.message.trim().length > 2000) errors.message = 'Votre message dépasse 2000 caractères.';
  if (!data.consentement) errors.consentement = 'Cochez cette case pour que nous puissions vous répondre.';

  return Object.keys(errors).length ? { errors } : { data };
}

export function rateLimited(ip, now = Date.now()) {
  const list = (hits.get(ip) || []).filter((ts) => now - ts < RATE_LIMIT.windowMs);
  list.push(now);
  hits.set(ip, list);
  if (hits.size > 5000) hits.clear();
  return list.length > RATE_LIMIT.max;
}

export function buildEmail(data) {
  const profil = PROFILS[data.profil];
  const objet = OBJETS[data.objet];
  // Convention ELAZYO des objets internes : « [Site ELAZYO] Contact — Nom (Profil) »
  const subject = `[Site ELAZYO] ${objet} — ${data.nom} (${profil})`.slice(0, 150);
  const rows = [
    ['Nom', data.nom],
    ['E-mail', data.email],
    ['Profil', profil],
    ['Club', data.club || '—'],
    ['Objet', objet],
    ['Consentement', 'Oui, pour répondre à la demande'],
  ];
  const text = `${rows.map(([k, v]) => `${k} : ${v}`).join('\n')}\n\nMessage :\n${data.message}\n`;
  const html = `<!doctype html><html lang="fr"><body style="font-family:Arial,sans-serif;color:#09131D">
<h1 style="font-size:18px">Nouvelle demande depuis le site</h1>
<table style="border-collapse:collapse">${rows.map(([k, v]) => `<tr><td style="padding:4px 12px 4px 0;color:#4A5A6A">${escapeHtml(k)}</td><td style="padding:4px 0"><strong>${escapeHtml(v)}</strong></td></tr>`).join('')}</table>
<p style="margin-top:16px;white-space:pre-wrap;border-left:4px solid #CCF709;padding-left:12px">${escapeHtml(data.message)}</p>
<p style="color:#4A5A6A;font-size:12px">« Répondre » écrit directement à la personne.</p>
</body></html>`;
  return { subject, text, html };
}

async function deliver(data, env, fetchImpl) {
  if (env.CONTACT_FORWARD_URL) {
    const headers = { 'Content-Type': 'application/json' };
    if (env.CONTACT_FORWARD_TOKEN) headers.Authorization = `Bearer ${env.CONTACT_FORWARD_TOKEN}`;
    const res = await fetchImpl(env.CONTACT_FORWARD_URL, {
      method: 'POST',
      headers,
      body: JSON.stringify({ ...data, profilLibelle: PROFILS[data.profil], objetLibelle: OBJETS[data.objet], source: 'site' }),
    });
    if (!res.ok) throw new Error(`forward_${res.status}`);
    return;
  }
  if (env.RESEND_API_KEY && env.CONTACT_TO && env.CONTACT_FROM) {
    const mail = buildEmail(data);
    const res = await fetchImpl('https://api.resend.com/emails', {
      method: 'POST',
      headers: { Authorization: `Bearer ${env.RESEND_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({
        from: env.CONTACT_FROM,
        to: env.CONTACT_TO.split(',').map((s) => s.trim()).filter(Boolean),
        reply_to: data.email,
        subject: mail.subject,
        text: mail.text,
        html: mail.html,
      }),
    });
    if (!res.ok) throw new Error(`resend_${res.status}`);
    return;
  }
  const err = new Error('not_configured');
  err.code = 'not_configured';
  throw err;
}

function allowedOrigin(origin, env) {
  if (!origin) return true; // requête sans en-tête Origin (formulaire HTML ancien, outils) : acceptée
  const list = (env.ALLOWED_ORIGINS || 'https://www.elazyo.fr,https://elazyo.fr').split(',').map((s) => s.trim());
  return list.includes(origin);
}

function wantsHtml(req) {
  const accept = String(req.headers.accept || '');
  return accept.includes('text/html') && !accept.includes('application/json');
}

function send(res, status, body) {
  res.statusCode = status;
  res.setHeader('Content-Type', 'application/json; charset=utf-8');
  res.setHeader('Cache-Control', 'no-store');
  res.end(JSON.stringify(body));
}

function redirect(res, location) {
  res.statusCode = 303;
  res.setHeader('Location', location);
  res.setHeader('Cache-Control', 'no-store');
  res.end();
}

async function readBody(req) {
  if (req.body !== undefined && req.body !== null && typeof req.body === 'object' && !Buffer.isBuffer(req.body)) return req.body;
  let raw = typeof req.body === 'string' ? req.body : Buffer.isBuffer(req.body) ? req.body.toString('utf8') : '';
  if (!raw && typeof req.on === 'function') {
    raw = await new Promise((resolve, reject) => {
      let size = 0;
      const chunks = [];
      req.on('data', (chunk) => {
        size += chunk.length;
        if (size > MAX_BODY_BYTES) { reject(Object.assign(new Error('too_large'), { code: 'too_large' })); return; }
        chunks.push(chunk);
      });
      req.on('end', () => resolve(Buffer.concat(chunks).toString('utf8')));
      req.on('error', reject);
    });
  }
  if (raw.length > MAX_BODY_BYTES) throw Object.assign(new Error('too_large'), { code: 'too_large' });
  const type = String(req.headers['content-type'] || '');
  if (type.includes('application/x-www-form-urlencoded')) return Object.fromEntries(new URLSearchParams(raw));
  return raw ? JSON.parse(raw) : {};
}

export default async function handler(req, res, { env = process.env, fetchImpl = globalThis.fetch, now = Date.now() } = {}) {
  if (req.method !== 'POST') {
    res.setHeader('Allow', 'POST');
    return send(res, 405, { ok: false, error: 'method_not_allowed' });
  }
  if (!allowedOrigin(req.headers.origin, env)) return send(res, 403, { ok: false, error: 'forbidden_origin' });

  const html = wantsHtml(req);
  let input;
  try {
    input = await readBody(req);
  } catch (error) {
    return send(res, error.code === 'too_large' ? 413 : 400, { ok: false, error: error.code === 'too_large' ? 'too_large' : 'bad_request' });
  }

  const ip = String(req.headers['x-forwarded-for'] || req.socket?.remoteAddress || 'inconnu').split(',')[0].trim();
  if (rateLimited(ip, now)) return send(res, 429, { ok: false, error: 'rate_limited' });

  const result = validate(input, now);
  if (result.spam) return html ? redirect(res, '/merci.html') : send(res, 200, { ok: true });
  if (result.errors) return html ? redirect(res, '/?erreur=formulaire#contact') : send(res, 422, { ok: false, errors: result.errors });

  try {
    await deliver(result.data, env, fetchImpl);
  } catch (error) {
    console.error('[contact] envoi impossible :', error.message);
    const status = error.code === 'not_configured' ? 503 : 502;
    return html ? redirect(res, '/?erreur=envoi#contact') : send(res, status, { ok: false, error: error.code === 'not_configured' ? 'not_configured' : 'delivery_failed' });
  }
  return html ? redirect(res, '/merci.html') : send(res, 200, { ok: true });
}
