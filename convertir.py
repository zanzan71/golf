#!/usr/bin/env python3
"""Génère les parties de la page d'accueil du thème WordPress ELAZYO
à partir de la page statique (elazyo-site/index.html).

Chaque section devient template-parts/home/<nom>.php. Les chemins des images
passent par le thème, les liens vers l'appli et les pages légales par les
réglages, et les textes modifiables par elazyo_mod().

Usage : python3 convertir.py
"""
import re
from pathlib import Path

SRC = Path('/home/claude/elazyo-site/index.html')
THEME = Path('/home/claude/elazyo-wp/elazyo')
html = SRC.read_text(encoding='utf-8')

SECTIONS = [
    ('hero', '  <!-- 02 · HERO -->'),
    ('manifeste', '  <!-- 03 · MANIFESTE -->'),
    ('maillons', '  <!-- 04 · CHAQUE MAILLON COMPTE -->'),
    ('application', '  <!-- 05 · L’APPLICATION -->'),
    ('profils', '  <!-- 06 · LES PROFILS (fusion avec les quatre univers) -->'),
    ('mycard', '  <!-- 07 · LE JOUEUR : MY CARD -->'),
    ('communaute', '  <!-- 08 · COMMUNAUTÉ -->'),
    ('vision', '  <!-- 09 · NOTRE VISION -->'),
    ('fondateurs', '  <!-- CLUBS FONDATEURS -->'),
    ('faq', '  <!-- FAQ -->'),
    ('contact', '  <!-- 10 · CONTACT -->'),
]
END = '</main>'


def must_replace(s, old, new, count=1):
    n = s.count(old)
    if n != count:
        raise SystemExit(f'Attendu {count} occurrence(s), trouvé {n} : {old[:90]!r}')
    return s.replace(old, new)


def common(s):
    s = re.sub(r'(src|srcset)="assets/', r'\1="<?php echo $a; ?>', s)
    s = re.sub(r', assets/', ', <?php echo $a; ?>', s)
    s = s.replace('href="https://app.elazyo.fr/"', 'href="<?php echo esc_url( elazyo_mod( \'app_url\' ) ); ?>"')
    s = s.replace('href="confidentialite.html"', 'href="<?php echo esc_url( elazyo_page_url( \'confidentialite\' ) ); ?>"')
    s = s.replace('href="mentions-legales.html"', 'href="<?php echo esc_url( elazyo_page_url( \'mentions-legales\' ) ); ?>"')
    if 'assets/' in s.replace('<?php echo $a; ?>', ''):
        raise SystemExit('Chemin assets/ non converti')
    return s


def hero(s):
    s = must_replace(s,
        '<span class="chev" aria-hidden="true"></span>L’appli des clubs de foot amateur</p>',
        '<span class="chev" aria-hidden="true"></span><?php echo esc_html( elazyo_mod( \'hero_label\' ) ); ?></p>')
    s = must_replace(s, '''          <span class="line"><span>Le sport nous</span></span>
          <span class="line"><span>rassemble.</span></span>
          <span class="line"><span>ELAZYO nous</span></span>
          <span class="line"><span>fait <em class="accent">grandir.</em></span></span>
''', '''          <?php foreach ( elazyo_hero_lines() as $line ) : ?>
          <span class="line"><span><?php echo $line; // Échappé dans elazyo_hero_lines(). ?></span></span>
          <?php endforeach; ?>
''')
    s = must_replace(s,
        'data-hero-step="1">Convocations, présences, covoiturage, licences&nbsp;: ELAZYO simplifie la vie des coachs et des présidents, et transforme chaque saison en aventure pour les joueurs.</p>',
        'data-hero-step="1"><?php echo elazyo_nbsp( esc_html( elazyo_mod( \'hero_lead\' ) ) ); ?></p>')
    s = must_replace(s,
        'rel="noopener">Essayer gratuitement<svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>\n          <a class="btn btn--ghost btn--lg" href="#application"><svg class="icon icon--fill" aria-hidden="true"><use href="#i-play"/></svg>Voir l’appli en action</a>',
        'rel="noopener"><?php echo esc_html( elazyo_mod( \'hero_cta\' ) ); ?><svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>\n          <a class="btn btn--ghost btn--lg" href="#application"><svg class="icon icon--fill" aria-hidden="true"><use href="#i-play"/></svg><?php echo esc_html( elazyo_mod( \'hero_cta2\' ) ); ?></a>')
    s = must_replace(s, '''          <li>Gratuit pour les joueurs</li>
          <li>Web, Android et iPhone</li>
          <li>Conçu par des gens du foot amateur</li>
''', '''          <?php foreach ( array( 'hero_proof1', 'hero_proof2', 'hero_proof3' ) as $key ) : if ( elazyo_mod( $key ) ) : ?>
          <li><?php echo esc_html( elazyo_mod( $key ) ); ?></li>
          <?php endif; endforeach; ?>
''')
    return s


