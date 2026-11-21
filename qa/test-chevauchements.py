import asyncio, json, sys
from playwright.async_api import async_playwright
WIDTHS=[int(x) for x in sys.argv[1].split(',')] if len(sys.argv)>1 else [360,390,414,600,768,900,1024,1180,1280,1366,1440,1920]
JS=r"""
(() => {
  const EXCL = '.scene, .mur, .valeurs, .deck, .hcard, .sr-only, .skip-link, [hidden], .spots, .phone__screen, .card, .mobile-menu, script, style, svg';
  const W = innerWidth;
  const vis = el => { for (let e=el; e && e!==document.body; e=e.parentElement){ const cs=getComputedStyle(e); if (cs.display==='none'||cs.visibility==='hidden'||parseFloat(cs.opacity)===0) return false; } return true; };
  const boxes=[]; const issues=[];
  const walker=document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
  let n; const owners=new Map();
  while((n=walker.nextNode())){
    if(!n.nodeValue.trim()) continue;
    const el=n.parentElement; if(!el||el.closest(EXCL)) continue;
    const det=el.closest('details'); if(det && !det.open && !el.closest('summary')) continue;
    if(!vis(el)) continue;
    const r=document.createRange(); r.selectNodeContents(n);
    for(const rc of r.getClientRects()){ if(rc.width<2||rc.height<2) continue;
      boxes.push({el, x:rc.left, y:rc.top+scrollY, r:rc.right, b:rc.bottom+scrollY, t:n.nodeValue.trim().slice(0,40)}); }
  }
  const path=el=>{const p=[];for(let e=el;e&&e!==document.body&&p.length<3;e=e.parentElement){p.unshift(e.tagName.toLowerCase()+(e.classList.length?'.'+[...e.classList].slice(0,2).join('.'):''));}return p.join('>')};
  // overlaps between text of different elements
  boxes.sort((a,b)=>a.y-b.y);
  for(let i=0;i<boxes.length;i++){ const A=boxes[i];
    for(let j=i+1;j<boxes.length;j++){ const B=boxes[j]; if(B.y>A.b) break;
      if(A.el===B.el) continue;
      const hA=A.el.closest('h1,h2,h3,h4,p,li,a,button,summary'), hB=B.el.closest('h1,h2,h3,h4,p,li,a,button,summary'); if(hA&&hA===hB) continue;
      const ox=Math.min(A.r,B.r)-Math.max(A.x,B.x), oy=Math.min(A.b,B.b)-Math.max(A.y,B.y);
      if(ox>2&&oy>3){ issues.push(['overlap', path(A.el)+' «'+A.t+'»', path(B.el)+' «'+B.t+'»', Math.round(ox)+'x'+Math.round(oy)]); }
    }
  }
  // text going outside viewport
  for(const B of boxes){ if(B.r>W+1||B.x<-1) issues.push(['offscreen', path(B.el)+' «'+B.t+'»', Math.round(B.x)+'..'+Math.round(B.r)]); }
  // clipped containers
  for(const el of document.querySelectorAll('body *')){
    if(el.closest(EXCL)) continue;
    const cs=getComputedStyle(el);
    if((cs.overflowX==='hidden'||cs.overflowX==='clip') && el.scrollWidth>el.clientWidth+2 && vis(el) && el.innerText && el.innerText.trim() && !['auto','scroll'].includes(cs.overflowX)){
      // only report if text inside is cut
      const r=el.getBoundingClientRect(); let cut=false;
      for(const B of boxes){ if(el.contains(B.el) && (B.r>r.right+1 || B.x<r.left-1) && B.b>r.top+scrollY && B.y<r.bottom+scrollY){cut=true;break;} }
      if(cut) issues.push(['clipped', path(el)]);
    }
  }
  return [document.documentElement.scrollWidth, issues];
})()
"""
async def main():
    async with async_playwright() as p:
        b=await p.chromium.launch()
        total=0
        for w in WIDTHS:
            pg=await b.new_page(viewport={"width":w,"height":900})
            await pg.goto("http://127.0.0.1:8080/",wait_until="networkidle")
            await pg.add_style_tag(content="[data-reveal],[data-reveal] *,.line>span{opacity:1!important;transform:none!important;clip-path:none!important;animation:none!important;transition:none!important}")
            await pg.wait_for_timeout(2500)
            sw,iss=await pg.evaluate(JS)
            seen=set(); out=[]
            for it in iss:
                k=json.dumps(it[:3]); 
                if k in seen: continue
                seen.add(k); out.append(it)
            total+=len(out)
            print(f"== {w}px scrollWidth={sw} issues={len(out)}")
            for it in out[:40]: print('  ',' | '.join(map(str,it)))
            await pg.close()
        print('TOTAL',total)
        await b.close()
asyncio.run(main())
