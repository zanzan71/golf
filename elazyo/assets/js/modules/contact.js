// Formulaire de contact : validation côté navigateur (la vraie validation est
// refaite côté serveur), envoi à /api/contact, ou, dans l'aperçu publié sur
// claude.ai, enregistrement dans la base de la page.

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const PROFILS = ['president', 'coach', 'tresorerie', 'parent', 'benevole', 'partenaire', 'autre'];
const OBJETS = ['club-fondateur', 'demo', 'question', 'partenariat'];
const FIELDS = ['nom', 'email', 'profil', 'objet', 'message', 'consentement'];

const TEXT = {
  nomVide: 'Indiquez votre nom et prénom.',
  nomCourt: 'Votre nom doit compter 2 caractères au moins.',
  emailVide: 'Indiquez votre adresse e-mail.',
  emailInvalide: 'Cette adresse e-mail n’est pas valide. Exemple : prenom@club.fr',
  profil: 'Choisissez votre profil.',
  objet: 'Choisissez l’objet de votre demande.',
  messageVide: 'Écrivez votre message.',
  messageCourt: 'Votre message doit compter 10 caractères au moins.',
  messageLong: 'Votre message dépasse 2000 caractères.',
  consentement: 'Cochez cette case pour que nous puissions vous répondre.',
  envoi: 'Envoi en cours…',
  bouton: 'Envoyer ma demande',
  succesApi: (email) => `Merci, votre demande est envoyée. Nous vous répondons à ${email}.`,
  succesApercu: 'Merci, votre demande est enregistrée dans la base de cet aperçu. Sur le site en ligne, elle part par e-mail à l’équipe ELAZYO.',
  erreurReseau: 'L’envoi n’a pas abouti. Vérifiez votre connexion, puis réessayez.',
  erreurLimite: 'Trop de demandes envoyées depuis cette connexion. Réessayez dans quelques minutes.',
  erreurConfig: 'L’envoi en ligne n’est pas encore activé. Réessayez un peu plus tard.',
  erreurApercu: 'Cet aperçu ne peut pas enregistrer votre demande avec ce compte. Ouvrez la page en étant connecté à claude.ai.',
  aCorriger: (n) => (n === 1 ? 'Un champ est à corriger.' : `${n} champs sont à corriger.`),
};

