import Vue from 'vue';

Vue.directive('copy-on-click', {
  bind: function (element, binding, vnode) {

    if (element.style)
      element.style.cursor = "pointer";
    if (!element.title)
      element.title = "Click to copy";

    element.addEventListener('click', function (event) {
      var s = JSON.stringify
      var str = binding.value;

      if (!str) str = this.textContent;               // Set its value to the string that you want copied

      const el = document.createElement('textarea');  // Create a <textarea> element
      el.value = str;                                 // Set its value to the string that you want copied
      el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
      el.style.position = 'absolute';
      el.style.left = '-9999px';                      // Move outside the screen to make it invisible
      document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
      const selected =
        document.getSelection().rangeCount > 0        // Check if there is any content selected previously
          ? document.getSelection().getRangeAt(0)     // Store selection if found
          : false;                                    // Mark as false to know no selection existed before
      el.select();                                    // Select the <textarea> content
      document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
      if (window.alertify) {
        window.alertify.success("Copied to clipboard");
      }
      document.body.removeChild(el);                  // Remove the <textarea> element
      if (selected) {                                 // If a selection existed before copying
        document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
        document.getSelection().addRange(selected);   // Restore the original selection
      }

      vnode.context.$store.dispatch('popup', { msg: 'copied', variant: 'dark' });
    });
  }
});

Vue.directive('open-in-viewer', {
  inserted: function (el, binding, vnode) {
    el.classList.add('c-ptr');
    let img = el.tagName !== 'IMG' ? el.querySelector('img') : el;
    el.addEventListener('click', function (e) {
      e.stopPropagation();
      e.preventDefault();
      vnode.context.$store.commit('SET_IMG_TO_VIEW', img.src);
    });
  }
});

Vue.directive('pop-up-on-click', {
  inserted: function (el, binding) {
    el.addEventListener('click', function () {
      new Vue().popupOnClick(binding.value, binding.value.duration || 1);
    });
  }
});

Vue.directive('t', {
  inserted: function (el, binding) {
    if (window.app) el.innerHTML = window.app.$t(binding.value);
  },
  update: function (el, binding) {
    if (window.app) el.innerHTML = window.app.$t(binding.value);
  },
});

Vue.directive('b-modal', {
  inserted: function (el, binding, vnode) {
    el.addEventListener('click', function () {      
      vnode.context.$store.dispatch('setModal', { ...binding.value, value: true });
    })
  },
});

Vue.directive('lock', {
  inserted: function (el, binding) {
    if (binding.value === undefined || binding.value) {
      var new_element = document.createElement('div'),
      overlay = document.createElement('div'),
      icon = document.createElement('i');
      // new_element
      new_element.className = el.className + ' border-0';
      el.childNodes.forEach(node => new_element.appendChild(node));
      // icon
      icon.className = 'fas fa-lock position-absolute position-center font-md';
      // overlay
      overlay.className = "bg-white-4 text-primary position-absolute position-top-left h-100 w-100";
      overlay.appendChild(icon);
      new_element.appendChild(overlay);
      new_element.style.position = "relative";
      el.parentNode.replaceChild(new_element, el);
    }
  }
});

// Vue.directive('badge', {
//   inserted: function (el, bindings) {
//     if (bindings.value) {
//       let badge = document.createElement('span');
//       badge.className = `badge badge-pill badge-${bindings.value.variant || 'warning'} badge-animation ml-2 font-300`;
//       badge.innerText = bindings.value.text || 'dev';
//       el.className += ' position-relative d-flex align-items-center';
//       el.appendChild(badge);
//     }
//   }
// });