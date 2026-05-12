(()=>{function g(){let t=document.querySelector("[data-js-search]");if(!t)return;let r=document.querySelector("[data-js-results]");if(!r)return;let e=document.querySelector("[data-js-search-clear]");if(!e)return;let a=document.querySelector('meta[name="base-url"]')?.content;if(!a)return;let c=2,i=250,s=null,l=null,o=n=>{r.classList.toggle("hidden",!n)},u=()=>{if(!e)return;let n=t.value.trim().length>0;e.classList.toggle("hidden",!n)};o(!1),u();let m=()=>{r.innerHTML="",o(!1)},y=n=>String(n??"").replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;").replaceAll('"',"&quot;").replaceAll("'","&#039;"),x=()=>{r.innerHTML='<div class="alert alert-error mt-4"><span>Search failed. Please try again.</span></div>',o(!1)},b=async n=>{l&&l.abort(),l=new AbortController;try{let p=n,d=await fetch(`${a}ajax/search?q=${encodeURIComponent(p)}`,{headers:{"X-Requested-With":"XMLHttpRequest"},signal:l.signal});if(!d.ok)throw new Error(`HTTP ${d.status}`);let T=await d.json();S(r,T,p,y,o)}catch(p){if(p.name==="AbortError")return;x()}};t.addEventListener("input",()=>{let n=t.value.trim();if(u(),s&&clearTimeout(s),n.length<c){l&&l.abort(),m();return}s=setTimeout(()=>{b(n)},i)}),e&&e.addEventListener("click",()=>{l&&l.abort(),s&&clearTimeout(s),t.value="",m(),u(),t.focus()})}function S(t,r,e,a,c){if(!Array.isArray(r)||r.length===0){t.innerHTML="",c(!1);return}c(!0);let i=r.map(s=>`
        <tr>
            <td class="px-2 py-2 lg:px-4 lg:py-3">${f(s.creator,e,a)}</td>
            <td class="px-2 py-2 lg:px-4 lg:py-3">${f(s.title,e,a)}</td>
            <td class="px-2 py-2 lg:px-4 lg:py-3">
                <span class="font-heading font-bold">${a(s.category)}</span>
            </td>
            <td class="px-2 py-2 lg:px-4 lg:py-3">
                <span class="badge badge-primary badge-sm font-heading font-bold whitespace-nowrap">${a(s.format)}</span>
            </td>
        </tr>
    `).join("");t.innerHTML=`
        <div class="overflow-x-auto mt-4">
            <table class="table table-sm table-zebra lg:table-md lg:text-base">
                <thead>
                    <tr>
                        <th class="px-2 py-2 text-xs lg:px-4 lg:py-3 lg:text-sm">Artist</th>
                        <th class="px-2 py-2 text-xs lg:px-4 lg:py-3 lg:text-sm">Title</th>
                        <th class="px-2 py-2 text-xs lg:px-4 lg:py-3 lg:text-sm">Category</th>
                        <th class="px-2 py-2 text-xs lg:px-4 lg:py-3 lg:text-sm">Format</th>
                    </tr>
                </thead>
                <tbody>${i}</tbody>
            </table>
        </div>
    `}function f(t,r,e){let a=String(t??""),c=String(r??"").trim();if(!c)return e(a);let i=c.replace(/[.*+?^${}()|[\]\\]/g,"\\$&"),s=new RegExp(`(${i})`,"gi");return a.split(s).map((o,u)=>u%2===1?`<mark class="bg-primary text-white px-1">${e(o)}</mark>`:e(o)).join("")}function h(){let t=document.querySelector("[data-js-search]");if(!t)return;let r=document.querySelectorAll("button[data-search-term]");r.length&&r.forEach(e=>{e.addEventListener("click",()=>{let a=e.dataset.searchTerm?.trim();a&&(t.value=a,t.dispatchEvent(new Event("input",{bubbles:!0})),t.focus())})})}g();h();})();
