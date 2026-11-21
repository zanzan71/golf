import asyncio
from playwright.async_api import async_playwright
async def main():
    async with async_playwright() as p:
        b=await p.chromium.launch()
        for w in [360,390,768,1024,1100,1180,1280,1366,1440,1920]:
            pg=await b.new_page(viewport={"width":w,"height":900})
            await pg.goto("http://127.0.0.1:8080/",wait_until="networkidle")
            await pg.add_style_tag(content=".line>span{transform:none!important;animation:none!important}")
            r=await pg.evaluate("""(()=>{const o=[];for(const l of document.querySelectorAll('.display .line > span')){const fs=parseFloat(getComputedStyle(l).fontSize);const h=l.getBoundingClientRect().height;if(h>fs*1.6 && l.closest('.display--hero'))o.push(l.textContent.trim()+' h='+Math.round(h)+' fs='+Math.round(fs)+' '+getComputedStyle(l).display)}return o})()""")
            print(w, r if r else 'ok')
            await pg.close()
        await b.close()
asyncio.run(main())
