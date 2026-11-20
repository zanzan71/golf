"""Prépare les images du site ELAZYO à partir des fichiers sources.

Usage :
    python3 scripts/preparer-images.py <dossier_sources> <dossier_site>

<dossier_sources> doit contenir :
    ecrans/*.png                   captures réelles de l'appli (branche elazyo-video-assets)
    mycard/corps.webp, tete.webp   joueur MY CARD (corps détouré 720 × 1660, tête 520 × 650)
    instagram/n2-01.jpg            visuel Instagram validé (sert à l'image de partage)
    logo_paths.json                tracés du logo (design system ELAZYO)
    video/covoiturage-je-conduis.mp4|webm

Le script écrit dans <dossier_site>/assets : écrans en AVIF + WebP (360 et 720 px),
joueur détouré, logos SVG, icônes, image de partage 1200 × 630.
Dépendance : Pillow (avec AVIF), ffmpeg pour l'affiche de la vidéo.
"""
import json
import shutil
import subprocess
import sys
from pathlib import Path

from PIL import Image

SRC = Path(sys.argv[1])
SITE = Path(sys.argv[2])
OUT = SITE / "assets"

NIGHT = "#09131D"
LIME = "#CCF709"
BLEU = "#01284F"
BLANC = "#FFFFFF"
NOIR_LOGO = "#0D0D0D"

# Les captures « Accueil, Planning, Gestion, Finance, Comm. » ont une barre d'onglets
# qui affiche encore l'ancien nom PLAYCARD : on la coupe (y >= 1784) et on prolonge le fond.
SCREEN_W, SCREEN_H = 1080, 2004
TAB_BAR_TOP = 1784
SCREENS = {
    "planning": True,
    "finance": True,
    "communication": True,
    "match": False,
    "covoiturage": False,
    "covoiturage-depart": False,
    "covoiturage-formulaire": False,
}


def save_variants(img: Image.Image, base: Path, widths, quality_webp=80, quality_avif=55):
    base.parent.mkdir(parents=True, exist_ok=True)
    for w in widths:
        h = round(img.height * w / img.width)
        r = img.resize((w, h), Image.LANCZOS)
        r.save(f"{base}-{w}.webp", "WEBP", quality=quality_webp, method=6)
        r.save(f"{base}-{w}.avif", "AVIF", quality=quality_avif, speed=4)


