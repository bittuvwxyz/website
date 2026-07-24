(function(){
  const nav=document.querySelector('.site-nav');
  const toggle=document.querySelector('.nav-toggle');
  if(toggle&&nav){toggle.addEventListener('click',function(){const open=nav.getAttribute('data-open')==='true';nav.setAttribute('data-open',open?'false':'true');toggle.setAttribute('aria-expanded',String(!open));});}
  document.querySelectorAll('form').forEach(function(form){
    form.addEventListener('submit',function(event){
      const password=form.querySelector('input[name="password"]');
      const confirm=form.querySelector('input[name="confirm_password"]');
      if(password&&password.value&&password.value.length<8){alert('Password must be at least 8 characters.');event.preventDefault();return;}
      if(password&&confirm&&password.value!==confirm.value){alert('Passwords do not match.');event.preventDefault();return;}
      const destructive=form.querySelector('.danger');
      if(destructive&&!confirm('Are you sure? This action cannot be undone.')){event.preventDefault();return;}
      const submit=form.querySelector('button[type="submit"],button:not([type])');
      if(submit){submit.classList.add('loading');submit.setAttribute('aria-busy','true');}
    });
  });
})();
