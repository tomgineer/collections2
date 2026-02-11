(()=>{function m(){let t=document.querySelector("[data-js-search]");if(!t)return;let r=document.querySelector("[data-js-results]");if(!r)return;let e=document.querySelector("[data-js-search-clear]");if(!e)return;let n=document.querySelector('meta[name="base-url"]')?.content;if(!n)return;let c=2,i=250,a=null,o=null,l=s=>{r.classList.toggle("hidden",!s)},u=()=>{if(!e)return;let s=t.value.trim().length>0;e.classList.toggle("hidden",!s)};l(!1),u();let p=()=>{r.innerHTML="",l(!1)},b=s=>String(s??"").replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;").replaceAll('"',"&quot;").replaceAll("'","&#039;"),y=()=>{r.innerHTML='<div class="alert alert-error mt-4"><span>Search failed. Please try again.</span></div>',l(!1)},T=async s=>{o&&o.abort(),o=new AbortController;try{let d=s,h=await fetch(`${n}ajax/search?q=${encodeURIComponent(d)}`,{headers:{"X-Requested-With":"XMLHttpRequest"},signal:o.signal});if(!h.ok)throw new Error(`HTTP ${h.status}`);let S=await h.json();v(r,S,d,b,l)}catch(d){if(d.name==="AbortError")return;y()}};t.addEventListener("input",()=>{let s=t.value.trim();if(u(),a&&clearTimeout(a),s.length<c){o&&o.abort(),p();return}a=setTimeout(()=>{T(s)},i)}),e&&e.addEventListener("click",()=>{o&&o.abort(),a&&clearTimeout(a),t.value="",p(),u(),t.focus()})}function v(t,r,e,n,c){if(!Array.isArray(r)||r.length===0){t.innerHTML="",c(!1);return}c(!0);let i=r.map(a=>`
        <tr>
            <td>${f(a.creator,e,n)}</td>
            <td>${f(a.title,e,n)}</td>
            <td>${f(a.collection,e,n)}</td>
            <td class="text-right">
                <span class="badge badge-secondary badge-sm font-heading font-bold">${n(a.type)}</span>
            </td>
        </tr>
    `).join("");t.innerHTML=`
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
                <tbody>${i}</tbody>
            </table>
        </div>
    `}function f(t,r,e){let n=String(t??""),c=String(r??"").trim();if(!c)return e(n);let i=c.replace(/[.*+?^${}()|[\]\\]/g,"\\$&"),a=new RegExp(`(${i})`,"gi");return n.split(a).map((l,u)=>u%2===1?`<mark class="bg-primary text-white px-1">${e(l)}</mark>`:e(l)).join("")}function g(){let t=document.querySelector("[data-js-search]");if(!t)return;let r=document.querySelectorAll("button[data-search-term]");r.length&&r.forEach(e=>{e.addEventListener("click",()=>{let n=e.dataset.searchTerm?.trim();n&&(t.value=n,t.dispatchEvent(new Event("input",{bubbles:!0})),t.focus())})})}m();g();})();