export function initContact() {
  const form = document.querySelector('[data-contact-form]');
  if (!form) return;
  const status = form.querySelector('[data-form-status]');
  const submit = form.querySelector('[data-submit]');
  const submitLabel = form.querySelector('[data-submit-label]');
  const counter = form.querySelector('[data-count]');
  const time = form.querySelector('[data-form-time]');
  if (time) time.value = String(Date.now());

  const el = (name) => form.elements.namedItem(name);
  let attempted = false;

  el('message').addEventListener('input', () => {
    counter.textContent = String(el('message').value.length);
  });

  const validate = (name) => {
    const v = name === 'consentement' ? el(name).checked : el(name).value.trim();
    switch (name) {
      case 'nom': return !v ? TEXT.nomVide : v.length < 2 ? TEXT.nomCourt : '';
      case 'email': return !v ? TEXT.emailVide : !EMAIL_RE.test(v) || v.length > 254 ? TEXT.emailInvalide : '';
      case 'profil': return PROFILS.includes(v) ? '' : TEXT.profil;
      case 'objet': return OBJETS.includes(v) ? '' : TEXT.objet;
      case 'message': return !v ? TEXT.messageVide : v.length < 10 ? TEXT.messageCourt : v.length > 2000 ? TEXT.messageLong : '';
      case 'consentement': return v ? '' : TEXT.consentement;
      default: return '';
    }
  };

  const showError = (name, message) => {
    const input = el(name);
    const out = form.querySelector(`[data-error-for="${name}"]`);
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
    if (out) out.textContent = message;
  };

  FIELDS.forEach((name) => {
    const input = el(name);
    const evt = input.type === 'checkbox' || input.tagName === 'SELECT' ? 'change' : 'blur';
    input.addEventListener(evt, () => { if (attempted) showError(name, validate(name)); });
    if (evt === 'blur') input.addEventListener('input', () => {
      if (attempted && input.getAttribute('aria-invalid') === 'true') showError(name, validate(name));
    });
  });

  const setStatus = (kind, message) => {
    status.classList.toggle('is-success', kind === 'success');
    status.classList.toggle('is-error', kind === 'error');
    status.textContent = message;
  };

  const setSending = (sending) => {
    form.classList.toggle('is-sending', sending);
    submit.setAttribute('aria-disabled', String(sending));
    submitLabel.textContent = sending ? TEXT.envoi : TEXT.bouton;
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (form.classList.contains('is-sending')) return;
    attempted = true;
    const errors = FIELDS.map((name) => [name, validate(name)]);
    errors.forEach(([name, message]) => showError(name, message));
    const invalid = errors.filter(([, message]) => message);
    if (invalid.length) {
      setStatus('error', TEXT.aCorriger(invalid.length));
      el(invalid[0][0]).focus();
      return;
    }

    const data = {
      nom: el('nom').value.trim(),
      email: el('email').value.trim(),
      profil: el('profil').value,
      club: el('club').value.trim(),
      objet: el('objet').value,
      message: el('message').value.trim(),
      consentement: el('consentement').checked,
      site_web: el('site_web').value,
      t: Number(time?.value) || 0,
    };

    setSending(true);
    setStatus('', '');
    try {
      const result = await send(form, data);
      if (result.errors) {
        Object.entries(result.errors).forEach(([name, message]) => { if (el(name)) showError(name, message); });
        setStatus('error', TEXT.aCorriger(Object.keys(result.errors).length));
        return;
      }
      const email = data.email;
      form.reset();
      counter.textContent = '0';
      if (time) time.value = String(Date.now());
      attempted = false;
      FIELDS.forEach((name) => showError(name, ''));
      setStatus('success', result.mode === 'preview' ? TEXT.succesApercu : TEXT.succesApi(email));
    } catch (error) {
      const code = error?.message;
      setStatus('error',
        code === 'rate_limited' ? TEXT.erreurLimite
          : code === 'not_configured' ? TEXT.erreurConfig
            : code === 'preview_unavailable' ? TEXT.erreurApercu
              : TEXT.erreurReseau);
    } finally {
      setSending(false);
    }
  });
}

async function send(form, data) {
  // Champ piège rempli : un robot. On ne transmet rien et on ne lui dit rien.
  if (data.site_web) return { ok: true, mode: 'ignored' };
  const runtime = window.claude;
  if (runtime && typeof runtime.use === 'function') return sendToPreview(runtime, data);
  return sendToApi(form.getAttribute('action') || '/api/contact', data);
}

async function sendToApi(url, data) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), 12000);
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(data),
      signal: controller.signal,
      credentials: 'same-origin',
    });
    const body = await response.json().catch(() => ({}));
    if (response.ok && body.ok) return { ok: true, mode: 'api' };
    if (response.status === 422 && body.errors) return { ok: false, errors: body.errors };
    throw new Error(body.error || `http_${response.status}`);
  } finally {
    clearTimeout(timer);
  }
}

// Aperçu claude.ai : la demande est rangée dans la base de la page, sous le
// sous-dossier de la personne connectée (règles « boîte à idées »).
async function sendToPreview(runtime, data) {
  const withTimeout = (promise, ms = 10000) => Promise.race([
    promise,
    new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), ms)),
  ]);
  const db = await withTimeout(runtime.use('db'));
  const user = await withTimeout(runtime.use('user'));
  const uid = user && typeof user.id === 'function' ? await withTimeout(user.id()) : null;
  if (!db || !uid) throw new Error('preview_unavailable');
  const { site_web: _hp, t: _t, ...demande } = data;
  try {
    await withTimeout(db.collection(`demandes/${uid}/envois`).add({
      ...demande,
      envoyeeLe: new Date().toISOString(),
      source: 'apercu',
    }));
  } catch (error) {
    if (error?.code === 'invalid_argument' || error?.code === 'not_granted') throw new Error('preview_unavailable');
    throw error;
  }
  return { ok: true, mode: 'preview' };
}
