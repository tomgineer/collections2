(()=>{function m(){let e=document.querySelector("[data-js-search]");if(!e)return;let t=document.querySelector("[data-js-results]");if(!t)return;let r=document.querySelector('meta[name="base-url"]')?.content;if(!r)return;let a=2,o=250,c=null,n=null,l=s=>{t.classList.toggle("hidden",!s)};l(!1);let i=()=>{t.innerHTML="",l(!1)},d=s=>String(s??"").replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;").replaceAll('"',"&quot;").replaceAll("'","&#039;"),g=()=>{t.innerHTML='<div class="alert alert-error mt-4"><span>Search failed. Please try again.</span></div>',l(!1)},b=async s=>{n&&n.abort(),n=new AbortController;try{let u=s,h=await fetch(`${r}ajax/search?q=${encodeURIComponent(u)}`,{headers:{"X-Requested-With":"XMLHttpRequest"},signal:n.signal});if(!h.ok)throw new Error(`HTTP ${h.status}`);let y=await h.json();T(t,y,u,d,l)}catch(u){if(u.name==="AbortError")return;g()}};e.addEventListener("input",()=>{let s=e.value.trim();if(c&&clearTimeout(c),s.length<a){n&&n.abort(),i();return}c=setTimeout(()=>{b(s)},o)})}function T(e,t,r,a,o){if(!Array.isArray(t)||t.length===0){e.innerHTML="",o(!1);return}o(!0);let c=t.map(n=>`
        <tr>
            <td>${f(n.creator,r,a)}</td>
            <td>${f(n.title,r,a)}</td>
            <td>${f(n.collection,r,a)}</td>
            <td class="text-right">
                <span class="badge badge-secondary badge-sm font-heading font-bold">${a(n.type)}</span>
            </td>
        </tr>
    `).join("");e.innerHTML=`
        <div class="overflow-x-auto mt-4">
            <table class="table table-zebra lg:text-base">
                <thead>
                    <tr>
                        <th>Creator</th>
                        <th>Title</th>
                        <th>Collection</th>
                        <th class="text-right">Type</th>
                    </tr>
                </thead>
                <tbody>${c}</tbody>
            </table>
        </div>
    `}function f(e,t,r){let a=String(e??""),o=String(t??"").trim();if(!o)return r(a);let c=o.replace(/[.*+?^${}()|[\]\\]/g,"\\$&"),n=new RegExp(`(${c})`,"gi");return a.split(n).map((i,d)=>d%2===1?`<mark class="bg-primary text-white px-1">${r(i)}</mark>`:r(i)).join("")}function p(){let e=document.querySelector("[data-js-search]");if(!e)return;let t=document.querySelectorAll("button[data-search-term]");t.length&&t.forEach(r=>{r.addEventListener("click",()=>{let a=r.dataset.searchTerm?.trim();a&&(e.value=a,e.dispatchEvent(new Event("input",{bubbles:!0})),e.focus())})})}m();p();})();
