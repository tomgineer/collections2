(()=>{function m(){let e=document.querySelector("[data-js-search]");if(!e)return;let r=document.querySelector("[data-js-results]");if(!r)return;let t=document.querySelector("[data-js-search-clear]");if(!t)return;let n=document.querySelector('meta[name="base-url"]')?.content;if(!n)return;let c=2,i=250,a=null,l=null,o=s=>{r.classList.toggle("hidden",!s)},u=()=>{if(!t)return;let s=e.value.trim().length>0;t.classList.toggle("hidden",!s)};o(!1),u();let p=()=>{r.innerHTML="",o(!1)},b=s=>String(s??"").replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;").replaceAll('"',"&quot;").replaceAll("'","&#039;"),y=()=>{r.innerHTML='<div class="alert alert-error mt-4"><span>Search failed. Please try again.</span></div>',o(!1)},T=async s=>{l&&l.abort(),l=new AbortController;try{let d=s,h=await fetch(`${n}ajax/search?q=${encodeURIComponent(d)}`,{headers:{"X-Requested-With":"XMLHttpRequest"},signal:l.signal});if(!h.ok)throw new Error(`HTTP ${h.status}`);let S=await h.json();v(r,S,d,b,o)}catch(d){if(d.name==="AbortError")return;y()}};e.addEventListener("input",()=>{let s=e.value.trim();if(u(),a&&clearTimeout(a),s.length<c){l&&l.abort(),p();return}a=setTimeout(()=>{T(s)},i)}),t&&t.addEventListener("click",()=>{l&&l.abort(),a&&clearTimeout(a),e.value="",p(),u(),e.focus()})}function v(e,r,t,n,c){if(!Array.isArray(r)||r.length===0){e.innerHTML="",c(!1);return}c(!0);let i=r.map(a=>`
        <tr>
            <td>${f(a.creator,t,n)}</td>
            <td>${f(a.title,t,n)}</td>
            <td class="hidden md:table-cell">${f(a.collection,t,n)}</td>
            <td class="text-right">
                <span class="badge badge-secondary badge-sm font-heading font-bold">${n(a.type)}</span>
            </td>
        </tr>
    `).join("");e.innerHTML=`
        <div class="overflow-x-auto mt-4">
            <table class="table table-zebra lg:text-base">
                <thead>
                    <tr>
                        <th>Creator</th>
                        <th>Title</th>
                        <th class="hidden md:table-cell">Collection</th>
                        <th class="text-right">Type</th>
                    </tr>
                </thead>
                <tbody>${i}</tbody>
            </table>
        </div>
    `}function f(e,r,t){let n=String(e??""),c=String(r??"").trim();if(!c)return t(n);let i=c.replace(/[.*+?^${}()|[\]\\]/g,"\\$&"),a=new RegExp(`(${i})`,"gi");return n.split(a).map((o,u)=>u%2===1?`<mark class="bg-primary text-white px-1">${t(o)}</mark>`:t(o)).join("")}function g(){let e=document.querySelector("[data-js-search]");if(!e)return;let r=document.querySelectorAll("button[data-search-term]");r.length&&r.forEach(t=>{t.addEventListener("click",()=>{let n=t.dataset.searchTerm?.trim();n&&(e.value=n,e.dispatchEvent(new Event("input",{bubbles:!0})),e.focus())})})}m();g();})();