def fondateurs(s):
    s = must_replace(s, 'data-count-to="10">10</span>',
        'data-count-to="<?php echo esc_attr( elazyo_mod( \'fondateurs_places\' ) ); ?>"><?php echo esc_html( elazyo_mod( \'fondateurs_places\' ) ); ?></span>')
    s = must_replace(s, 'jusqu’au <strong>30 juin 2027</strong></p>',
        'jusqu’au <strong><?php echo esc_html( elazyo_mod( \'fondateurs_date\' ) ); ?></strong></p>')
    s = must_replace(s, 'L’appli gratuite pour tout le club jusqu’au 30 juin 2027</li>',
        'L’appli gratuite pour tout le club jusqu’au <?php echo esc_html( elazyo_mod( \'fondateurs_date\' ) ); ?></li>')
    a = s.index('        <figure class="preuve" data-reveal>')
    b = s.index('        </figure>', a) + len('        </figure>')
    s = s[:a] + "        <?php get_template_part( 'template-parts/home/preuve' ); ?>" + s[b:]
    return s


def faq(s):
    a = s.index('      <div class="faq__list">')
    b = s.index('    </div>\n  </section>', a)
    s = s[:a] + '''      <div class="faq__list">
        <?php foreach ( elazyo_faq_items() as $item ) : ?>
        <details class="qa" name="faq">
          <summary><?php echo elazyo_nbsp( esc_html( $item['question'] ) ); // Échappé. ?><svg class="icon" aria-hidden="true"><use href="#i-plus"/></svg></summary>
          <div class="qa__body"><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div>
        </details>
        <?php endforeach; ?>
      </div>
''' + s[b:]
    return s


def contact(s):
    s = must_replace(s, '<form class="form" action="/api/contact" method="post" novalidate data-contact-form>',
        '<form class="form" action="<?php echo esc_url( admin_url( \'admin-post.php?action=elazyo_contact\' ) ); ?>" method="post" novalidate data-contact-form>\n        <input type="hidden" name="action" value="elazyo_contact">')
    s = must_replace(s, '<p class="form__status" role="status" aria-live="polite" data-form-status></p>',
        '<?php $etat = isset( $_GET[\'demande\'] ) ? sanitize_key( wp_unslash( $_GET[\'demande\'] ) ) : \'\'; // phpcs:ignore WordPress.Security.NonceVerification ?>\n          <p class="form__status<?php echo $etat ? \' is-error\' : \'\'; ?>" role="status" aria-live="polite" data-form-status><?php echo esc_html( elazyo_contact_message( $etat ) ); ?></p>')
    return s


TRANSFORMS = {'hero': hero, 'fondateurs': fondateurs, 'faq': faq, 'contact': contact}

starts = [html.index(marker) for _, marker in SECTIONS]
end = html.index(END)
out_dir = THEME / 'template-parts' / 'home'
out_dir.mkdir(parents=True, exist_ok=True)
for i, (name, marker) in enumerate(SECTIONS):
    chunk = html[starts[i]: starts[i + 1] if i + 1 < len(SECTIONS) else end]
    chunk = chunk.replace(marker + '\n', '', 1).rstrip() + '\n'
    if name in TRANSFORMS:
        chunk = TRANSFORMS[name](chunk)
    chunk = common(chunk)
    head = ('<?php\n/**\n * Page d’accueil : section « ' + name + ' ».\n *\n'
            ' * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.\n *\n'
            ' * @package ELAZYO\n */\n\ndefined( \'ABSPATH\' ) || exit;\n')
    if '$a' in chunk:
        head += '$a = elazyo_asset_base();\n'
    head += '?>\n'
    (out_dir / f'{name}.php').write_text(head + chunk, encoding='utf-8')
    print(f'{name:12s} {len(chunk):6d} octets')

# Sprite d'icônes, partagé par toutes les pages.
a = html.index('<svg class="sprite"')
b = html.index('</svg>\n', html.index('i-big-chevron')) + len('</svg>\n')
(THEME / 'template-parts' / 'sprite.php').write_text(
    "<?php\n/**\n * Icônes du site (sprite SVG en ligne).\n *\n * @package ELAZYO\n */\n\ndefined( 'ABSPATH' ) || exit;\n?>\n" + html[a:b],
    encoding='utf-8')
