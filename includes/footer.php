</main></div><script>
document.querySelectorAll('[data-confirm]').forEach(x=>x.addEventListener('click',e=>{if(!confirm(x.dataset.confirm))e.preventDefault()}));
document.addEventListener('keydown',e=>{if(e.key==='F2'){e.preventDefault();location.href='?page=pdv'}});
const productModal=document.getElementById('productCreateModal');
if(productModal){
 const productForm=document.getElementById('productCreateForm'),productName=document.getElementById('productName'),productCost=document.getElementById('productCost'),productProfit=document.getElementById('productProfit'),productPrice=document.getElementById('productPrice');
 const parseProductValue=value=>{value=String(value||'').trim();if(value.includes(','))value=value.replace(/\./g,'').replace(',','.');return Number(value)||0;};
 const formatProductValue=value=>Number(value||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2});
 const calculateProductPrice=()=>{const cost=parseProductValue(productCost.value),profit=parseProductValue(productProfit.value);productPrice.value=cost>0?formatProductValue(cost*(1+profit/100)):'';};
 const calculateProductProfit=()=>{const cost=parseProductValue(productCost.value),price=parseProductValue(productPrice.value);if(cost>0&&price>=0)productProfit.value=formatProductValue(((price-cost)/cost)*100);};
 productCost.addEventListener('input',calculateProductPrice);productProfit.addEventListener('input',calculateProductPrice);productPrice.addEventListener('input',calculateProductProfit);
 const closeProductModal=()=>{productModal.hidden=true;productModal.setAttribute('aria-hidden','true');document.body.classList.remove('modal-open');};
 document.querySelectorAll('[data-product-open]').forEach(button=>button.addEventListener('click',()=>{
  productForm.reset();const code=productForm.querySelector('[name="codigo_barras"]');code.setCustomValidity('');if(code.nextElementSibling?.tagName==='SMALL')code.nextElementSibling.textContent='';productModal.hidden=false;productModal.setAttribute('aria-hidden','false');document.body.classList.add('modal-open');requestAnimationFrame(()=>productName.focus());
 }));
 productModal.querySelectorAll('[data-product-close]').forEach(button=>button.addEventListener('click',closeProductModal));
 productModal.addEventListener('click',event=>{if(event.target===productModal)closeProductModal();});
 document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!productModal.hidden)closeProductModal();});
 productForm.addEventListener('submit',event=>{const submit=event.currentTarget.querySelector('button:not([type])');submit.disabled=true;submit.textContent='Cadastrando...';});
}
const stockModal=document.getElementById('stockAdjustModal');
if(stockModal){
 const variationId=document.getElementById('stockVariationId'),productName=document.getElementById('stockProductName'),balance=document.getElementById('stockCurrentBalance'),quantity=document.getElementById('stockQuantity'),reason=document.getElementById('stockReason');
 const closeStockModal=()=>{stockModal.hidden=true;stockModal.setAttribute('aria-hidden','true');document.body.classList.remove('modal-open');};
 document.querySelectorAll('[data-stock-adjust]').forEach(button=>button.addEventListener('click',()=>{
  variationId.value=button.dataset.id;productName.textContent=button.dataset.product;balance.textContent=button.dataset.stock+' peças';quantity.value='';reason.value='';
  stockModal.hidden=false;stockModal.setAttribute('aria-hidden','false');document.body.classList.add('modal-open');requestAnimationFrame(()=>quantity.focus());
 }));
 stockModal.querySelectorAll('[data-modal-close]').forEach(button=>button.addEventListener('click',closeStockModal));
 stockModal.addEventListener('click',event=>{if(event.target===stockModal)closeStockModal();});
 document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!stockModal.hidden)closeStockModal();});
 document.getElementById('stockAdjustForm').addEventListener('submit',event=>{const submit=event.currentTarget.querySelector('button[type="submit"],button:not([type])');submit.disabled=true;submit.textContent='Salvando...';});
}
const barcode=document.querySelector('input[name="codigo_barras"]');
if(barcode){const note=document.createElement('small');barcode.insertAdjacentElement('afterend',note);let timer;barcode.addEventListener('input',()=>{clearTimeout(timer);note.textContent='';barcode.setCustomValidity('');if(!barcode.value.trim())return;timer=setTimeout(async()=>{try{const r=await fetch('api/barcode.php?code='+encodeURIComponent(barcode.value.trim()));const d=await r.json();if(d.exists){const i=d.item;note.textContent='Já cadastrado: '+i.nome+' / '+(i.cor||'-')+' / '+(i.tamanho||'-')+' — estoque '+i.estoque_atual;note.style.color='#b42318';barcode.setCustomValidity('Código já cadastrado');}else{note.textContent='Código disponível';note.style.color='#16875b';}}catch(e){}},350)});}
</script></body></html>
