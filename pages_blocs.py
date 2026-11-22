"""Convertit le contenu des pages légales statiques en blocs WordPress (inc/pages/*.html)."""
import re
from html.parser import HTMLParser
from pathlib import Path

SRC = Path('/home/claude/elazyo-site')
OUT = Path('/home/claude/elazyo-wp/elazyo/inc/pages')

LINKS = {
    'index.html#contact': '{contact}', 'confidentialite.html': '{confidentialite}',
    'mentions-legales.html': '{mentions}', 'index.html': '{accueil}', 'https://app.elazyo.fr/': '{app}',
}
FIXES = {
    'Le joueur de la carte MY CARD est un personnage d’illustration.': 'La carte MY CARD présentée sur ce site montre la mascotte ELAZYO.',
    '[nom de l’hébergeur, raison sociale, adresse et téléphone : par exemple Vercel Inc., 440 N Barranca Ave #4133, Covina, CA 91723, États-Unis, si le site est déployé sur Vercel]':
        '[nom de l’hébergeur, raison sociale, adresse et téléphone, par exemple o2switch ou OVHcloud]',
    'l’hébergeur du site <mark class="todo">[nom]</mark> et le service d’envoi d’e-mails <mark class="todo">[nom, par exemple Resend]</mark>.':
        'l’hébergeur du site <mark class="todo">[nom]</mark>, qui conserve aussi les demandes dans l’administration du site, et le service d’envoi d’e-mails <mark class="todo">[nom, si un service d’envoi est utilisé]</mark>.',
}


def body(path):
    html = path.read_text(encoding='utf-8')
    a = html.index('<div class="container legal__body">') + len('<div class="container legal__body">')
    b = html.index('</div>\n  </section>', a)
    inner = html[a:b]
    inner = re.sub(r'<h1[^>]*>.*?</h1>', '', inner, flags=re.S)
    inner = re.sub(r'<span class="todo">(.*?)</span>', r'<mark class="todo">\1</mark>', inner)
    for old, new in LINKS.items():
        inner = inner.replace(f'href="{old}"', f'href="{new}"')
    for old, new in FIXES.items():
        inner = inner.replace(old, new)
    return inner


def to_blocks(inner):
    out = []
    for m in re.finditer(r'<(h2|p|ul)([^>]*)>(.*?)</\1>', inner, flags=re.S):
        tag, attrs, content = m.group(1), m.group(2), m.group(3).strip()
        if tag == 'h2':
            out.append(f'<!-- wp:heading -->\n<h2 class="wp-block-heading">{content}</h2>\n<!-- /wp:heading -->')
        elif tag == 'p':
            if 'btn' in content:
                href = re.search(r'href="([^"]+)"', content).group(1)
                label = re.sub(r'<[^>]+>', '', content).strip()
                out.append('<!-- wp:buttons -->\n<div class="wp-block-buttons"><!-- wp:button -->\n'
                           f'<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{href}">{label}</a></div>\n'
                           '<!-- /wp:button --></div>\n<!-- /wp:buttons -->')
            else:
                out.append(f'<!-- wp:paragraph -->\n<p>{content}</p>\n<!-- /wp:paragraph -->')
        else:
            items = re.findall(r'<li>(.*?)</li>', content, flags=re.S)
            lis = ''.join(f'<!-- wp:list-item -->\n<li>{i.strip()}</li>\n<!-- /wp:list-item -->' for i in items)
            out.append(f'<!-- wp:list -->\n<ul class="wp-block-list">{lis}</ul>\n<!-- /wp:list -->')
    return '\n\n'.join(out) + '\n'


for name in ['mentions-legales', 'confidentialite', 'merci']:
    inner = body(SRC / f'{name}.html')
    blocks = to_blocks(inner)
    assert '.html' not in blocks, name
    (OUT / f'{name}.html').write_text(blocks, encoding='utf-8')
    print(name, len(blocks), blocks.count('wp:'))