print('sprite ok')


# ---------------------------------------------------------------------------
# En-tête de la page d'accueil et pied de page.
# ---------------------------------------------------------------------------
a = html.index('<header class="site-header" data-header>')
b = html.index('</header>', a) + len('</header>')
h = html[a:b]
h = must_replace(h, '''  <div class="announce" id="annonce">''', '''  <?php if ( elazyo_mod( 'annonce_afficher' ) ) : ?>
  <div class="announce" id="annonce">''')
h = must_replace(h, '''<span class="announce__text"><span class="announce__long"><strong>Clubs Fondateurs</strong>&nbsp;: 10 places, ELAZYO gratuit pour tout le club jusqu’au 30 juin 2027.</span><span class="announce__short"><strong>Clubs Fondateurs</strong>&nbsp;: 10 places</span></span>''',
    '''<span class="announce__text"><span class="announce__long"><?php echo elazyo_announce_text(); // Échappé dans la fonction. ?></span><span class="announce__short"><?php echo elazyo_announce_text( true ); // Échappé dans la fonction. ?></span></span>''')
h = must_replace(h, '''    </p>
  </div>
  <div class="site-header__bar">''', '''    </p>
  </div>
  <?php endif; ?>
  <div class="site-header__bar">''')
h = must_replace(h, '''        <li><a href="#faq">Questions</a></li>''', '''        <li><a href="#faq">Questions</a></li>
        <?php if ( elazyo_news_url() ) : ?><li><a href="<?php echo esc_url( elazyo_news_url() ); ?>">Actualités</a></li><?php endif; ?>''')
h = h.replace('src="assets/', 'src="<?php echo $a; ?>')
h = h.replace('href="https://app.elazyo.fr/"', 'href="<?php echo esc_url( elazyo_mod( \'app_url\' ) ); ?>"')
assert 'assets/' not in h.replace('<?php echo $a; ?>', '')
(THEME / 'template-parts' / 'header-home.php').write_text(
    "<?php\n/**\n * En-tête de la page d’accueil : bandeau Clubs Fondateurs, barre de navigation, menu mobile.\n *\n * @package ELAZYO\n */\n\ndefined( 'ABSPATH' ) || exit;\n$a = elazyo_asset_base();\n?>\n" + h + '\n',
    encoding='utf-8')
print('header-home ok')

a = html.index('<footer class="site-footer theme-night">')
b = html.index('</footer>', a) + len('</footer>')
f = html[a:b]
for anchor in ['application', 'profils', 'my-card', 'clubs-fondateurs', 'faq', 'contact']:
    f = f.replace(f'href="#{anchor}"', f'href="<?php echo esc_url( elazyo_anchor( \'{anchor}\' ) ); ?>"')
f = must_replace(f, '''          <li><a class="link" href="<?php echo esc_url( elazyo_anchor( 'contact' ) ); ?>">Contact</a></li>''',
    '''          <?php if ( elazyo_news_url() ) : ?><li><a class="link" href="<?php echo esc_url( elazyo_news_url() ); ?>">Actualités</a></li><?php endif; ?>
          <li><a class="link" href="<?php echo esc_url( elazyo_anchor( 'contact' ) ); ?>">Contact</a></li>''')
f = must_replace(f, '<p>© 2026 ELAZYO</p>', '<p>© <?php echo esc_html( wp_date( \'Y\' ) ); ?> ELAZYO</p>')
f = f.replace('src="assets/', 'src="<?php echo $a; ?>')
f = f.replace('href="https://app.elazyo.fr/"', 'href="<?php echo esc_url( elazyo_mod( \'app_url\' ) ); ?>"')
f = f.replace('href="confidentialite.html"', 'href="<?php echo esc_url( elazyo_page_url( \'confidentialite\' ) ); ?>"')
f = f.replace('href="mentions-legales.html"', 'href="<?php echo esc_url( elazyo_page_url( \'mentions-legales\' ) ); ?>"')
assert 'assets/' not in f.replace('<?php echo $a; ?>', '') and '.html' not in f
(THEME / 'template-parts' / 'site-footer.php').write_text(
    "<?php\n/**\n * Pied de page, commun à toutes les pages.\n *\n * @package ELAZYO\n */\n\ndefined( 'ABSPATH' ) || exit;\n$a = elazyo_asset_base();\n?>\n" + f + '\n',
    encoding='utf-8')
print('footer ok')