def prepare_screens():
    for name, has_tab_bar in SCREENS.items():
        im = Image.open(SRC / "ecrans" / f"{name}.png").convert("RGB")
        if has_tab_bar:
            content = im.crop((0, 0, SCREEN_W, TAB_BAR_TOP))
            bg = content.getpixel((SCREEN_W // 2, TAB_BAR_TOP - 4))
            canvas = Image.new("RGB", (SCREEN_W, SCREEN_H), bg)
            canvas.paste(content, (0, 0))
            im = canvas
        elif name != "match":
            im = im.crop((0, 0, SCREEN_W, SCREEN_H))
        # « match » garde sa hauteur (2550 px) : l'écran défile doucement dans le téléphone.
        save_variants(im, OUT / "img" / "ecrans" / name, (360, 720))
        print("écran", name, im.size)


def prepare_player():
    corps = Image.open(SRC / "mycard" / "corps.webp").convert("RGBA")
    tete = Image.open(SRC / "mycard" / "tete.webp").convert("RGBA")
    # Règle du dossier MY CARD : la tête se pose à 28,92 % du bord gauche, en haut,
    # sur 36,94 % de la largeur du corps (boîte au format de l'image de tête, 520 × 650).
    w = round(corps.width * 0.3694)
    h = round(w * tete.height / tete.width)
    x = round(corps.width * 0.2892)
    canvas = Image.new("RGBA", corps.size, (0, 0, 0, 0))
    canvas.alpha_composite(corps)
    canvas.alpha_composite(tete.resize((w, h), Image.LANCZOS), (x, 0))
    bbox = canvas.getbbox()
    player = canvas.crop(bbox)
    save_variants(player, OUT / "img" / "mycard" / "joueur", (380, 760), quality_webp=82, quality_avif=60)
    print("joueur", player.size)


def logo_svg(variant: str, paths: dict) -> str:
    letters, sil, lime, navy = paths["letters"], paths["sil"], paths["lime"], paths["navy"]
    if variant == "symbole":
        # Le « A » seul : même repère que le logo complet, cadré sur le symbole.
        body = (
            f'<path fill="{BLANC}" fill-rule="evenodd" d="{sil}"/>'
            f'<path fill="{LIME}" fill-rule="evenodd" d="{lime}"/>'
            f'<path fill="{BLEU}" fill-rule="evenodd" d="{navy}"/>'
        )
        return (
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="456.5 0 369.5 279.8" role="img" aria-label="ELAZYO">'
            f'<g transform="translate(-17.5 -11)">{body}</g></svg>'
        )
    ink = BLANC if variant == "fond-fonce" else NOIR_LOGO
    body = (
        f'<path fill="{ink}" fill-rule="evenodd" d="{letters}"/>'
        f'<path fill="{BLANC}" fill-rule="evenodd" d="{sil}"/>'
        f'<path fill="{LIME}" fill-rule="evenodd" d="{lime}"/>'
        f'<path fill="{BLEU}" fill-rule="evenodd" d="{navy}"/>'
    )
    return (
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1565 279.8" role="img" aria-label="ELAZYO">'
        f'<g transform="translate(-17.5 -11)">{body}</g></svg>'
    )


def icon_svg(paths: dict, size=1024) -> str:
    # Icône d'app : le symbole sur un carré night aux angles adoucis (22 % du côté).
    sym_w, sym_h = 369.5, 279.8
    scale = (size * 0.62) / sym_w
    tx = (size - sym_w * scale) / 2
    ty = (size - sym_h * scale) / 2 - size * 0.02
    r = size * 0.22
    return (
        f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {size} {size}">'
        f'<rect width="{size}" height="{size}" rx="{r:.1f}" fill="{NIGHT}"/>'
        f'<g transform="translate({tx:.2f} {ty:.2f}) scale({scale:.5f}) translate(-474 -11)">'
        f'<path fill="{BLANC}" fill-rule="evenodd" d="{paths["sil"]}"/>'
        f'<path fill="{LIME}" fill-rule="evenodd" d="{paths["lime"]}"/>'
        f'<path fill="{BLEU}" fill-rule="evenodd" d="{paths["navy"]}"/>'
        "</g></svg>"
    )


def prepare_logos():
    paths = json.loads((SRC / "logo_paths.json").read_text())
    logo_dir = OUT / "img" / "logo"
    logo_dir.mkdir(parents=True, exist_ok=True)
    for variant in ("fond-fonce", "fond-clair", "symbole"):
        (logo_dir / f"elazyo-{variant}.svg").write_text(logo_svg(variant, paths))
    icons = OUT / "icons"
    icons.mkdir(parents=True, exist_ok=True)
    (icons / "favicon.svg").write_text(icon_svg(paths))
    print("logos et favicon SVG écrits")


def prepare_og():
    slide = Image.open(SRC / "instagram" / "n2-01.jpg").convert("RGB")
    bg = slide.getpixel((540, 600))
    og = Image.new("RGB", (1200, 630), bg)
    chevrons = slide.crop((700, 0, 1080, 500))
    og.paste(chevrons, (1200 - chevrons.width, 0))
    headline = slide.crop((70, 755, 840, 1160))
    scale = 340 / headline.height
    headline = headline.resize((round(headline.width * scale), 340), Image.LANCZOS)
    og.paste(headline, (64, 176))
    logo = slide.crop((70, 80, 360, 135))
    og.paste(logo, (64, 64))
    site = slide.crop((70, 1228, 220, 1275))
    og.paste(site, (64, 548))
    signature = slide.crop((735, 1228, 1010, 1275))
    og.paste(signature, (1200 - 64 - signature.width, 548))
    og.save(OUT / "img" / "og-elazyo.jpg", "JPEG", quality=88, optimize=True, progressive=True)
    print("image de partage 1200 × 630")


def prepare_video():
    vdir = OUT / "video"
    vdir.mkdir(parents=True, exist_ok=True)
    for ext in ("mp4", "webm"):
        shutil.copyfile(SRC / "video" / f"covoiturage-je-conduis.{ext}", vdir / f"covoiturage.{ext}")
    poster_png = vdir / "covoiturage-poster.png"
    subprocess.run(
        ["ffmpeg", "-y", "-loglevel", "error", "-ss", "0", "-i", str(vdir / "covoiturage.mp4"),
         "-frames:v", "1", str(poster_png)],
        check=True,
    )
    poster = Image.open(poster_png).convert("RGB")
    poster.resize((360, round(poster.height * 360 / poster.width)), Image.LANCZOS).save(
        vdir / "covoiturage-poster.webp", "WEBP", quality=78, method=6
    )
    poster_png.unlink()
    print("vidéo copiée, affiche extraite")


if __name__ == "__main__":
    prepare_screens()
    prepare_player()
    prepare_logos()
    prepare_og()
    prepare_video()
