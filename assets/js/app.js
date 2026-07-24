(function () {
  const nav = document.querySelector('.site-nav');
  const toggle = document.querySelector('.nav-toggle');

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      const isOpen = nav.getAttribute('data-open') === 'true';
      nav.setAttribute('data-open', isOpen ? 'false' : 'true');
      toggle.setAttribute('aria-expanded', String(!isOpen));
    });
  }

  document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      const password = form.querySelector('input[name="password"]');
      const confirmPassword = form.querySelector('input[name="confirm_password"]');
      const destructiveButton = form.querySelector('.danger');

      if (password && password.value && password.value.length < 8) {
        alert('Password must be at least 8 characters.');
        event.preventDefault();
        return;
      }

      if (password && confirmPassword && password.value !== confirmPassword.value) {
        alert('Passwords do not match.');
        event.preventDefault();
        return;
      }

      if (destructiveButton && !window.confirm('Are you sure? This action cannot be undone.')) {
        event.preventDefault();
        return;
      }

      const submitButton = form.querySelector('button[type="submit"], button:not([type])');
      if (submitButton) {
        submitButton.classList.add('loading');
        submitButton.setAttribute('aria-busy', 'true');
      }
    });
  });
})();
